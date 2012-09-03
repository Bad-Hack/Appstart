<?php
class Admin_Form_Customer extends Zend_Form {
	public function init() {
		$this->setMethod ( 'POST' );
		$notEmptyValidator = new Zend_Validate_NotEmpty ();
		$notEmptyValidator->setMessage ( 'Enter Enter Valid Value For The Field.' );
		
		// --------------------------------
		// Customer Information Section
		// --------------------------------
		
		// Customer ID
		$customer_id = $this->createElement ( "hidden", "customer_id", array (
				'value' => '',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $customer_id );
		
		// App Access ID
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
						) 
				),
				'errorMessages' => array (
						'Invalid App Access ID' 
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
		
		// Username
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
						) 
				),
				'errorMessages' => array (
						'Invalid Username' 
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
		
		// Customer Name
		$customer_name = $this->createElement ( "text", "name", array (
				'label' => 'Customer Name:',
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
						'2' => 'InActive' 
				),
				'validators' => array (
						'NotEmpty' 
				),
				'Required' => true 
		) );
		$this->addElement ( $status );
		
		// --------------------------------
		// Customer Information Section
		// --------------------------------
		
		// Customer Configuration ID
		$customer_configuration_id = $this->createElement ( "hidden", "customer_configuration_id", array (
				'value' => '',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement($customer_configuration_id);
		
		// Font Type
		$font_type = $this->createElement("text", "font_type" , array(
				'label' => 'Font Type:',
				'size' => '90',
				'filters' => array (
						'StringTrim'
				)
		)); 
		$this->addElement($font_type);
		
		// Font Color
		$font_color = $this->createElement("text", "font_color", array(
				'label' => 'Font Color:',
				'size' => '15',
				'filters' => array (
						'StringTrim'
				)
		));
		$this->addElement($font_color);
		
		// Font Size
		$font_size = $this->createElement("text", "font_size", array(
				'label' => 'Font Size:',
				'size' => '15',
				'filters' => array (
						'StringTrim'
				)
		));
		$this->addElement($font_size);
		
		// Spacing
		$spacing = $this->createElement("text", "spacing", array(
				'label' => 'Spacing:',
				'size' => '15',
				'filters' => array (
						'StringTrim'
				)
		)); 
		$this->addElement($spacing);
		
		//------------------------------
		
		
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
}