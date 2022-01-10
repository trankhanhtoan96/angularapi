<?php


namespace SpiceCRM\includes\SpiceFTSManager\api\controllers;

use SpiceCRM\includes\SpiceFTSManager\SpiceFTSRESTManager;
use SpiceCRM\includes\SpiceFTSManager\SpiceFTSHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use SpiceCRM\includes\SpiceSlim\SpiceResponse as Response;

class FTSManagerController {
    /**
     * SpiceFTSRESTController constructor.
     * initialize ftsHandler
     */
/*    public function __construct()
    {
        $this->ftsManager = new SpiceFTSRESTManager;
    }*/

    /**
     * get modules list
     *
     * @param Request $req
     * @param Response $res
     * @param array $args
     * @return Response
     */
    public function getModules(Request $req, Response $res, array $args): Response {
        $spiceFtsRestManager = new SpiceFTSRESTManager();
        return $res->withJson($spiceFtsRestManager->getModules());
    }

    /**
     * get index
     *
     * @param Request $req
     * @param Response $res
     * @param array $args
     * @return Response
     */
    public function getIndex(Request $req, Response $res, array $args): Response {
        $this->ftsManager = new SpiceFTSRESTManager();
        return $res->withJson($this->ftsManager->getIndex());
    }

    /**
     * get nodes
     *
     * @param Request $req
     * @param Response $res
     * @param array $args
     * @return Response
     */
    public function getNodes(Request $req, Response $res, array $args): Response {
        $this->ftsManager = new SpiceFTSRESTManager();
        return $res->withJson($this->ftsManager->getNodes());
    }

    /**
     * get fields
     *
     * @param Request $req
     * @param Response $res
     * @param array $args
     * @return Response
     */
    public function getFields(Request $req, Response $res, array $args): Response {
        $this->ftsManager = new SpiceFTSRESTManager();
        $getParams = $req->getQueryParams();
        return $res->withJson($this->ftsManager->getFields($getParams['nodeid']));
    }

    /**
     * get analyzers
     *
     * @param Request $req
     * @param Response $res
     * @param array $args
     * @return Response
     */
    public function getAnalyzers(Request $req, Response $res, array $args): Response {
        $this->ftsManager = new SpiceFTSRESTManager();
        return $res->withJson($this->ftsManager->getAnalyzers());
    }

    /**
     * initialize (create all indexes)
     *
     * @param Request $req
     * @param Response $res
     * @param array $args
     * @return Response
     * @throws \SpiceCRM\includes\ErrorHandlers\ForbiddenException
     */
    public function initialize(Request $req, Response $res, array $args): Response {
        $this->ftsManager = new SpiceFTSRESTManager();
        return $res->withJson($this->ftsManager->initialize());
    }

    /**
     * @param Request $req
     * @param Response $res
     * @param array $args
     * @return Response
     * @throws \SpiceCRM\includes\ErrorHandlers\ForbiddenException
     */
    public function getFTSFields(Request $req, Response $res, array $args): Response {
        $this->ftsManager = new SpiceFTSRESTManager();
        return $res->withJson($this->ftsManager->getFTSFields($args['module']));
    }


    /**
     * get FTS index settings
     *
     * @param Request $req
     * @param Response $res
     * @param array $args
     * @return Response
     */
    public function getFTSSettings(Request $req, Response $res, array $args): Response
    {
        $this->ftsManager = new SpiceFTSRESTManager();
        return $res->withJson($this->ftsManager->getFTSSettings($args['module']));
    }

    /**
     * delete FTS index settings
     *
     * @param Request $req
     * @param Response $res
     * @param array $args
     * @return Response
     */
    public function deleteIndexSettings(Request $req, Response $res, array $args): Response {
        $this->ftsManager = new SpiceFTSRESTManager();
        return $res->withJson($this->ftsManager->deleteIndexSettings($args['module']));
    }

    /**
     * set FTS fields
     *
     * @param Request $req
     * @param Response $res
     * @param array $args
     * @return Response
     */
    public function setFTSFields(Request $req, Response $res, array $args): Response {
        $this->ftsManager = new SpiceFTSRESTManager();
        $items = $req->getParsedBody();

        // clear any session cached data for the module
        unset($_SESSION['SpiceFTS']['indexes'][$args['module']]);

        return $res->withJson($this->ftsManager->setFTSFields($args['module'], $items));
    }

    /**
     * index data
     *
     * @param Request $req
     * @param Response $res
     * @param array $args
     * @return Response
     */
    public function index(Request $req, Response $res, array $args): Response {
        $this->ftsManager = new SpiceFTSRESTManager();
        set_time_limit(300);


        $params = $req->getQueryParams();
        if (isset($params['resetIndexDates'])) {
            SpiceFTSHandler::getInstance()->resetIndexModule($args['module']);
        }

        if (isset($params['bulkAmount']) && $params['bulkAmount'] != 0) {
            ob_start();
            SpiceFTSHandler::getInstance()->indexModuleBulk($args['module'], $params['bulkAmount']); //CR1000257
            $message = ob_get_clean();
        }

        return $res->withJson(['status' => 'success', 'message' => $message]);
    }

    /**
     * @param Request $req
     * @param Response $res
     * @param array $args
     * @return Response
     */
    public function deleteIndex(Request $req, Response $res, array $args): Response {
        $this->ftsManager = new SpiceFTSRESTManager();
        return $res->withJson($this->ftsManager->deleteIndex($args['module']));
    }

    /**
     * @param Request $req
     * @param Response $res
     * @param array $args
     * @return Response
     */
    public function resetIndex(Request $req, Response $res, array $args): Response {
        $this->ftsManager = new SpiceFTSRESTManager();

        // delete and recreate the index
        $this->ftsManager->deleteIndex($args['module']);
        $mapResults =  $this->ftsManager->mapModule($args['module']);

        if(!$mapResults->acknowledged){
            return $res->withJson([
                'status' => 'error',
                'type' => $mapResults->error->type,
                'message' => $mapResults->error->reason,
            ]);
        }

        // index the beans
        SpiceFTSHandler::getInstance()->resetIndexModule($args['module']);

        return $res->withJson(['status' => 'success']);
    }
}
