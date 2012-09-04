<?php
class Default_Model_DbTable_User extends Zend_Db_Table_Abstract {
	protected $_name = 'user';
	protected $_dependentTables = array (
			'Admin_Model_DbTable_Customer' 
	);
	protected $_referenceMap = array (
			'BusinessType' => array (
					'columns' => array (
							'business_type_id' 
					),
					'refTableClass' => 'Admin_Model_DbTable_BusinessType',
					'refColumns' => array (
							'business_type_id' 
					), 
			),
			'UserGroup' => array (
					'columns' => array (
							'user_group_id' 
					),
					'refTableClass' => 'Default_Model_DbTable_UserGroup',
					'refColums' => array (
							'user_group_id' 
					) 
			) 
	);
}