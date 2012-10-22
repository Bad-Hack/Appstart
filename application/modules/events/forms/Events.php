<?php
class Events_Form_Events extends Zend_Form {
	public function init() {
		$this->setMethod ( 'POST' );
		
		$notEmptyValidator = new Zend_Validate_NotEmpty ();
		$notEmptyValidator->setMessage ( 'Enter Valid Value For The Field.' );
		
		// Module Events ID
		$module_events_id = $this->createElement ( "hidden", "module_events_id", array (
				'value' => '',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $module_events_id);
		
		// Module Events Detail ID
		$module_events_detail_id = $this->createElement ( "hidden", "module_events_detail_id", array (
				'value' => '',
				'filters' => array (
						'StringTrim'
				)
		) );
		$this->addElement ( $module_events_detail_id);
		
		// Language ID
		$language_id = $this->createElement ( "hidden", "language_id", array (
				'value' => '',
				'filters' => array (
						'StringTrim'
				)
		) );
		$this->addElement ( $language_id );
		
		// Title
		$title = $this->createElement ( "text", "title", array (
				'label' => 'Title:',
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
						'Invalid Title' 
				) 
		) );
		$title->setAttrib("required", "required");
		$this->addElement ( $title);
		
		// Description
		$description = $this->createElement ( "textarea", "description", array (
				'label' => 'Description:',
				'size' => '128',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $description );
		
		// Start Date/Time
		$startDate = $this->createElement ( "text", "start_date_time", array (
				'label' => 'Start Date/Time:',
				'size' => '30',
				'filters' => array (
						'StringTrim'
				)
		) );
		$this->addElement ( $startDate);
		
		// End Date/Time
		$endDate = $this->createElement ( "text", "end_date_time", array (
				'label' => 'End Date/Time:',
				'size' => '30',
				'filters' => array (
						'StringTrim'
				)
		) );
		$this->addElement ( $endDate);
		
		// image
		
		$image = $this->createElement('file','image');
		$image->setLabel('Image:')
			 ->setDestination(Standard_Functions::getResourcePath(). "events/images")
			 ->addValidator('Size', false, 102400)
			 ->addValidator('Extension', false, 'jpg,png,gif');
		$this->addElement($image);
		
		$this->addElement('checkbox', 'status', array(
				'label'      => 'Active',
				'value'      => '1'
		));
		
		// Submit button
		$submit = $this->addElement ( 'submit', 'submit', array (
				'ignore' => true,
				'class' => "button" 
		) );
		
		// REset button
		$reset = $this->addElement ( 'reset', 'reset', array (
				'ignore' => true,
				'class' => "button"
		) );
		$this->addElements ( array (
				$submit,
				$reset
		) );
	}
}