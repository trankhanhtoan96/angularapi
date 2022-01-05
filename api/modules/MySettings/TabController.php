<?php



use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\authentication\AuthenticationController;
use SpiceCRM\modules\SpiceACL\SpiceACL;

class TabController
{
    /**
     * @var bool flag of validation of the cache
     */
    static protected $isCacheValid = false;

    private function get_system_tabs() {
        global $moduleList;

        static $system_tabs_result = null;

        // if the value is not already cached, then retrieve it.
        if (empty($system_tabs_result) || !self::$isCacheValid) {

            $administration = BeanFactory::getBean('Administration');
            $administration->retrieveSettings('MySettings');
            if (isset($administration->settings) && isset($administration->settings['MySettings_tab'])) {
                $tabs = $administration->settings['MySettings_tab'];
                $trimmed_tabs = trim($tabs);
                //make sure serialized string is not empty
                if (!empty($trimmed_tabs)) {
                    $tabs = base64_decode($tabs);
                    $tabs = unserialize($tabs);
                    //Ensure modules saved in the prefences exist.
                    foreach ($tabs as $id => $tab) {
                        if (is_array($moduleList) && !in_array($tab, $moduleList))
                            unset($tabs[$id]);
                    }
                    SpiceACL::getInstance()->filterModuleList($tabs);
                    $tabs = $this->get_key_array($tabs);
                    $system_tabs_result = $tabs;
                } else {
                    $system_tabs_result = $this->get_key_array($moduleList);
                }
            } else {
                $system_tabs_result = $this->get_key_array($moduleList);
            }
            self::$isCacheValid = true;
        }

        return $system_tabs_result;
    }

    public function get_tabs_system() {
        global $moduleList;
        $tabs = $this->get_system_tabs();
        $unsetTabs = $this->get_key_array($moduleList);
        foreach ($tabs as $tab) {
            unset($unsetTabs[$tab]);
        }

        $should_hide_iframes = !file_exists('modules/iFrames/iFrame.php');
        if ($should_hide_iframes) {
            if (isset($unsetTabs['iFrames'])) {
                unset($unsetTabs['iFrames']);
            } else if (isset($tabs['iFrames'])) {
                unset($tabs['iFrames']);
            }
        }

        return [$tabs, $unsetTabs];
    }

    private function get_key_array($arr) {
        $new = [];
        if (!empty($arr)) {
            foreach ($arr as $val) {
                $new[$val] = $val;
            }
        }
        return $new;
    }
}
