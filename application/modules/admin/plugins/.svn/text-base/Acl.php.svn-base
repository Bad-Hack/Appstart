<?php
class Admin_Plugin_Acl extends Zend_Acl {
	protected static $_instance;
	public static function getInstance()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
	
		return self::$_instance;
	}
	public function __construct() {
		
		// Setup Roles
		$this->addRole(new Zend_Acl_Role('guest'))
			 ->addRole(new Zend_Acl_Role('admin'))
			 ->addRole(new Zend_Acl_Role('user'));
		
		// Set Resources
		$this->addResource(new Zend_Acl_Resource('user'))
			 ->addResource(new Zend_Acl_Resource('business_type'))
			 ->addResource(new Zend_Acl_Resource('template'))
			 ->addResource(new Zend_Acl_Resource('customer'));
		
		// Set Permissions
		$this->allow('admin',array('user','business_type','template'))
			 ->allow(array('admin','user'),'customer');
		
	}
}