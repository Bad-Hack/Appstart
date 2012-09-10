<?php

class Default_Bootstrap extends Zend_Application_Module_Bootstrap
{
	public function _initAuloload() 
	{	
		$this->_acl = Default_Plugin_Acl::getInstance();
		$this->_auth = Zend_Auth::getInstance();
		
		if(!$this->_auth->hasIdentity()) {
			$this->_auth->getStorage()->write((object)array('role'=>'guest'));
		}
		
		$fc = Zend_Controller_Front::getInstance();
		$fc->registerPlugin(new Default_Plugin_Authentication($this->_acl, $this->_auth));
	}
}

