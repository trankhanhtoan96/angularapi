<?php
namespace SpiceCRM\modules\Roles;

use SpiceCRM\data\SugarBean;
use SpiceCRM\includes\SugarObjects\SpiceModules;
use SpiceCRM\modules\Users\User;



/*********************************************************************************
 * @deprecated

 * Description:
 ********************************************************************************/

class Role extends SugarBean {

	var $field_name_map;
	
	var $id;
	var $deleted;
	var $date_entered;
	var $date_modified;
	var $modified_user_id;
	var $created_by;
	var $name;
	var $description;
	var $modules;
	var $disable_row_level_security = true;

	var $table_name = 'roles';
	var $rel_module_table = 'roles_modules';
	var $object_name = 'Role';
	var $module_dir = 'Roles';
	var $new_schema = true;

	function __construct()
	{
		parent::__construct();
	}
	

	function query_modules($allow = 1)
	{
		$query = "SELECT module_id FROM roles_modules WHERE ";
		$query .= "role_id = '$this->id' AND allow = '$allow' AND deleted=0";
		$result = $this->db->query($query);
		
		$return_array = [];
		
		while($row = $this->db->fetchByAssoc($result))
		{
			array_push($return_array, $row['module_id']);
		}
		
		return $return_array;
	}
	function set_module_relationship($role_id, &$mod_ids, $allow)
	{
		foreach($mod_ids as $mod_id)
		{
			if($mod_id != '')
				$this->set_relationship('roles_modules', ['module_id'=>$mod_id, 'role_id'=>$role_id, 'allow'=>$allow]);
		}
	}
	
	function clear_module_relationship($role_id)
	{
		$query = "DELETE FROM roles_modules WHERE role_id='$role_id'";
		$this->db->query($query);
	}

	function set_user_relationship($role_id, &$user_ids)
	{
		foreach($user_ids as $user_id)
		{
			if($user_id != '')
				$this->set_relationship('roles_users', ['user_id'=>$user_id, 'role_id'=>$role_id]);
		}
	}

	function clear_user_relationship($role_id, $user_id)
	{
		$query = "DELETE FROM roles_users WHERE role_id='$role_id' AND user_id='$user_id'";
		$this->db->query($query);
	}

	function query_user_allowed_modules($user_id)
	{
		$userArray = [];
		global $app_list_strings;
		
		
	
		$sql = "SELECT role_id FROM roles_users WHERE user_id='$user_id'";
		
		$result = $this->db->query($sql);
		
		while($row = $this->db->fetchByAssoc($result))
		{
			$role_id = $row["role_id"];
			$sql = "SELECT module_id FROM roles_modules WHERE role_id='$role_id' AND allow='1'";
			$res = $this->db->query($sql);
			
			while($col = $this->db->fetchByAssoc($res))
			{
				$key = $col['module_id'];
				if(!(array_key_exists($key, $userArray)))
				{
					$userArray[$key] = $app_list_strings['moduleList'][$key];
				}
			}
		}
	
		return $userArray;
	}
	
	function query_user_disallowed_modules($user_id, &$allowed)
	{
		$returnArray = [];
		
		foreach (SpiceModules::getInstance()->getModuleList() as $key => $val) {
			if(array_key_exists($val, $allowed))
				continue;
			$returnArray[$val] = $val;
		}
		
		return $returnArray;

	}

	function get_users()
	{
		// First, get the list of IDs.
		
		$query = "SELECT user_id as id FROM roles_users WHERE role_id='$this->id' AND deleted=0";
		
		return $this->build_related_list($query, new User());
	}

	function check_user_role_count($user_id)
	{
		$query =  "SELECT count(*) AS num FROM roles_users WHERE ";
		$query .= "user_id='$user_id' AND deleted=0";
		$result = $this->db->query($query);
		
		$row = $this->db->fetchByAssoc($result);
		
		return $row['num'];
	}		
		
}

?>
