<?php

global $dictionary;
$dictionary['Role'] = ['table' => 'roles'
                               ,'fields' => [
  'id' =>
  [
    'name' => 'id',
    'vname' => 'LBL_ID',
    'required'=>true,
    'type' => 'id',
    'reportable'=>false,
  ],
   'date_entered' =>
  [
    'name' => 'date_entered',
    'vname' => 'LBL_DATE_ENTERED',
    'type' => 'datetime',
    'required'=>true
  ],
  'date_modified' =>
  [
    'name' => 'date_modified',
    'vname' => 'LBL_DATE_MODIFIED',
    'type' => 'datetime',
    'required'=>true,
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
  ],
   'name' =>
  [
    'name' => 'name',
    'type' => 'varchar',
    'vname' => 'LBL_NAME',
    'len' => 150,
    'importable' => 'required',
  ],
   'description' =>
  [
    'name' => 'description',
    'vname' => 'LBL_DESCRIPTION',
    'type' => 'text',
  ],
  'modules' =>
  [
    'name' => 'modules',
    'vname' => 'LBL_MODULES',
    'type' => 'text',
  ],
  'deleted' =>
  [
    'name' => 'deleted',
    'vname' => 'LBL_DELETED',
    'type' => 'bool',
    'reportable'=>false,
  ],
  'users' =>
  [
  	'name' => 'users',
    'type' => 'link',
    'relationship' => 'roles_users',
    'source'=>'non-db',
	'vname'=>'LBL_USERS',
  ],
    ]
, 'indices' => [
       ['name' =>'rolespk', 'type' =>'primary', 'fields'=> ['id']],
       ['name' =>'idx_role_id_del', 'type' =>'index', 'fields'=> ['id', 'deleted']],
    ]

];
?>
