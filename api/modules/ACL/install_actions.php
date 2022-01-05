<?php


use SpiceCRM\data\BeanFactory;
use SpiceCRM\includes\Logger\LoggerManager;
use SpiceCRM\includes\SugarObjects\SpiceModules;
use SpiceCRM\includes\utils\SpiceUtils;
use SpiceCRM\modules\ACLActions\ACLAction;

global $current_user, $mod_strings;

$installed_classes = [];
$ACLbeanList = SpiceModules::getInstance()->getBeanList();

if (SpiceUtils::isAdmin($current_user)) {
    foreach ($ACLbeanList as $module => $beanName) {
        $beanClass = SpiceModules::getInstance()->getBeanClassForModule($module);

        if (empty($installed_classes[$beanName]) && class_exists($beanClass)) {
            if ($beanName != 'Tracker') {
                $mod = BeanFactory::getBean($module);
                LoggerManager::getLogger()->debug("DOING: $beanName");
                if ($mod->bean_implements('ACL') && empty($mod->acl_display_only)) {
                    // BUG 10339: do not display messages for upgrade wizard
                    if (!isset($_REQUEST['upgradeWizard'])) {
                        echo SpiceUtils::translate('LBL_ADDING','ACL','') . $mod->module_dir . '<br>';
                    }
                    if (!empty($mod->acltype)) {
                        ACLAction::addActions($mod->getACLCategory(), $mod->acltype);
                    } else {
                        ACLAction::addActions($mod->getACLCategory());
                    }

                    $installed_classes[$beanName] = true;
                }
            }
        }
    }


}
