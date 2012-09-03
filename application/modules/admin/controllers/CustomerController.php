<?php
class Admin_CustomerController extends Zend_Controller_Action {
	public function init() {
		/* Initialize action controller here */
	}
	public function indexAction() {
		// action body
	}
	public function addAction() {
		$this->view->heading = "Add Customer";
		
		// Customer Form
		$customerForm = new Admin_Form_Customer();
		$this->view->customerForm = $customerForm;
		
		
		$this->render ( "add-edit" );
	}
	public function editAction() {
		$this->view->heading = "Edit Customer";
		$this->render ( "add-edit" );
	}
}

