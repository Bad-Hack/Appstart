<?php
class Default_Form_CustomerModule extends Zend_Form {
	public function init() {
		$this->setMethod ( 'POST' );
		
		$notEmptyValidator = new Zend_Validate_NotEmpty ();
		$notEmptyValidator->setMessage ( 'Enter Valid Value For The Field.' );
		
		// Customer Module ID
		$customer_module_id = $this->createElement ( "hidden", "customer_module_id", array (
				'value' => '',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $customer_module_id);
		
		// Language ID
		$language_id = $this->createElement ( "hidden", "language_id", array (
				'value' => '',
				'filters' => array (
						'StringTrim'
				)
		) );
		$this->addElement ( $language_id);
		
		// Screen Name
		$screen_name = $this->createElement ( "text", "screen_name", array (
				'label' => 'Screen Name:',
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
						'Invalid Screen Name' 
				) 
		) );
		$screen_name->setAttrib("required", "required");
		$this->addElement ( $screen_name);
		
		// Icon
		$icon = $this->createElement('file','icon');
		$icon->setLabel('Icon:')
			 ->setDestination(Standard_Functions::getResourcePath(). "default/images/icon")
			 ->addValidator('Size', false, 102400)
			 ->addValidator('Extension', false, 'jpg,png,gif');
		$this->addElement($icon);
		
		// Background
		$background = $this->createElement('file','background_image');
		$background->setLabel('Background:')
				->setDestination(Standard_Functions::getResourcePath(). "default/images/background")
				->addValidator('Size', false, 102400)
				->addValidator('Extension', false, 'jpg,png,gif');
		$this->addElement($background);
		
		$this->addElement('checkbox', 'visibility', array(
				'label'      => 'Visible',
				'value'      => '1'
		));
		
		// Submit button
		$submit = $this->addElement ( 'submit', 'submit', array (
				'ignore' => true,
				'class' => "button" 
		) );
		
		// REset button
		$reset = $this->addElement ( 'reset', 'cancel', array (
				'ignore' => true,
				'class' => "button",
				'onclick' => "hideForm();"
		) );
		$this->addElements ( array (
				$submit,
				$reset
		) );
	}
}