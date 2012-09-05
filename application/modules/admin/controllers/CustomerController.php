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
		
		$customerForm = new Admin_Form_Customer ();
		
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
	public function saveCustomerAction() {
		$customerForm = new Admin_Form_Customer ();
		$userForm = new Default_Form_User ();
		
		// Add Username
		$customerForm->addElement ( $userForm->getElement ( "username" )->setLabel ( 'Customer Username' ) );
		// Add Password
		$customerForm->addElement ( $userForm->getElement ( "password" )->setLabel ( 'Customer Password' ) );
		
		$response = array ();
		$params = $this->_request->getParams ();
		if ($customerForm->isValid ( $params )) {
			
			$refinedParams = $customerForm->getValues ();
			
			// Add Created-At and Last-Updated-At
			$refinedParams ["last_updated_at"] = Standard_Functions::getCurrentDateTime ( time (), Standard_Functions::$MYSQL_DATETIME_FORMAT );
			$refinedParams ["created_at"] = Standard_Functions::getCurrentDateTime ( time (), Standard_Functions::$MYSQL_DATETIME_FORMAT );
			
			$customerMapper = new Admin_Model_Mapper_Customer ();
			try {
				$customerUserData = $customerMapper->saveCustomer ( $refinedParams );
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
									'message' => $ex->getMessage () 
							) 
					);
				}
			}
			if (empty ( $response )) {
				$response = array(
						'success' => array(
								'message' => $customerUserData
						)
				);
			}
		} else {
			$errors = $customerForm->getMessages ();
			
			foreach ( $errors as $name => $error ) {
				$errors [$name] = array_pop($error);
			}
			$response = array (
					"errors" => $errors 
			);
		}
		$this->_helper->json ( $response );
	}
}

