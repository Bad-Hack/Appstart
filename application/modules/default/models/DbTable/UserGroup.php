<?php
class Default_Model_DbTable_UserGroup extends Zend_Db_Table_Abstract {
	protected $_name = 'user_group';
	protected $_dependentTables = array (
			'Admin_Model_DbTable_User' 
	);
	protected $_referenceMap = array (
			'Customer' => array (
					'columns' => array (
							'customer_id' 
					),
					'refTableClass' => 'Admin_Model_DbTable_Customer',
					'refColumns' => array (
							'customer_id' 
					),
					'onDelete'          => self::CASCADE,
					'onUpdate'          => self::CASCADE
			) 
	);
}