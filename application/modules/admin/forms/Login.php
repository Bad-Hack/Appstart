<?php
class Admin_Form_Login extends Zend_Form {
	public function init() {
		$this->setMethod ( 'post' );
		$notEmpty = new Zend_Validate_NotEmpty ();
		$notEmpty->setMessage ( 'Enter A Password.' );
		
		// Adding Email to the form
		$email = $this->createElement ( "text", "email", array (
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
		
		// Password Element
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
						) 
				) 
		) );
		$password->setAttrib ( "required", "required" );
		$this->addElement ( $password );
		
		// Remember Me
		$remember = $this->createElement ( 'checkbox', 'remember', array (
				'label' => 'Keep me logged in',
				'value' => 'checked' 
		) );
		$this->addElement ( $remember );
		
		// Submit button
		$submit = $this->createElement ( 'submit', 'submit', array (
				'ignore' => true,
				'class' => "button" 
		) );
		$this->addElement ( $submit );
	}
}

