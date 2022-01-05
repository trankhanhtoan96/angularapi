<?php


 if(!defined('ACL_ALLOW_NONE')){   
 	define('ACL_ALLOW_ADMIN_DEV', 100);
 	define('ACL_ALLOW_ADMIN', 99);
 	define('ACL_ALLOW_ALL', 90);                        
 	define('ACL_ALLOW_ENABLED', 89);
 	define('ACL_ALLOW_OWNER', 75);
 	define('ACL_ALLOW_NORMAL', 1);
 	define('ACL_ALLOW_DEFAULT', 0);
 	define('ACL_ALLOW_DISABLED', -98);
 	define('ACL_ALLOW_NONE', -99);
 	define('ACL_ALLOW_DEV', 95);
 }
 /**
  * $GLOBALS['ACLActionAccessLevels
  * these are rendering descriptions for Access Levels giving information such as the label, color, and text color to use when rendering the access level
  */
 $GLOBALS['ACLActionAccessLevels'] = [
 	ACL_ALLOW_ALL=> ['color'=>'#008000', 'label'=>'LBL_ACCESS_ALL', 'text_color'=>'white'],
 	ACL_ALLOW_OWNER=> ['color'=>'#6F6800', 'label'=>'LBL_ACCESS_OWNER', 'text_color'=>'white'],
 	ACL_ALLOW_NONE=> ['color'=>'#FF0000', 'label'=>'LBL_ACCESS_NONE', 'text_color'=>'white'],
 	ACL_ALLOW_ENABLED=> ['color'=>'#008000', 'label'=>'LBL_ACCESS_ENABLED', 'text_color'=>'white'],
 	ACL_ALLOW_DISABLED=> ['color'=>'#FF0000', 'label'=>'LBL_ACCESS_DISABLED', 'text_color'=>'white'],
 	ACL_ALLOW_ADMIN=> ['color'=>'#0000FF', 'label'=>'LBL_ACCESS_ADMIN', 'text_color'=>'white'],
 	ACL_ALLOW_NORMAL=> ['color'=>'#008000', 'label'=>'LBL_ACCESS_NORMAL', 'text_color'=>'white'],
 	ACL_ALLOW_DEFAULT=> ['color'=>'#008000', 'label'=>'LBL_ACCESS_DEFAULT', 'text_color'=>'white'],
 	ACL_ALLOW_DEV=> ['color'=>'#0000FF', 'label'=>'LBL_ACCESS_DEV', 'text_color'=>'white'],
 	ACL_ALLOW_ADMIN_DEV=> ['color'=>'#0000FF', 'label'=>'LBL_ACCESS_ADMIN_DEV', 'text_color'=>'white'],
 ];
/**
 * $GLOBALS['ACLActions
 * These are the actions for a given type. It includes the ACCESS Levels for that action and the label for that action. Every an object of the category (e.g. module) is added all associated actions are added for that object
 */
$GLOBALS['ACLActions'] = [
	'module'=> ['actions'=>
						[
						'access'=>
								[
									'aclaccess'=> [ACL_ALLOW_ENABLED,ACL_ALLOW_DEFAULT, ACL_ALLOW_DISABLED],
									'label'=>'LBL_ACTION_ACCESS',
									'default'=>ACL_ALLOW_ENABLED,
                                ],
							
						'view'=>
								[
									'aclaccess'=> [ACL_ALLOW_ALL,ACL_ALLOW_OWNER,ACL_ALLOW_DEFAULT, ACL_ALLOW_NONE],
									'label'=>'LBL_ACTION_VIEW',
									'default'=>ACL_ALLOW_ALL,
                                ],
					
						'list'=>
								[
									'aclaccess'=> [ACL_ALLOW_ALL,ACL_ALLOW_OWNER,ACL_ALLOW_DEFAULT, ACL_ALLOW_NONE],
									'label'=>'LBL_ACTION_LIST',
									'default'=>ACL_ALLOW_ALL,
                                ],
						'edit'=>
								[
									'aclaccess'=> [ACL_ALLOW_ALL,ACL_ALLOW_OWNER,ACL_ALLOW_DEFAULT, ACL_ALLOW_NONE],
									'label'=>'LBL_ACTION_EDIT',
									'default'=>ACL_ALLOW_ALL,

                                ],
						'create'=>
								[
									'aclaccess'=> [ACL_ALLOW_ALL,ACL_ALLOW_DEFAULT, ACL_ALLOW_NONE],
									'label'=>'LBL_ACTION_CREATE',
									'default'=>ACL_ALLOW_ALL,

                                ],
						'delete'=>
							[
									'aclaccess'=> [ACL_ALLOW_ALL,ACL_ALLOW_OWNER,ACL_ALLOW_DEFAULT, ACL_ALLOW_NONE],
									'label'=>'LBL_ACTION_DELETE',
									'default'=>ACL_ALLOW_ALL,

                            ],
						'import'=>
							[
									'aclaccess'=> [ACL_ALLOW_ALL,ACL_ALLOW_DEFAULT, ACL_ALLOW_NONE],
									'label'=>'LBL_ACTION_IMPORT',
									'default'=>ACL_ALLOW_ALL,
                            ],
						'export'=>
							[
									'aclaccess'=> [ACL_ALLOW_ALL,ACL_ALLOW_OWNER,ACL_ALLOW_DEFAULT, ACL_ALLOW_NONE],
									'label'=>'LBL_ACTION_EXPORT',
									'default'=>ACL_ALLOW_ALL,
                            ],
                        'massupdate'=>
							[
									'aclaccess'=> [ACL_ALLOW_ALL,ACL_ALLOW_DEFAULT, ACL_ALLOW_NONE],
									'label'=>'LBL_ACTION_MASSUPDATE',
									'default'=>ACL_ALLOW_ALL,
                            ],


                        ],],
];


?>
