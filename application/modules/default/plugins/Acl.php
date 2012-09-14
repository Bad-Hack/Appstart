<?php
class Default_Plugin_Acl extends Zend_Acl {
	protected static $_instance;
	public static function getInstance()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
	
		return self::$_instance;
	}
	public function __construct() {
		//Zend_Loader_Autoloader::autoload('Admin_Model_Mapper_Module');
		$arrResources = array();
		
		// Add static resources
		$this->addResource(new Zend_Acl_Resource("index"));
		$this->addResource(new Zend_Acl_Resource("settings"));
		$this->addResource(new Zend_Acl_Resource("user-group"));
		$this->addResource(new Zend_Acl_Resource("profile"));

		$moduleMapper = new Admin_Model_Mapper_Module();
		$modules = $moduleMapper->fetchAll("status=1");
		foreach($modules as $module) {
			$this->addResource(new Zend_Acl_Resource($module->getName()));
			$arrResources[$module->getModuleId()] = $module->getName();
		}
		
		// Setup Roles
		$rolesMapper = new Default_Model_Mapper_UserGroup();
		$roles = $rolesMapper->fetchAll();
		foreach($roles as $role) {
			$this->addRole(new Zend_Acl_Role($role->getUserGroupId()));
			
			// Add static permissins
			$this->allow($role->getUserGroupId(),"index");
			$this->allow($role->getUserGroupId(),"settings");
			$this->allow($role->getUserGroupId(),"user-group");
			$this->allow($role->getUserGroupId(),"profile");
			
			// Set Permissions
			$groupmodulesMapper = new Default_Model_Mapper_UserGroupModule();
			$groupmodules = $groupmodulesMapper->fetchAll("status=1 AND user_group_id=".$role->getUserGroupId());
			foreach($groupmodules as $module) {
				$this->allow($role->getUserGroupId(),$arrResources[$module->getModuleId()]);
			}
		}
	}
}