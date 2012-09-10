<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
	public function _initAutoload() {
		$autoloader = new Zend_Application_Module_Autoloader ( array (
				'basePath' => APPLICATION_PATH . '/modules/admin/',
				'namespace' => '',
				'resourceTypes' => array (
						'model' => array (
								'path' => 'models/',
								'namespace' => 'Admin_Model_' 
						) 
				) 
		) );
		
		return $autoloader;
	}
}

