<?php
namespace SpiceCRM\modules\OutputTemplates;

use SpiceCRM\data\BeanFactory;
use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\ErrorHandlers\Exception;
use SpiceCRM\includes\SpiceAttachments\SpiceAttachments;
use SpiceCRM\includes\SpiceTemplateCompiler\Compiler;
use SpiceCRM\includes\SugarObjects\SpiceConfig;



/*********************************************************************************
 * Description:  represents a template to output something to a .pdf or like that...
 * Contributor(s): Sebastian Franz
 ********************************************************************************/

class OutputTemplate extends SugarBean
{
    var $table_name = "outputtemplates";
    var $object_name = "OutputTemplate";
    var $module_dir = "OutputTemplates";
    var $new_schema = true;
    // fields which holds options to create pdfs
    public static $PDF_OPTION_FIELDS = [
        'page_size',
        'page_orientation',
        'margin_left',
        'margin_top',
        'margin_right',
        'margin_bottom'
    ];

    /**
     * the loaded PDF handler Class
     *
     * @var
     */
    private $pdf_handler;

    /**
     * hold potential additonal values
     *
     * @var array
     */
    private $additonalValues = [];

    /**
     * an be calleed to set an array or object with different values to be
     *
     * @param $additonalValues an stdclass object
     */
    public function setAdditonalValues($additonalValues){
        $this->additonalValues = $additonalValues;
    }

    /**
     * List of IDs of possible parent templates (to prevent recursions).
     * @var
     */
    public $idsOfParentTemplates = [];

    public function translateBody($bean = null, $bodyOnly = false)
    {
        if(!$bean)
        {
            if(!$this->bean)
                $this->retrieveBean();

            $bean = $this->bean;
        }
        if(!$bean)
            throw new Exception("No Bean found, translation aborted!");

        $templateCompiler = new Compiler($this);
        $templateCompiler->idsOfParentTemplates = array_merge( $this->idsOfParentTemplates, [$this->id] );
        if ($bodyOnly) {
            $html = $templateCompiler->compile(html_entity_decode( $this->body), $bean, $this->language, $this->additonalValues);
        } else {
            $html = '<style>' . $this->getStyle() . '</style>' . $templateCompiler->compile('<body><header>'
                    .html_entity_decode( $this->header ).'</header><footer>'.html_entity_decode( $this->footer ).'</footer><main>'.html_entity_decode( $this->body ).'</main></body>', $bean, $this->language, $this->additonalValues);
        }

        return $html;
    }

    public function __toString()
    {
        return $this->translateBody();
    }

    private function setPDFHandler(){
        $class = @SpiceConfig::getInstance()->config['outputtemplates']['pdf_handler_class'];
        if(!$class) $class = '\SpiceCRM\modules\OutputTemplates\handlers\pdf\DomPdfHandler';
        $this->pdf_handler = new $class($this);
    }

    public function download()
    {
        $this->setPDFHandler();
        return $this->pdf_handler->toDownload();
    }

    private function saveAsTmpFile($filename = null)
    {
        $this->setPDFHandler();
        return $this->pdf_handler->toTempFile($filename);
    }

    public function getFileName()
    {
        return "{$this->module_name}_{$this->name}.pdf";
    }

    public function getPdfContent()
    {
        $this->setPDFHandler();
        return $this->pdf_handler->__toString();
    }

    public function convertToSpiceAttatchment()
    {
        $file = $this->saveAsTmpFile();
        return SpiceAttachments::saveAttachmentLocalFile($this->module_name, $this->bean_id, $file);
    }

    private function retrieveBean()
    {
        if ($this->bean_id) {
            $this->bean = BeanFactory::getBean($this->module_name, $this->bean_id);
        }

        return $this->bean;
    }

    public function getStyle()
    {
        $style = '';
        if (!empty($this->stylesheet_id)) {
            $styleRecord = $this->db->fetchByAssoc($this->db->query("SELECT csscode FROM sysuihtmlstylesheets WHERE id='{$this->stylesheet_id}'"));
            $style = html_entity_decode($styleRecord['csscode'], ENT_QUOTES);
        }
        return str_replace(["\n", "\t"], "", $style);
    }

}

