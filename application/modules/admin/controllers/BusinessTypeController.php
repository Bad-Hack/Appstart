<?php

class Admin_BusinessTypeController extends Zend_Controller_Action {
	
	public function init() {
		/* Initialize action controller here */
	}
	
	public function indexAction() {
		// action body
	
	}
	
	public function gridAction() {
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$businessTypeMapper = new Admin_Model_Mapper_BusinessType();
		$response = $businessTypeMapper->getDataTableList(array(
			'column' => array(
					'id' => array('actions'),
			)
		));
		
		echo $this->_helper->json($response);
		
	}
	
	public function addAction() {
		// action body
	
	}
	
	public function editAction() {
		// action body
	
	}
}