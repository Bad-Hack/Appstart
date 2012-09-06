<?php
class Admin_Form_Customer extends Zend_Form {
	public function init() {
		$this->setMethod ( 'POST' );
		$notEmptyValidator = new Zend_Validate_NotEmpty ();
		$notEmptyValidator->setMessage ( 'Enter Enter Valid Value For The Field.' );
		
		// Customer ID
		$customer_id = $this->createElement ( "hidden", "customer_id", array (
				'value' => '',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $customer_id );
		
		// App Access ID
		// Check with front_controller
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$uniqueAppAccessIdValidator = new Zend_Validate_Db_NoRecordExists ( array (
				'table' => 'customer',
				'field' => 'app_access_id',
				'exclude' => array (
						'field' => 'customer_id',
						'value' => $request->getParam("customer_id",null) 
				) 
		) );
		$uniqueAppAccessIdValidator->setMessage ( "App Access ID already exits" );
		
		$app_access_id = $this->createElement ( "text", "app_access_id", array (
				'label' => 'App Access ID:',
				'size' => '35',
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
								$uniqueAppAccessIdValidator,
								true 
						) 
				) 
		) );
		
		$app_access_id->setAttrib ( "required", "required" );
		
		$this->addElement ( $app_access_id );
		
		// User ID
		$user_id = $this->createElement ( "hidden", "user_id", array (
				'value' => '',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $user_id );
		
		// Customer Name
		$customer_name = $this->createElement ( "text", "customer_name", array (
				'label' => 'Customer Name:',
				'size' => '30',
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
						'Invalid Username' 
				) 
		) );
		$customer_name->setAttrib ( "required", "required" );
		$this->addElement ( $customer_name );
		
		// Business Type ID
		$business_type_id = $this->createElement ( 'select', 'business_type_id', array (
				'label' => 'Business Type:',
				'MultiOptions' => $this->_getBusinessType (),
				'validators' => array (
						'NotEmpty' 
				),
				'Required' => true 
		) );
		$business_type_id->setAttrib ( "required", "required" );
		$this->addElement ( $business_type_id );
		
		// Address
		$address = $this->createElement ( "textarea", "address", array (
				'label' => 'Address:',
				'size' => '90',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $address );
		
		// Country
		$country = $this->createElement ( "text", "country", array (
				'label' => 'Country:',
				'size' => '30',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $country );
		
		// City
		$city = $this->createElement ( "text", "city", array (
				'label' => 'City:',
				'size' => '30',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $city );
		
		// Contact Person Name
		$contact_person_name = $this->createElement ( "text", "contact_person_name", array (
				'label' => 'Contact Person Name:',
				'size' => '50',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $contact_person_name );
		
		// Contact Person Email
		$contact_person_email = $this->createElement ( "text", "contact_person_email", array (
				'label' => 'Contact Person Email:',
				'size' => '60',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $contact_person_email );
		
		// Contact Person Phone
		$contact_person_phone = $this->createElement ( "text", "contact_person_phone", array (
				'label' => 'Contact Person Phone:',
				'size' => '20',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $contact_person_phone );
		
		// Status
		$status = $this->createElement ( 'select', 'status', array (
				'label' => 'Status:',
				'MultiOptions' => array (
						'1' => 'Active',
						'0' => 'InActive' 
				),
				'validators' => array (
						'NotEmpty' 
				),
				'Required' => true 
		) );
		$status->setAttrib ( "required", "required" );
		$this->addElement ( $status );
		
		// Template ID
		$template_id = $this->createElement ( 'select', 'template_id', array (
				'label' => 'Template:',
				'MultiOptions' => $this->_getTemplates (),
				'validators' => array (
						'NotEmpty' 
				),
				'Required' => true 
		) );
		$template_id->setAttrib ( "required", "required" );
		$this->addElement ( $template_id );
		
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
	public function _getTemplates() {
		$options = array (
				"" => 'Select Template' 
		);
		
		$mapper = new Admin_Model_Mapper_Template ();
		$models = $mapper->fetchAll ();
		foreach ( $models as $template ) {
			$options [$template->getTemplateId ()] = $template->getName ();
		}
		
		return $options;
	}
}