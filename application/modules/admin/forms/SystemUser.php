<?php

class Admin_Form_SystemUser extends Zend_Form
{
	public function init(){
		$this->setMethod('post');
		$this->addElement('hidden', 'system_user_id', array(
				'value'		 => '',
				'filters'    => array('StringTrim')
		));
		
		$notEmpty = new Zend_Validate_NotEmpty();
		$notEmpty->setMessage('Enter A Password.');
		$this->addElement('text', 'email', array(
			'label'      => 'User Email:',
			'size'		 => '35',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array($notEmpty,true),
            	array('EmailAddress',true),
            ),
		'errorMessages' => array('Invalid Email Address')
				
        ));
		$notEmpty = new Zend_Validate_NotEmpty();
		$notEmpty->setMessage('Value Is Required.');
		$this->addElement('password', 'password', array(
				'label'      => 'Password:',
				'size'		 => '35',
				'required'   => true,
				'filters'    => array('StringTrim'),
				'validators' => array(
						array($notEmpty,true),
						array('StringLength',true,
								array('min' => 6,
										'max' => 50,
										'messages' => array(
												Zend_Validate_StringLength::INVALID =>
												'Invalid Password',
												Zend_Validate_StringLength::TOO_LONG =>
												'Password too long',
												Zend_Validate_StringLength::TOO_SHORT =>
												'Password must be of minimum 6 character'))),
				)
		));
		$confValidator = new Zend_Validate_Identical('password');
		$confValidator->setMessage("Confirm password do not match");
		
		$this->addElement('password', 'confirm_password', array(
				'label'      => 'Confirm Password:',
				'size'		 => '35',
				'required'   => true,
				'filters'    => array('StringTrim'),
				'validators' => array(
						array($notEmpty,true),array($confValidator,true)
				)
		));
		$this->addElement('select','role',array(
				'label'		 => 'Role:',
				'MultiOptions' => $this->_getGroupMultiOptions(),
				'validators'	=>	array(
						'NotEmpty'
				),
				'Required'	=>	true
		));
		$this->addElement('checkbox', 'status', array(
				'label'      => 'Active',
				'value'      => '1'
		));
		// Add the submit button
		$this->addElement('submit', 'submit', array(
				'ignore'   => true
		));
		// Add the reset button
		$this->addElement('reset', 'reset', array(
				'ignore'   => true
		));
    }
    public function _getGroupMultiOptions()
    {
    	$options = array("" => 'Select user group');
    	$options[1] = "System Administrator";
    	$options[2] = "System User";
    	return $options;
    }
}