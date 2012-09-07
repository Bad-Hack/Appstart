<?php
class Admin_CustomerController extends Zend_Controller_Action {
	public function init() {
		//$parentModule = $templateModules->findParentRow('Admin_Model_DbTable_Module','Module')->toArray();
		
		/* Initialize action controller here */
	}
	public function indexAction() {
		// action body
	}
	public function addAction() {
		$this->view->heading = "Add Customer";
		
		$customerForm = $this->_createCustomerForm ();
		
		// Configure the Customer Configuration Form
		$customerConfigurationForm = new Admin_Form_CustomerConfiguration ( Admin_Model_Mapper_Customer::$ADD_MODE );
		
		// Set form for view
		$this->view->customerForm = $customerForm;
		$this->view->customerConfigurationForm = $customerConfigurationForm;
		$this->render ( "add-edit" );
	}
	public function editAction() {
		
		// Redirect on no customer_id found
		$customer_id = $this->getParam ( "id", "" );
		if ($customer_id == "") {
			$this->_redirect ( 'index' );
		}
		$customerMapper = new Admin_Model_Mapper_Customer ();
		
		// Find and populate the customer information in customer model
		$customer = $customerMapper->find ( $customer_id );
		if (! $customer) {
			$this->_redirect ( '/admin/customer' );
		}
		
		// Populate the User data with username and password
		$user = new Default_Model_User ();
		$user->populate ( $customer->getUserId () );
		
		$formPopulateData = $customer->toArray ();
		$formPopulateData ["username"] = $user->getUsername ();
		$formPopulateData ["password"] = $user->getPassword ();
		$formPopulateData ["phone"] = $user->getPhone ();
		$formPopulateData ["email"] = $user->getEmail ();
		$this->view->heading = "Edit Customer";
		
		$customerForm = $this->_createCustomerForm ( Admin_Model_Mapper_Customer::$EDIT_MODE );
		
		// Remove Password Validation and required attribute
		$password = $customerForm->getElement ( 'password' );
		$password->removeValidator ( 'NotEmpty' );
		$password->setRequired ( false );
		unset ( $password->required );
		$customerForm->addElement ( $password );
		
		// Populate the form with available data
		$customerForm->populate ( $formPopulateData );
		
		// Configure the Customer Configuration Form
		$customerConfigurationForm = new Admin_Form_CustomerConfiguration ();
		
		// Set form for view
		$this->view->customerForm = $customerForm;
		$this->view->customerConfigurationForm = $customerConfigurationForm;
		
		$this->render ( "add-edit" );
	}
	public function saveCustomerAction() {
		$customer_id = $this->_request->getParam ( "customer_id", "" );
		$user_id = $this->_request->getParam ( "user_id", "" );
		
		// if isset customer_id and user_id in option then set the mode to edit
		// mode
		$mode = Admin_Model_Mapper_Customer::$ADD_MODE;
		if ($customer_id != "" && $customer_id != null && $user_id != "" && $user_id != null)
			$mode = Admin_Model_Mapper_Customer::$EDIT_MODE;
		
		$customerForm = $this->_createCustomerForm ( $mode );
		
		$response = array ();
		$params = $this->_request->getParams ();
		if ($customerForm->isValid ( $params )) {
			
			$refinedParams = $customerForm->getValues ();
			
			$customerMapper = new Admin_Model_Mapper_Customer ();
			try {
				$customerUserData = $customerMapper->saveCustomer ( $refinedParams, $mode );
			} catch ( Exception $ex ) {
				if (strpos ( $ex->getMessage (), "Duplicate entry" ) !== false && strpos ( $ex->getMessage (), "username" ) !== false) {
					$response = array (
							'errors' => array (
									'username' => "Customer username already exists." 
							) 
					);
				} else if (strpos ( $ex->getMessage (), "Duplicate entry" ) !== false && strpos ( $ex->getMessage (), "app_access_id" ) !== false) {
					$response = array (
							'errors' => array (
									'app_access_id' => "App Access ID already exists." 
							) 
					);
				} else {
					$response = array (
							'errors' => array (
									'message' => $ex->getMessage ().$ex->getTraceAsString() 
							) 
					);
				}
			}
			if (empty ( $response )) {
				$response = array (
						'success' => array (
								'message' => $customerUserData 
						) 
				);
			}
		} else {
			$errors = $customerForm->getMessages ();
			
			foreach ( $errors as $name => $error ) {
				$errors [$name] = array_pop ( $error );
			}
			$response = array (
					"errors" => $errors 
			);
		}
		$this->_helper->json ( $response );
	}
	private function _createCustomerForm($mode = null) {
		$mode = $mode == null ? Admin_Model_Mapper_Customer::$ADD_MODE : $mode;
		$customerForm = new Admin_Form_Customer ();
		$userForm = new Default_Form_User ();
		
		// Add Username
		$customerForm->addElement ( $userForm->getElement ( "username" )->setLabel ( 'Customer Username' ) );
		// Add Password
		$customerForm->addElement ( $userForm->getElement ( "password" )->setLabel ( 'Customer Password' ) );
		// Add Phone
		$customerForm->addElement ( $userForm->getElement ( "phone" )->setLabel ( 'Customer Phone' ) );
		// Add Email
		$customerForm->addElement ( $userForm->getElement ( "email" )->setLabel ( 'Customer Email' ) );
		
		if ($mode == Admin_Model_Mapper_Customer::$EDIT_MODE) {
			$password = $customerForm->getElement ( 'password' );
			$password->removeValidator ( 'NotEmpty' );
			$password->setRequired ( false );
			unset ( $password->required );
			$customerForm->addElement ( $password );
		}
		
		return $customerForm;
	}
}

