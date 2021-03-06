<?php

namespace SpiceCRM\modules\ACLRoles;

use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\SugarCache\SugarCache;
use SpiceCRM\includes\SugarObjects\SpiceModules;
use SpiceCRM\includes\TimeDate;
use SpiceCRM\modules\ACLActions\ACLAction;

class ACLRole extends SugarBean{
    var $module_dir = 'ACLRoles';
    var $object_name = 'ACLRole';
    var $table_name = 'acl_roles';
    var $new_schema = true;
    var $disable_row_level_security = true;
    var $relationship_fields = [
                                    'user_id'=>'users'
    ];

    var $created_by;

    function __construct(){
        parent::__construct();
    }

    // bug 16790 - missing get_summary_text method led Tracker to display SugarBean's "base implementation"
    function get_summary_text()
    {
        return "$this->name";
    }


/**
 * function setAction($role_id, $action_id, $access)
 *
 * Sets the relationship between a role and an action and sets the access level of that relationship
 *
 * @param GUID $role_id - the role id
 * @param GUID $action_id - the ACL Action id
 * @param int $access - the access level ACL_ALLOW_ALL ACL_ALLOW_NONE ACL_ALLOW_OWNER...
 */
function setAction($role_id, $action_id, $access){
    $relationship_data = ['role_id'=>$role_id, 'action_id'=>$action_id,];
    $additional_data = ['access_override'=>$access];
    $this->set_relationship('acl_roles_actions',$relationship_data,true, true,$additional_data);
}


/**
 * static  getUserRoles($user_id)
 * returns a list of ACLRoles for a given user id
 *
 * @param GUID $user_id
 * @return a list of ACLRole objects
 */
function getUserRoles($user_id, $getAsNameArray = true){

        //if we don't have it loaded then lets check against the db
        $additional_where = '';
        $query = "SELECT acl_roles.* ".
            "FROM acl_roles ".
            "INNER JOIN acl_roles_users ON acl_roles_users.user_id = '$user_id' ".
                "AND acl_roles_users.role_id = acl_roles.id AND acl_roles_users.deleted = 0 ".
            "WHERE acl_roles.deleted=0 ";

        $result = DBManagerFactory::getInstance()->query($query);
        $user_roles = [];

        while($row = DBManagerFactory::getInstance()->fetchByAssoc($result) ){
            $role = new ACLRole();
            $role->populateFromRow($row);
            if($getAsNameArray)
                $user_roles[] = $role->name;
            else
                $user_roles[] = $role;
        }

        return $user_roles;
}

/**
 * static  getUserRoleNames($user_id)
 * returns a list of Role names for a given user id
 *
 * @param GUID $user_id
 * @return a list of ACLRole Names
 */
function getUserRoleNames($user_id){

        $user_roles = SugarCache::sugar_cache_retrieve("RoleMembershipNames_".$user_id);

        if(!$user_roles){
            //if we don't have it loaded then lets check against the db
            $additional_where = '';
            $query = "SELECT acl_roles.* ".
                "FROM acl_roles ".
                "INNER JOIN acl_roles_users ON acl_roles_users.user_id = '$user_id' ".
                    "AND acl_roles_users.role_id = acl_roles.id AND acl_roles_users.deleted = 0 ".
                "WHERE acl_roles.deleted=0 ";

            $result = DBManagerFactory::getInstance()->query($query);
            $user_roles = [];

            while($row = DBManagerFactory::getInstance()->fetchByAssoc($result) ){
                $user_roles[] = $row['name'];
            }

            SugarCache::sugar_cache_put("RoleMembershipNames_".$user_id, $user_roles);
        }

        return $user_roles;
}


/**
 * static getAllRoles($returnAsArray = false)
 *
 * @param boolean $returnAsArray - should it return the results as an array of arrays or as an array of ACLRoles
 * @return either an array of array representations of acl roles or an array of ACLRoles
 */
function getAllRoles($returnAsArray = false){
        $db = DBManagerFactory::getInstance();
        $query = "SELECT acl_roles.* FROM acl_roles
                    WHERE acl_roles.deleted=0 ORDER BY name";

        $result = $db->query($query);
        $roles = [];

        while($row = $db->fetchByAssoc($result) ){
            $role = new ACLRole();
            $role->populateFromRow($row);
            if($returnAsArray){
                $roles[] = $role->toArray();
            }else{
                $roles[] = $role;
            }

        }
        return $roles;


}

/**
 * static getRoleActions($role_id)
 *
 * gets the actions of a given role
 *
 * @param GUID $role_id
 * @return array of actions
 */
function getRoleActions($role_id, $type='module'){
        //if we don't have it loaded then lets check against the db
        $additional_where = '';
        $db = DBManagerFactory::getInstance();
        $query = "SELECT acl_actions.*";
        //only if we have a role id do we need to join the table otherwise lets use the ones defined in acl_actions as the defaults
        if(!empty($role_id)){
                $query .=" ,acl_roles_actions.access_override ";
        }
        $query .=" FROM acl_actions ";

        if(!empty($role_id)){
            $query .=		" LEFT JOIN acl_roles_actions ON acl_roles_actions.role_id = '$role_id' AND  acl_roles_actions.action_id = acl_actions.id AND acl_roles_actions.deleted = 0";
        }
        $query .= " WHERE acl_actions.deleted=0 ORDER BY acl_actions.category, acl_actions.name";
        $result = $db->query($query);
        $role_actions = [];

        while($row = $db->fetchByAssoc($result) ){
            $action = new ACLAction();
            $action->populateFromRow($row);
            if(!empty($row['access_override'])){
                $action->aclaccess = $row['access_override'];
            }else{
                $action->aclaccess = ACL_ALLOW_DEFAULT;

            }
            //#27877 . If  there is no this module in beanlist , we will not show them in UI, no matter this module was deleted or not in ACL_ACTIONS table.
            if (empty(SpiceModules::getInstance()->getBeanName($action->category))) {
                continue;
            }
            //end

            if(!isset($role_actions[$action->category])){
                $role_actions[$action->category] = [];
            }

            $role_actions[$action->category][$action->acltype][$action->name] = $action->toArray();


        }
        
        // Sort by translated categories
        uksort($role_actions, "ACLRole::langCompare");
        return $role_actions;

    }
    
    private static function langCompare($a, $b) 
    {
        global $app_list_strings;
        // Fallback to array key if translation is empty
        $a = empty($app_list_strings['moduleList'][$a]) ? $a : $app_list_strings['moduleList'][$a];
        $b = empty($app_list_strings['moduleList'][$b]) ? $b : $app_list_strings['moduleList'][$b];
        if ($a == $b)
            return 0;
        return ($a < $b) ? -1 : 1;
    }
/**
 * function mark_relationships_deleted($id)
 *
 * special case to delete acl_roles_actions relationship
 *
 * @param ACLRole GUID $id
 */
function mark_relationships_deleted($id){
        //we need to delete the actions relationship by hand (special case)
        $date_modified = DBManagerFactory::getInstance()->convert("'". TimeDate::getInstance()->nowDb()."'", 'datetime');
        $query =  "UPDATE acl_roles_actions SET deleted=1 , date_modified=$date_modified WHERE role_id = '$id' AND deleted=0";
        $this->db->query($query);
        parent::mark_relationships_deleted($id);
}

    /**
    *  toArray()
    * returns this role as an array
    *
    * @return array of fields with id, name, description
    */
    public function toArray($dbOnly = false, $stringOnly = false, $upperKeys = false){
        $array_fields = ['id', 'name', 'description'];
        $arr = [];
        foreach($array_fields as $field){
            if(isset($this->$field)){
                $arr[$field] = $this->$field;
            }else{
                $arr[$field] = '';
            }
        }
        return $arr;
    }

    /**
    * fromArray($arr)
    * converts an array into an role mapping name value pairs into files
    *
    * @param Array $arr
    */
    function fromArray($arr){
        foreach($arr as $name=>$value){
            $this->$name = $value;
        }
    }
}

?>
