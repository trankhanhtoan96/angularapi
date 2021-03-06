<?php

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\Logger\APILogEntryHandler;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\utils\SpiceUtils;

if (!defined('sugarEntry')) define('sugarEntry', true);


require('service/core/SugarSoapService.php');
require('vendor/nusoap/nusoap.php');

/**
 * This is an abstract class for the soap implementation for using NUSOAP. This class is responsible for making
 * all NUSOAP call by passing the client's request to NUSOAP server and seding response back to client
 * @api
 */
abstract class NusoapSoap extends SugarSoapService
{
    private $startingTime;
    private $logEntry;
    private $logging = false;

    /**
     * This is the constructor. It creates an instance of NUSOAP server.
     *
     * @param String $url - This is the soap URL
     * @access public
     */
    public function __construct($url)
    {
        LoggerManager::getLogger()->info('Begin: NusoapSoap->__construct');
        $this->server = new soap_server();
        $this->soapURL = $url;
        $this->server->configureWSDL('sugarsoap', $this->getNameSpace(), $url);
        if (!isset($GLOBALS['HTTP_RAW_POST_DATA'])) $GLOBALS['HTTP_RAW_POST_DATA'] = file_get_contents('php://input');
        parent::__construct();
        LoggerManager::getLogger()->info('End: NusoapSoap->__construct');
        // set a global transaction id
        $GLOBALS['transactionID'] = SpiceUtils::createGuid();
    } // ctor

    /**
     * Fallback function to catch unexpected failure in SOAP
     */
    public function shutdown()
    {
        if ($this->in_service) {
            $out = ob_get_contents();
            ob_end_clean();
            LoggerManager::getLogger()->info('NusoapSoap->shutdown: service died unexpectedly');
            $this->server->fault(-1, "Unknown error in SOAP call: service died unexpectedly", '', $out);
            $this->server->send_response();
            $this->generateLogEntry();
            $this->writeLog();
            $this->updateLogEntry();
        }
    }

    /**
     * It passes request data to NUSOAP server and sends response back to client
     * @access public
     */
    public function serve()
    {
        $this->startingTime = microtime(true);
        LoggerManager::getLogger()->info('Begin: NusoapSoap->serve');
        ob_clean();
        $this->in_service = true;
        register_shutdown_function([$this, "shutdown"]);
        ob_start();
        $this->server->service($GLOBALS['HTTP_RAW_POST_DATA']);
        $this->generateLogEntry();
        $this->writeLog();
        $this->updateLogEntry();
        $this->in_service = false;
        ob_end_flush();
        flush();
        LoggerManager::getLogger()->info('End: NusoapSoap->serve');
    } // fn

    /**
     * This method registers all the complex type with NUSOAP server so that proper WSDL can be generated
     *
     * @param String $name - name of complex type
     * @param String $typeClass - (complexType|simpleType|attribute)
     * @param String $phpType - array or struct
     * @param String $compositor - (all|sequence|choice)
     * @param String $restrictionBase - SOAP-ENC:Array or empty
     * @param Array $elements - array ( name => array(name=>'',type=>'') )
     * @param Array $attrs - array(array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'xsd:string[]'))
     * @param String $arrayType - arrayType: namespace:name (xsd:string)
     * @access public
     */
    public function registerType($name, $typeClass, $phpType, $compositor, $restrictionBase, $elements, $attrs = [], $arrayType = '')
    {
        $this->server->wsdl->addComplexType($name, $typeClass, $phpType, $compositor, $restrictionBase, $elements, $attrs, $arrayType);
    } // fn

    /**
     * This method registers all the functions you want to expose as services with NUSOAP
     *
     * @param String $function - name of the function
     * @param Array $input - assoc array of input values: key = param name, value = param type
     * @param Array $output - assoc array of output values: key = param name, value = param type
     * @access public
     */
    function registerFunction($function, $input, $output)
    {
        if (in_array($function, $this->excludeFunctions)) return;
        $use = false;
        $style = false;
        if (isset($_REQUEST['use']) && ($_REQUEST['use'] == 'literal')) {
            $use = "literal";
        } // if
        if (isset($_REQUEST['style']) && ($_REQUEST['style'] == 'document')) {
            $style = "document";
        } // if
        $this->server->register($function, $input, $output, $this->getNameSpace(), '', $style, $use);
    } // fn

    /**
     * This function registers implementation class name with NUSOAP so when NUSOAP makes a call to a funciton,
     * it will be made on this class object
     *
     * @param String $implementationClass
     * @access public
     */
    function registerImplClass($implementationClass)
    {
        LoggerManager::getLogger()->info('Begin: NusoapSoap->registerImplClass');
        if (empty($implementationClass)) {
            $implementationClass = $this->implementationClass;
        } // if
        $this->server->register_class($implementationClass);
        LoggerManager::getLogger()->info('End: NusoapSoap->registerImplClass');
    } // fn

    /**
     * Sets the name of the registry class
     *
     * @param String $registryClass
     * @access public
     */
    function registerClass($registryClass)
    {
        LoggerManager::getLogger()->info('Begin: NusoapSoap->registerClass');
        $this->registryClass = $registryClass;
        LoggerManager::getLogger()->info('End: NusoapSoap->registerClass');
    } // fn

    /**
     * This function sets the fault object on the NUSOAP
     *
     * @param SoapError $errorObject - This is an object of type SoapError
     * @access public
     */
    public function error($errorObject)
    {
        LoggerManager::getLogger()->info('Begin: NusoapSoap->error');
        $this->server->fault($errorObject->getFaultCode(), $errorObject->getName(), '', $errorObject->getDescription());
        LoggerManager::getLogger()->info('Begin: NusoapSoap->error');
    } // fn

    private function generateLogEntry()
    {
        $this->logEntry = new \stdClass();

        $this->logEntry->route = $this->server->methodname;
        $this->logEntry->method = 'SOAP';
        $this->logEntry->args = $this->server->methodparams;
        $this->logEntry->url = $this->server->SOAPAction;
        $this->logEntry->ip = $_SERVER['REMOTE_ADDR'];
        $this->logEntry->request_params = "";
        $this->logEntry->request_headers = json_encode($this->server->headers);
        $this->logEntry->request_body = $GLOBALS['HTTP_RAW_POST_DATA'];
        $this->logEntry->date_entered = gmdate('Y-m-d H:i:s');
        $this->logEntry->date_timestamp = APILogEntryHandler::getTimestamp();

        // $current_user is an empty beansobject if the current route doesn't need any authentication...
        $this->logEntry->user_id = "";
        // and session is also missing!
        $this->logEntry->session_id = session_id();
        $this->logEntry->transaction_id = $GLOBALS['transactionID'];
        $this->logEntry->direction = \SpiceCRM\includes\Middleware\LoggerMiddleware::DIRECTION_INBOUND;
    }

    private function writeLog()
    {
        $db = DBManagerFactory::getInstance('spicelogger');
        $this->logging = false;
        if ($db->tableExists('sysapilogconfig')) {
            // check if this request has to be logged by some rules...
            $sql = "SELECT COUNT(id) cnt FROM sysapilogconfig WHERE 
              (route = '{$this->logEntry->route}' OR route = '*' OR route LIKE '%{$this->logEntry->route}%') AND
              (method = '{$this->logEntry->method}' OR method = '*') AND
              (user_id = '{$this->logEntry->user_id}' OR user_id = '*') AND
              (ip = '{$this->logEntry->ip}' OR ip = '*') AND
              is_active = 1";
            $res = $db->query($sql);
            $row = $db->fetchByAssoc($res);
            if ($row['cnt'] > 0) {
                $this->logging = true;
                // write the log...
                $this->logEntry->id = SpiceUtils::createGuid();
                $db->insertQuery('sysapilog', (array)$this->logEntry);
            } else {
                $this->logging = false;
            }
        }
    }

    private function updateLogEntry()
    {
        if ($this->logging) {
            $this->logEntry->http_status_code = $this->extractHttpCode($this->server->outgoing_headers[0]);
            $this->logEntry->response_headers = $this->buildResponseHeader();
            $this->logEntry->runtime = (microtime(true) - $this->startingTime) * 1000;
            $this->logEntry->response_body = $this->server->responseSOAP;

            // update the log...
            $result = DBManagerFactory::getInstance('spicelogger')->updateQuery('sysapilog', ['id' => $this->logEntry->id], (array)$this->logEntry);
        }
    }

    /**
     * builds an associative array from the headers
     * @return false|string
     */
    private function buildResponseHeader()
    {
        $headers = [];
        foreach ($this->server->outgoing_headers as $header) {
            $h = explode(':', $header);
            $headers[$h[0]] = $h[1];
        }
        return json_encode($headers);
    }

    private function extractHttpCode($header): int
    {
        $headerParts = explode(' ', $header);
        return (int)$headerParts[1] ?: '200';
    }
} // clazz
