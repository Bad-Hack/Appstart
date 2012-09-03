<?php
class Admin_Form_SystemUser extends Zend_Form {
	public function init() {
		$this->setMethod ( 'post' );
		
		// System User ID
		$system_user_id = $this->createElement ( 'hidden', 'system_user_id', array (
				'value' => '',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $system_user_id );
		
		$notEmpty = new Zend_Validate_NotEmpty ();
		$notEmpty->setMessage ( 'Value Is Required.' );
		
		// Email
		$email = $this->createElement ( 'text', 'email', array (
				'label' => 'User Email:',
				'size' => '35',
				'required' => true,
				'filters' => array (
						'StringTrim' 
				),
				'validators' => array (
						array (
								$notEmpty,
								true 
						),
						array (
								'EmailAddress',
								true 
						) 
				),
				'errorMessages' => array (
						'Invalid Email Address' 
				) 
		) );
		$email->setAttrib ( "required", "required" );
		$this->addElement ( $email );
		
		// Password
		$password = $this->createElement ( 'password', 'password', array (
				'label' => 'Password:',
				'size' => '35',
				'required' => true,
				'filters' => array (
						'StringTrim' 
				),
				'validators' => array (
						array (
								$notEmpty,
								true 
						),
						array (
								'StringLength',
								true,
								array (
										'min' => 6,
										'max' => 50,
										'messages' => array (
												Zend_Validate_StringLength::INVALID => 'Invalid Password',
												Zend_Validate_StringLength::TOO_LONG => 'Password too long',
												Zend_Validate_StringLength::TOO_SHORT => 'Password must be of minimum 6 character' 
										) 
								) 
						) 
				) 
		) );
		$password->setAttrib ( "required", "required" );
		$this->addElement ( $password );
		
		// Confirm Password
		$confValidator = new Zend_Validate_Identical ( 'password' );
		$confValidator->setMessage ( "Confirm password do not match" );
		
		$confirm_password = $this->createElement ( 'password', 'confirm_password', array (
				'label' => 'Confirm Password:',
				'size' => '35',
				'required' => true,
				'filters' => array (
						'StringTrim' 
				),
				'validators' => array (
						array (
								$notEmpty,
								true 
						),
						array (
								$confValidator,
								true 
						) 
				) 
		) );
		$confirm_password->setAttrib ( "required", "required" );
		$this->addElement ( $confirm_password );
		
		// Role
		$role = $this->createElement ( 'select', 'role', array (
				'label' => 'Role:',
				'MultiOptions' => $this->_getGroupMultiOptions (),
				'validators' => array (
						'NotEmpty' 
				),
				'required' => true 
		) );
		$role->setAttrib ( "required", "required" );
		$this->addElement ( $role );
		
		// Add the submit button
		$submit = $this->createElement ( 'submit', 'submit', array (
				'ignore' => true,
				'onclick' => 'validate();' 
		) );
		$this->addElement ( $submit );
		
		// Add the reset button
		$reset = $this->createElement ( 'reset', 'reset', array (
				'ignore' => true 
		) );
		$this->addElement ( $reset );
	}
	
	/**
	 *
	 * @return multitype:string
	 */
	public function _getGroupMultiOptions() {
		$options = array (
				"" => 'Select user group' 
		);
		$options [1] = "Administrator";
		$options [2] = "System User";
		return $options;
	}
}