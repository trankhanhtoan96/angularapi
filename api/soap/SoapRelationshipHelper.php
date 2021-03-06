<?php

use SpiceCRM\includes\database\DBManagerFactory;
use SpiceCRM\includes\authentication\AuthenticationController;

require_once('soap/SoapError.php');

function check_for_relationship($relationships, $module){
	foreach($relationships as $table=>$rel){
		if( $rel['rhs_key'] == $module){
			return $table;

		}
	}
	return false;
}

/*
 * takes in two modules and returns the relationship information about them
 *
 */

function retrieve_relationships_properties($module_1, $module_2, $relationship_name = ""){

	$rs = new Relationship();
	$query =  "SELECT * FROM $rs->table_name WHERE ((lhs_module = '".$rs->db->quote($module_1)."' AND rhs_module='".$rs->db->quote($module_2)."') OR (lhs_module = '".$rs->db->quote($module_2)."' AND rhs_module='".$rs->db->quote($module_1)."'))";
	if(!empty($relationship_name) && isset($relationship_name)){
		$query .= " AND relationship_name = '".$rs->db->quote($relationship_name)."'";
	}
	$result = $rs->db->query($query);

	return $rs->db->fetchByAssoc($result);
}




/*
 * retireves relationships between two modules
 * This will return all viewable relationships between two modules
 * module_query is a filter on the first module
 * related_module_query is a filter on the second module
 * relationship_query is a filter on the relationship between them
 * show_deleted is if deleted items should be shown or not
 *
 */
function retrieve_relationships($module_name,  $related_module, $relationship_query, $show_deleted, $offset, $max_results){
	global $beanList, $beanFiles, $dictionary;
$current_user = AuthenticationController::getInstance()->getCurrentUser();

	$error = new SoapError();
	$result_list = [];
	if(empty($beanList[$module_name]) || empty($beanList[$related_module])){

		$error->set_error('no_module');
		return ['result'=>$result_list, 'error'=>$error->get_soap_array()];
	}

	$result = retrieve_relationship_query($module_name,  $related_module, $relationship_query, $show_deleted, $offset, $max_results);

	if(empty($result['module_1'])){

		$error->set_error('no_relationship_support');
		return ['result'=>$result_list, 'error'=>$error->get_soap_array()];
	}
	$query = $result['query'];
	$module_1 = $result['module_1'];
	$table = $result['join_table'];

	$class_name = $beanList[$module_1];
	require_once($beanFiles[$class_name]);
	$mod = new $class_name();

	$count_query = str_replace('rt.*', 'count(*)', $query);
	$result = $mod->db->query($count_query);
	$row = $mod->db->fetchByAssoc($result);
	$total_count = $row['count(*)'];

	if($max_results != '-99'){
		$result = $mod->db->limitQuery($query, $offset, $max_results);
	}else{
		$result = $mod->db->query($query);
	}
	while($row = $mod->db->fetchByAssoc($result)){

		$result_list[] = $row;
	}

	return ['table_name'=>$table, 'result'=>$result_list, 'total_count'=>$total_count, 'error'=>$error->get_soap_array()];
}

/*
 * retrieve_modified_relationships
 *
 * This method retrieves modified relationships between two modules
 * This will return all viewable relationships between two modules
 *
 * @param $module_name String value of the module on the left hand side of relationship
 * @param related_module String value of the module on the right hand side of relationship
 * @param relationship_query SQL String used to query for the relationships
 * @show_deleted boolean value indicating whether or not deleted items should be shown (IGNORED)
 * @offset integer value indicating the starting offset of results to return
 * @max_results integer value indicating the maximum number of results to return
 * @select_fields Mixed Array indicating the select fields used in the query to return in results
 * @relationship_name String value of the relationship name as defined in the relationships table to be used in retrieving the relationship information
 * @return Mixed Array of results with the following delta/value information:
 *         table_name String value of the table name queried for the results
 *         result Mixed Array of the results.  Each entry in the Array contains an Array of key/value pairs from the select_fields parameter
 *         total_count integer value indicating the total count of results from the query
 *         error Mixed Array containing the SOAP errors if found, empty otherwise
 *
 */
function retrieve_modified_relationships($module_name, $related_module, $relationship_query, $show_deleted, $offset, $max_results, $select_fields = [], $relationship_name = ''){

    global $beanList, $beanFiles, $dictionary;
$current_user = AuthenticationController::getInstance()->getCurrentUser();
	$error = new SoapError();
	$result_list = [];
	if(empty($beanList[$module_name]) || empty($beanList[$related_module])){

		$error->set_error('no_module');
		return ['result'=>$result_list, 'error'=>$error->get_soap_array()];
	}

	$row = retrieve_relationships_properties($module_name, $related_module, $relationship_name);

	if(empty($row)){

		$error->set_error('no_relationship_support');
		return ['result'=>$result_list, 'error'=>$error->get_soap_array()];
	}

	$table = $row['join_table'];
	$has_join = true;
	if(empty($table)){
		//return array('table_name'=>$table, 'result'=>$result_list, 'error'=>$error->get_soap_array());
		$table = $row['rhs_table'];
		$module_1 = $row['lhs_module'];
		$mod_key = $row['lhs_key'];
		$module_2 = $row['rhs_module'];
		$mod2_key = $row['rhs_key'];
		$has_join = false;
	}
	else{
		$module_1 = $row['lhs_module'];
		$mod_key = $row['join_key_lhs'];
		$module_2 = $row['rhs_module'];
		$mod2_key = $row['join_key_rhs'];
	}



	$class_name = $beanList[$module_1];
	require_once($beanFiles[$class_name]);
	$mod = new $class_name();

	$mod2_name = $beanList[$module_2];
	require_once($beanFiles[$mod2_name]);
	$mod2 = new $mod2_name();
	$table_alias = 'rt';
	if($has_join == false){
		$table_alias = 'm1';
	}

	if(isset($select_fields) && !empty($select_fields)){
		$index = 0;
		$field_select ='';

		foreach($select_fields as $field){
			if($field == "id"){
				$field_select .= "DISTINCT m1.id";
			} else {
			    $parts = explode(' ', $field);
			    $alias = '';
			    if(count($parts) > 1) {
			        // have aliases: something like "blah.blah blah"
                    $alias = array_pop($parts);
                    $field = array_pop($parts); // will check for . further down
			    }
			    if($alias == "email1") {
                    // special case for primary emails
                    $field_select .= "(SELECT email_addresses.email_address FROM {$mod->table_name}
                    	LEFT JOIN  email_addr_bean_rel ON {$mod->table_name}.id = email_addr_bean_rel.bean_id
                    		AND email_addr_bean_rel.bean_module='{$mod->module_dir}'
                    		AND email_addr_bean_rel.deleted=0 AND email_addr_bean_rel.primary_address=1
                    	LEFT JOIN email_addresses ON email_addresses.id = email_addr_bean_rel.email_address_id Where {$mod->table_name}.id = m1.ID) email1";
			    } elseif($alias == "email2") {
                    // special case for non-primary emails
                    // FIXME: This is not a DB-safe code. Does not work on SQL Server & Oracle.
                    // Using dirty hack here.
                    $field_select .= "(SELECT email_addresses.email_address FROM {$mod->table_name}
                    	LEFT JOIN  email_addr_bean_rel on {$mod->table_name}.id = email_addr_bean_rel.bean_id
                    		AND email_addr_bean_rel.bean_module='{$mod->module_dir}' AND email_addr_bean_rel.deleted=0
                    		AND email_addr_bean_rel.primary_address!=1
                    	LEFT JOIN email_addresses ON email_addresses.id = email_addr_bean_rel.email_address_id Where {$mod->table_name}.id = m1.ID limit 1) email2";
			    } else {
                    if(strpos($field, ".") == false) {
                        // no dot - field for m1
                        $fieldname = "m1.".$mod->db->getValidDBName($field);
                    } else {
                        // There is a dot in here somewhere.
                        list($table_part,$field_part) = explode('.',$field);
                        $fieldname = $mod->db->getValidDBName($table_part).".".$mod->db->getValidDBName($field_part);
    			    }
    			    $field_select .= $fieldname;
    			    if(!empty($alias)) {
    			        $field_select .= " ".$mod->db->getValidDBName($alias);
    			    }
			    }
			}
			if($index < (count($select_fields) - 1))
			{
				$field_select .= ",";
				$index++;
			}
		}//end foreach
		$query = "SELECT $field_select FROM $table $table_alias ";
	}
	else{
		$query = "SELECT rt.* FROM  $table $table_alias ";
	}

	if($has_join == false){
		$query .= " inner join $mod->table_name m2 on $table_alias.$mod2_key = m2.id AND m2.id = '$current_user->id'";
	}
	else{
		$query .= " inner join $mod->table_name m1 on rt.$mod_key = m1.id ";
		$query .= " inner join $mod2->table_name m2 on rt.$mod2_key = m2.id AND m2.id = '$current_user->id'";
	}

	if(!empty($relationship_query)){
		$query .= ' WHERE ' . string_format($relationship_query, [$table_alias]);
	}

	if($max_results != '-99'){
		$result = $mod->db->limitQuery($query, $offset, $max_results);
	}else{
		$result = $mod->db->query($query);
	}
	while($row = $mod->db->fetchByAssoc($result)){
		$result_list[] = $row;
	}

    $total_count = !empty($result_list) ? count($result_list) : 0;
	return ['table_name'=>$table, 'result'=>$result_list, 'total_count'=>$total_count, 'error'=>$error->get_soap_array()];
}

function server_save_relationships($list, $from_date, $to_date){
	require_once('include/utils/db_utils.php');
	global $beanList, $beanFiles;
	$from_date = DBManagerFactory::getInstance()->convert("'". DBManagerFactory::getInstance()->quote($from_date)."'", 'datetime');
	$to_date = DBManagerFactory::getInstance()->convert("'". DBManagerFactory::getInstance()->quote($to_date)."'", 'datetime');

	$db = DBManagerFactory::getInstance();

	$ids = [];
	$add = 0;
	$modify = 0;
	$deleted = 0;

	foreach($list as $record)
	{
		$insert = '';
		$insert_values = '';
		$update = '';
		$select_values	= '';
		$args = [];

		$id = $record['id'];

		$table_name = $record['module_name'];
		$resolve = 1;

		foreach($record['name_value_list'] as $name_value){
			$name = DBManagerFactory::getInstance()->quote($name_value['name']);

			if($name == 'date_modified'){
                $value = $to_date;
			}else{
                $value = DBManagerFactory::getInstance()->convert("'". DBManagerFactory::getInstance()->quote($name_value['value'])."'", 'varchar');
			}
			if($name != 'resolve'){
			if(empty($insert)){
				$insert = '('	.$name;
				$insert_values = '('	.$value;
				if($name != 'date_modified' && $name != 'id' ){
					$select_values = $name ."=$value";
				}
				if($name != 'id'){
					$update = $name ."=$value";
				}
			}else{
				$insert .= ', '	.$name;
				$insert_values .= ', '	.$value;
				if(empty($update)){
					$update .= $name."=$value";
				}else{
					$update .= ','.$name."=$value";
				}

				if($name != 'date_modified' && $name != 'id' ){
					if(empty($select_values)){
						$select_values = $name ."=$value";
					}else{
						$select_values .= ' AND '.$name ."=$value";
					}
				}
			}
			}else{
				$resolve = $value;
			}




		}
		//ignore resolve for now server always wins
		$resolve = 1;
		$insert = "INSERT INTO $table_name $insert) VALUES $insert_values)";
		$update = "UPDATE $table_name SET $update WHERE id=";
		$delete = "DELETE FROM $table_name WHERE id=";
		$select_by_id_date = "SELECT id FROM $table_name WHERE id ='". DBManagerFactory::getInstance()->quote($id)."' AND date_modified > $from_date AND date_modified<= $to_date";
		$select_by_id = "SELECT id FROM $table_name WHERE id ='". DBManagerFactory::getInstance()->quote($id)."'";
		$select_by_values = "SELECT id FROM $table_name WHERE $select_values";
		$updated = false;


		$result = $db->query($select_by_id_date);
		//see if we have a matching id in the date_range
		if(!($row = $db->fetchByAssoc($result))){
			//if not lets check if we have one that matches the values

			$result = $db->query($select_by_values);
			if(!($row = $db->fetchByAssoc($result))){

				$result = $db->query($select_by_id);
				if($row = $db->fetchByAssoc($result)){

					$db->query($update ."'". DBManagerFactory::getInstance()->quote($row['id'])."'" );
					$ids[] = $row['id'];
					$modify++;
				}else{
					$db->query($insert);
					$add++;
					$ids[] = $row['id'];
				}
			}
	}

	}
	return ['add'=>$add, 'modify'=>$modify, 'ids'=>$ids];
}

/*
 *
 * gets the from statement from a query without the order by and without the select
 *
 */
function get_from_statement($query){
	$query = explode('FROM', $query);
	if(sizeof($query) == 1){
		$query = explode('from', $query[0]);
	}
	$query = explode( 'ORDER BY',$query[1]);

	return ' FROM ' . $query[0];

}

function retrieve_relationship_query($module_name,  $related_module, $relationship_query, $show_deleted, $offset, $max_results){
	global $beanList, $beanFiles, $dictionary;
$current_user = AuthenticationController::getInstance()->getCurrentUser();
	$error = new SoapError();
	$result_list = [];
	if(empty($beanList[$module_name]) || empty($beanList[$related_module])){

		$error->set_error('no_module');
		return ['query' =>"", 'module_1'=>"", 'join_table' =>"", 'error'=>$error->get_soap_array()];
	}

	$row = retrieve_relationships_properties($module_name, $related_module);
	if(empty($row)){

		$error->set_error('no_relationship_support');
		return ['query' =>"", 'module_1'=>"", 'join_table' =>"", 'error'=>$error->get_soap_array()];
	}

	$module_1 = $row['lhs_module'];
	$mod_key = $row['join_key_lhs'];
	$module_2 = $row['rhs_module'];
	$mod2_key = $row['join_key_rhs'];

	$table = $row['join_table'];
	if(empty($table)){
		return ['query' =>"", 'module_1'=>"", 'join_table' =>"", 'error'=>$error->get_soap_array()];
	}
	$class_name = $beanList[$module_1];
	require_once($beanFiles[$class_name]);
	$mod = new $class_name();

	$mod2_name = $beanList[$module_2];
	require_once($beanFiles[$mod2_name]);
	$mod2 = new $mod2_name();
	$query = "SELECT rt.* FROM  $table rt ";
	$query .= " inner join $mod->table_name m1 on rt.$mod_key = m1.id ";
	$query .= " inner join $mod2->table_name m2 on rt.$mod2_key = m2.id  ";


	if(!empty($relationship_query)){
		$query .= ' WHERE ' . $relationship_query;
	}

	return ['query' =>$query, 'module_1'=>$module_1, 'join_table' => $table, 'error'=>$error->get_soap_array()];
}

// Returns name of 'link' field between two given modules
function get_module_link_field($module_1, $module_2) {
	global $beanList, $beanFiles;

	// check to make sure both modules exist
	if (empty($beanList[$module_1]) || empty($beanList[$module_2])) {
		return FALSE;
	}

	$class_1 = $beanList[$module_1];
	require_once($beanFiles[$class_1]);

	$obj_1 = new $class_1();

	// loop through link fields of $module_1, checking for a link to $module_2
	foreach ($obj_1->get_linked_fields() as $linked_field) {
		$obj_1->load_relationship($linked_field['name']);
		$field = $linked_field['name'];

		if (empty($obj_1->$field)) {
			continue;
		}

		if ($obj_1->$field->getRelatedModuleName() == $module_2) {
			return $field;
		}
	}

	return FALSE;
}

// Retrieves array of ids for records of $get_module linked to $from_module by $get_id
// Example: to retrieve list of Contacts associated to Account X: $return = get_linked_records("Contacts", "Accounts", "X");
function get_linked_records($get_module, $from_module, $get_id) {
	global $beanList, $beanFiles;

	// instantiate and retrieve $from_module
	$from_class = $beanList[$from_module];
	require_once($beanFiles[$from_class]);
	$from_mod = new $from_class();
	$from_mod->retrieve($get_id);

	$field = get_module_link_field($from_module, $get_module);
	if ($field === FALSE) {
		return FALSE;
	}

	$from_mod->load_relationship($field);
	$id_arr = $from_mod->$field->get();

	//bug: 38065
	if ($get_module == 'EmailAddresses') {
        $emailAddresses = $from_mod->get_linked_beans('email_addresses');
        foreach ($emailAddresses as $emailAddress) {
            $email_arr[] = $emailAddress->id;
        }
		return $email_arr;
	}

	return $id_arr;
}

?>
