<?php


use SpiceCRM\modules\ACLActions\ACLAction;
use SpiceCRM\includes\SugarObjects\SpiceModules;

global $current_user; //todo-uebelmar check via regex
$actionarr = ACLAction::getDefaultActions();
if(is_admin($current_user)){
	$foundOne = false;
	foreach ($actionarr as $actionobj) {
	    $beanClass = SpiceModules::getInstance()->getBeanClassForModule($actionobj->category);
		if (empty(SpiceModules::getInstance()->getBeanName($actionobj->category)) || !class_exists($beanClass)) {
			if (!isset($_REQUEST['upgradeWizard'])) {
				echo 'Removing for ' . $actionobj->category . '<br>';
			}
			$foundOne = true;
			ACLAction::removeActions($actionobj->category);
		}
	}
	if (!$foundOne) {
        echo 'No ACL modules found that needed to be removed';
    }
}


?>
