<?php
class Default_Form_User extends Zend_Form {
	public function init() {
		$this->setMethod ( 'POST' );
		
		$notEmptyValidator = new Zend_Validate_NotEmpty ();
		$notEmptyValidator->setMessage ( 'Enter Enter Valid Value For The Field.' );
		
		// User ID
		$user_id = $this->createElement ( "hidden", "user_id", array (
				'value' => '',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		
		// Username
		// Check with front_controller
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$uniqueUsernameValidator = new Zend_Validate_Db_NoRecordExists ( array (
				'table' => 'user',
				'field' => 'username',
				'exclude' => array (
						'field' => 'user_id',
						'value' => $request->getParam ( "user_id", null ) 
				) 
		) );
		$uniqueUsernameValidator->setMessage ( "Username already exits" );
		$username = $this->createElement ( "text", "username", array (
				'label' => 'Username:',
				'size' => '50',
				'required' => true,
				'filters' => array (
						'StringTrim' 
				),
				'validators' => array (
						array (
								$notEmptyValidator,
								true 
						),
						array (
								$uniqueUsernameValidator,
								true 
						) 
				) 
		) );
		$username->setAttrib ( "required", "required" );
		$this->addElement ( $username );
		
		// Password
		$password = $this->createElement ( "password", "password", array (
				'label' => 'Password:',
				'size' => '50',
				'required' => true,
				'validators' => array (
						array (
								$notEmptyValidator,
								true 
						) 
				),
				'errorMessages' => array (
						'Invalid Password' 
				) 
		) );
		$password->setAttrib ( "required", "required" );
		$this->addElement ( $password );
		
		// Name
		$name = $this->createElement ( "text", "name", array (
				'label' => 'Name:',
				'size' => '35',
				'required' => true,
				'filters' => array (
						'StringTrim' 
				),
				'validators' => array (
						array (
								$notEmptyValidator,
								true 
						) 
				),
				'errorMessages' => array (
						'Invalid Customer Name' 
				) 
		) );
		$this->addElement ( $name );
		
		// Phone
		$phone = $this->createElement ( "text", "phone", array (
				'label' => 'Phone:',
				'size' => '20',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $phone );
		
		// Email
		$email = $this->createElement ( "text", "email", array (
				'label' => 'Email:',
				'size' => '60',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $email );
		
		// Status
		$status = $this->createElement ( 'select', 'status', array (
				'label' => 'Status:',
				'MultiOptions' => array (
						'1' => 'Active',
						'2' => 'InActive' 
				),
				'validators' => array (
						'NotEmpty' 
				),
				'Required' => true 
		) );
		$this->addElement ( $status );
		
		// User Group ID
		$this->addElement ( 'multiselect', 'user_group_id', array (
				'label' => 'User Group:',
				'MultiOptions' => $this->_getUserGroups (),
				'validators' => array (
						'NotEmpty' 
				),
				'Required' => true 
		) );
		
		// Submit Button
		$submit = $this->createElement ( 'submit', 'submit', array (
				'ignore' => true 
		) );
		$this->addElement ( $submit );
		
		// Reset Button
		$reset = $this->createElement ( 'reset', 'reset', array (
				'ignore' => true 
		) );
		$this->addElement ( $reset );
	}
	public function _getUserGroups() {
		$options = array (
				"" => 'Select User Groups' 
		);
		
		$mapper = new Default_Model_Mapper_UserGroup ();
		$models = $mapper->fetchAll ();
		if ($models) {
			foreach ( $models as $userGroup ) {
				$options [$userGroup->getUserGroupId ()] = $userGroup->getName ();
			}
		}
		return $options;
	}
}