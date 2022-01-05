<?php



global $dictionary;
$dictionary['ACLRole'] = ['table' => 'acl_roles', 'comment' => 'ACL Role definition'
                               ,'fields' => [
  'id' => 
  [
    'name' => 'id',
    'vname' => 'LBL_ID',
    'required'=>true,
    'type' => 'id',
    'reportable'=>false,
    'comment' => 'Unique identifier'
  ],
   'date_entered' => 
  [
    'name' => 'date_entered',
    'vname' => 'LBL_DATE_ENTERED',
    'type' => 'datetime',
    'required'=>true,
    'comment' => 'Date record created'
  ],
  'date_modified' => 
  [
    'name' => 'date_modified',
    'vname' => 'LBL_DATE_MODIFIED',
    'type' => 'datetime',
    'required'=>true,
    'comment' => 'Date record last modified'
  ],
    'modified_user_id' => 
  [
    'name' => 'modified_user_id',
    'rname' => 'user_name',
    'id_name' => 'modified_user_id',
    'vname' => 'LBL_MODIFIED_BY',
    'type' => 'assigned_user_name',
    'table' => 'modified_user_id_users',
    'isnull' => 'false',
    'dbType' => 'id',
    'required'=> false,
    'len' => 36,
    'reportable'=>true,
    'comment' => 'User who last modified record'
  ],
    'created_by' => 
  [
    'name' => 'created_by',
    'rname' => 'user_name',
    'id_name' => 'created_by',
    'vname' => 'LBL_CREATED',
    'type' => 'assigned_user_name',
    'table' => 'created_by_users',
    'isnull' => 'false',
    'dbType' => 'id',
    'len' => 36,
    'comment' => 'User who created record'
  ],
   'name' => 
  [
    'name' => 'name',
    'type' => 'varchar',
    'vname' => 'LBL_NAME',
    'len' => 150,
    'comment' => 'The role name'
  ],
   'description' => 
  [
    'name' => 'description',
    'vname' => 'LBL_DESCRIPTION',
    'type' => 'text',
    'comment' => 'The role description'
  ],
  'deleted' => 
  [
    'name' => 'deleted',
    'vname' => 'LBL_DELETED',
    'type' => 'bool',
    'reportable'=>false,
    'comment' => 'Record deletion indicator'
  ],
  'users' => 
  [
  	'name' => 'users',
    'type' => 'link',
    'relationship' => 'acl_roles_users',
    'source'=>'non-db',
	'vname'=>'LBL_USERS',
  ],
    'actions' => 
  [
  	'name' => 'actions',
    'type' => 'link',
    'relationship' => 'acl_roles_actions',
    'source'=>'non-db',
	'vname'=>'LBL_USERS',
  ],
    ]
, 'indices' => [
       ['name' =>'aclrolespk', 'type' =>'primary', 'fields'=> ['id']],
       ['name' =>'idx_aclrole_id_del', 'type' =>'index', 'fields'=> ['id', 'deleted']],
    ]

];

 

?>
