<?php


global $dictionary;
$dictionary['accounts_contacts'] = ['table' => 'accounts_contacts'
                                  , 'fields' => [
       ['name' =>'id', 'type' =>'varchar', 'len'=>'36']
      , ['name' =>'contact_id', 'type' =>'varchar', 'len'=>'36']
      , ['name' =>'account_id', 'type' =>'varchar', 'len'=>'36']
      , ['name' => 'date_modified','type' => 'datetime']
      , ['name' =>'deleted', 'type' =>'bool', 'len'=>'1', 'required'=>false, 'default'=>0]
    ], 'indices' => [
       ['name' =>'accounts_contactspk', 'type' =>'primary', 'fields'=> ['id']]
      , ['name' => 'idx_account_contact', 'type'=>'alternate_key', 'fields'=> ['account_id','contact_id']]
      , ['name' => 'idx_contid_del_accid', 'type' => 'index', 'fields'=> ['contact_id', 'deleted', 'account_id']]

    ]
      
 	  , 'relationships' => ['accounts_contacts' => ['lhs_module'=> 'Accounts', 'lhs_table'=> 'accounts', 'lhs_key' => 'id',
							  'rhs_module'=> 'Contacts', 'rhs_table'=> 'contacts', 'rhs_key' => 'id',
							  'relationship_type'=>'many-to-many',
							  'join_table'=> 'accounts_contacts', 'join_key_lhs'=>'account_id', 'join_key_rhs'=>'contact_id']]


]
?>
