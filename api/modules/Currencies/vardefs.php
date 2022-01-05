<?php

global $dictionary;
$dictionary['Currency'] = ['table' => 'currencies',
	'comment' => 'Currencies allow Sugar to store and display monetary values in various denominations'
                               ,'fields' => [
  'id' =>
  [
    'name' => 'id',
    'vname' => 'LBL_NAME',
    'type' => 'id',
    'required' => true,
    'reportable'=>false,
    'comment' => 'Unique identifer'
  ],
  'name' =>
  [
    'name' => 'name',
    'vname' => 'LBL_LIST_NAME',
    'type' => 'varchar',
    'len' => '36',
    'required' => true,
    'comment' => 'Name of the currency',
    'importable' => 'required',
  ],
  'symbol' =>
  [
    'name' => 'symbol',
    'vname' => 'LBL_LIST_SYMBOL',
    'type' => 'varchar',
    'len' => '36',
     'required' => true,
     'comment' => 'Symbol representing the currency',
     'importable' => 'required',
  ],
  'iso4217' =>
  [
    'name' => 'iso4217',
    'vname' => 'LBL_LIST_ISO4217',
    'type' => 'varchar',
    'len' => '3',
     'comment' => '3-letter identifier specified by ISO 4217 (ex: USD)',
  ],
  'conversion_rate' =>
  [
    'name' => 'conversion_rate',
    'vname' => 'LBL_LIST_RATE',
    'type' => 'float',
    'dbType' => 'double',
    'default' => '0',
     'required' => true,
	 'comment' => 'Conversion rate factor (relative to stored value)',
	 'importable' => 'required',
  ],
  'status' =>
  [
    'name' => 'status',
    'vname' => 'LBL_STATUS',
    'type' => 'enum',
    'dbType'=>'varchar',
    'options' => 'currency_status_dom',
    'len' => 100,
    'comment' => 'Currency status',
    'importable' => 'required',
  ],
  'deleted' =>
  [
    'name' => 'deleted',
    'vname' => 'LBL_DELETED',
    'type' => 'bool',
    'required' => false,
    'reportable'=>false,
    'comment' => 'Record deletion indicator'
  ],
  'date_entered' =>
  [
    'name' => 'date_entered',
    'vname' => 'LBL_DATE_ENTERED',
    'type' => 'datetime',
     'required' => true,
    'comment' => 'Date record created'

  ],
  'date_modified' =>
  [
    'name' => 'date_modified',
    'vname' => 'LBL_DATE_MODIFIED',
    'type' => 'datetime',
     'required' => true,
    'comment' => 'Date record last modified'
  ],
  'created_by' =>
  [
    'name' => 'created_by',
    'reportable' => false,
    'vname' => 'LBL_CREATED_BY',
    'type' => 'id',
    'len'  => '36',
    'required' => true,
  	'comment' => 'User ID who created record'
  ],
    ]
                                                      , 'indices' => [
   ['name' =>'currenciespk', 'type' =>'primary', 'fields'=> ['id']],
   ['name' =>'idx_currency_name', 'type' =>'index', 'fields'=> ['name','deleted']]
    ]

];


