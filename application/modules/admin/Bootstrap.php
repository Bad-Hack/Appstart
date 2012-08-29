<?php

class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{
	protected $_acl;
	protected $_auth;
	
	public function _initAuloload() {
	
		$this->_acl = Admin_Plugin_Acl::getInstance();
		
		$this->_auth = Zend_Auth::getInstance();
		$temp = $this->_auth->getStorage();
		$fc = Zend_Controller_Front::getInstance();
		if(!$this->_auth->hasIdentity()) {
			$this->_auth->getStorage()->write((object)array('role'=>'guest'));
		}
		
		$fc->registerPlugin(new Admin_Plugin_Authentication($this->_acl, $this->_auth));
		$fc->registerPlugin(new Admin_Plugin_Layout());
	}
	
}


