<?php
class Default_Form_Settings extends Zend_Form {
	public function init() {
		$this->setMethod ( 'POST' );
		
		$this->addElement ( 'hidden', 'user_id', array (
				'filters' => array (
						'StringTrim' 
				) 
		) );
		
		$this->addElement ( 'text', 'name', array (
				'label' => 'Name:',
				'size' => '30',
				'required' => true,
				'filters' => array (
						'StringTrim' 
				),
				'errorMessages' => array (
						'Invalid User Name' 
				) 
		)
		 );
		$this->addElement ( 'text', 'email', array (
				'label' => 'User Email:',
				'size' => '35',
				'required' => true,
				'filters' => array (
						'StringTrim' 
				),
				'errorMessages' => array (
						'Invalid Email Address' 
				) 
		)
		 );
		
		$this->addElement ( 'password', 'password', array (
				'label' => 'Password:',
				'size' => '35',
				'required' => true,
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->getElement ( "password" )->setAttrib ( "required", "required" );
		
		$this->addElement ( 'password', 'confirm_password', array (
				'label' => 'Confirm Password:',
				'size' => '35',
				'required' => true,
				'filters' => array (
						'StringTrim' 
				) 
		)
		 );
		
		$this->getElement ( "confirm_password" )->setAttrib ( "required", "required" );
		
		$this->addElement ( 'submit', 'submit', array (
				'ignore' => true,
				'action' => 'edit' 
		) );
	}
	/*
	 * public function defaults($values) { $records = array(); foreach ($values
	 * as $val) { $records[] = $val; } }
	 */
}
?>