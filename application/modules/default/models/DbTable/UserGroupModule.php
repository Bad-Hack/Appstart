<?php
class Default_Model_DbTable_UserGroupModule extends Zend_Db_Table_Abstract {
	protected $_name = 'user_group_module';
	protected $_referenceMap = array (
			'UserGroup' => array (
					'columns' => array (
							'user_group_id' 
					),
					'refTableClass' => 'Default_Model_DbTable_UserGroup',
					'refColumns' => array (
							'user_group_id' 
					) 
			),
			'Module' => array(
					'columns' => array (
							'module_id'
					),
					'refTableClass' => 'Admin_Model_DbTable_Module',
					'refColumns' => array (
							'module_id'
					)
			)
	);
}