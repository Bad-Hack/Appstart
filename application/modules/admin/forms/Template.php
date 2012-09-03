<?php
class Admin_Form_Template extends Zend_Form {
	public function init() {
		$this->setMethod ( 'post' );
		
		$template_id = $this->createElement ( 'hidden', 'template_id', array (
				'value' => '',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $template_id );
		
		$notEmpty = new Zend_Validate_NotEmpty ();
		$notEmpty->setMessage ( 'Enter A Password.' );
		
		// Name
		$name = $this->createElement ( 'text', 'name', array (
				'label' => 'Name:',
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
		$this->addElement ( $name );
		
		// Business Type ID
		$business_type_id = $this->createElement ( 'select', 'business_type_id', array (
				'label' => 'Business Type:',
				'MultiOptions' => $this->_getBusinessType (),
				'validators' => array (
						'NotEmpty' 
				),
				'Required' => true 
		) );
		$this->addElement ( $business_type_id );
		
		// Modules
		$modules = $this->createElement ( 'multiselect', 'modules', array (
				'label' => 'Modules:',
				'MultiOptions' => $this->_getModules (),
				'validators' => array (
						'NotEmpty' 
				),
				'Required' => true 
		) );
		$this->addElement ( $modules );
		
		// Status
		$status = $this->createElement ( 'checkbox', 'status', array (
				'label' => 'Active',
				'value' => '1' 
		) );
		$this->addElement ( $status );
		
		// Submit
		$submit = $this->createElement ( 'submit', 'submit', array (
				'ignore' => true,
				'onclick' => 'validate();' 
		) );
		$this->addElement ( $submit );
		
		// Reset
		$reset = $this->createElement ( 'reset', 'reset', array (
				'ignore' => true 
		) );
		$this->addElement ( $reset );
	}
	public function _getBusinessType() {
		$options = array (
				"" => 'Select business type' 
		);
		
		$mapper = new Admin_Model_Mapper_BusinessType ();
		$models = $mapper->fetchAll ();
		foreach ( $models as $businessType ) {
			$options [$businessType->getBusinessTypeId ()] = $businessType->getName ();
		}
		
		return $options;
	}
	public function _getModules() {
		$options = array ();
		
		$mapper = new Admin_Model_Mapper_Module ();
		$models = $mapper->fetchAll ();
		foreach ( $models as $module ) {
			$options [$module->getModuleId ()] = $module->getName ();
		}
		
		return $options;
	}
}