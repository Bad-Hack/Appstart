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
		
		$columns = array(
				'business_type_id',
				'name'
		);
		
		$filter = " 1=1 ";
		$gridData["sEcho"] = $this->_request->getParam("sEcho",1);
		$gridData["iTotalRecords"] = $businessTypeMapper->countAll($filter);
		$gridData["iTotalDisplayRecords"] = $businessTypeMapper->countAll($filter);
		$gridData["aaData"] = $businessTypeMapper->getList($columns,null);
		echo $this->_helper->json($gridData);
	}
	
	public function addAction() {
		// action body
	
	}
	
	public function editAction() {
		// action body
	
	}
}