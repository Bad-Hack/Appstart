<?php
class Default_Plugin_Authentication extends Zend_Controller_Plugin_Abstract {
	private $_acl = null;
	private $_auth = null;
	public function __construct(Zend_Auth $auth) {
		$this->_auth = $auth;
	}
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		if($this->_acl == null) {
			$this->_acl = new Default_Plugin_Acl();
		}
		
		$resource = $request->getControllerName ();
		$action = $request->getActionName ();
		if (strtolower ( $request->getModuleName () ) != "admin") {
			
			if($resource != "error") {
				if (! $this->_auth->hasIdentity () && $resource != "forgot" && $action != "check-login" ) {
					$request->setModuleName("default")->setControllerName ( 'login' )->setActionName ( 'index' );
				} else if ((! isset ( $this->_auth->getStorage ()->read ()->group_id ) || $this->_auth->getStorage ()->read ()->group_id == 0) && $resource != "forgot" && $action != "check-login") {
					$request->setModuleName("default")->setControllerName ( 'login' )->setActionName ( 'index' );
				}
				
				if ($this->_auth->hasIdentity () && $this->_auth->getStorage ()->read ()->group_id != "guest") {
					if($request->getModuleName ()== "default" && 
							$this->_acl->hasRole($this->_auth->getStorage ()->read ()->group_id) && 
							$this->_acl->has($resource) &&
							$this->_acl->isAllowed($this->_auth->getStorage ()->read ()->group_id,$resource)) {
						// Access Allowed For Default Module
						$this->_initNavigation();
					} else if($request->getModuleName () != "default" &&
							$this->_acl->hasRole($this->_auth->getStorage ()->read ()->group_id) &&
							$this->_acl->has($request->getModuleName ()) &&
							$this->_acl->isAllowed($this->_auth->getStorage ()->read ()->group_id,$request->getModuleName ())) {
						// Access Allowed For Other Modules
						$this->_initNavigation();
					}
					else {
						$request->setModuleName("default")->setControllerName ( 'login' )->setActionName ( 'logout' );
					}
				} else {
					$request->setModuleName("default")->setControllerName ( 'login' )->setActionName ( 'index' );
				}
			}
		}
	}
	
	private function _initNavigation() {
		$view = Zend_Layout::getMvcInstance ()->getView ();
		$config = new Zend_Config( array(), true);
		
		$iterator = new DirectoryIterator(APPLICATION_PATH . '/modules/');
		foreach ($iterator as $fileinfo) {
			if($fileinfo->isDir() && strtolower($fileinfo->getFilename()) != "admin") {
				if(file_exists($fileinfo->getPath().'/'.$fileinfo->getFilename().'/configs/navigation.xml')) {
					$config->merge(new Zend_Config_Xml ( $fileinfo->getPath().'/'.$fileinfo->getFilename().'/configs/navigation.xml', "nav" ));
				}
			}
		}
		//var_dump($config);die;
		//die;
		$navigation = new Zend_Navigation ( $config );
		$view->navigation ( $navigation )->setAcl ( $this->_acl )->setRole ( $this->_auth->getStorage ()->read ()->group_id );
	}
}