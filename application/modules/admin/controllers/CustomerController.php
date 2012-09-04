<?php
class Admin_CustomerController extends Zend_Controller_Action {
	public static $ADD_MODE = "add";
	public static $EDIT_MODE = "edit";
	public function init() {
		/* Initialize action controller here */
	}
	public function indexAction() {
		// action body
	}
	public function addAction() {
		$this->view->heading = "Add Customer";
		
		$customerForm = $this->_createCustomerForm ();
		
		// Configure the user form according to the need
		$userForm = new Default_Form_User ();
		// Add Username
		$customerForm->addElement ( $userForm->getElement ( "username" )->setLabel ( 'Customer Username' ) );
		// Add Password
		$customerForm->addElement ( $userForm->getElement ( "password" )->setLabel ( 'Customer Password' ) );
		
		// Configure the Customer Configuration Form
		$customerConfigurationForm = new Admin_Form_CustomerConfiguration ();
		
		// Set form for view
		$this->view->customerForm = $customerForm;
		$this->view->customerConfigurationForm = $customerConfigurationForm;
		$this->render ( "add-edit" );
	}
	public function editAction() {
		$this->view->heading = "Edit Customer";
		$this->render ( "add-edit" );
	}
	private function _createCustomerForm($mode = null) {
		if ($mode == null) {
			$mode = self::$ADD_MODE;
		}
		
		$customerForm = new Admin_Form_Customer ();
		if ($mode == self::$ADD_MODE) {
		}
		
		return $customerForm;
	}
	public function saveCustomerAction() {
		sleep(2);
		$response = array ();
		$response = array (
				"success" => array (
						"message" => "Customer Saved Successfully" 
				) 
		);
		/*$response = array (
				"errors" => array (
						"username" => "Username already exists",
						"password" => "Too weak password"
				)
		);
		*/
		$this->_helper->json ( $response );
	}
}

