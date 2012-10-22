<?php 
class PushMessage_Form_PushMessage extends Zend_Form{
	public function init(){
		$this->setMethod('POST');
		$notEmptyValidator = new Zend_Validate_NotEmpty ();
		$notEmptyValidator->setMessage ( 'Enter Valid Value For The Field.' );

		// Push Message ID
		$push_message_id = $this->createElement ( "hidden", "push_message_id", array (
				'value' => '',
				'filters' => array (
						'StringTrim'
				)
		) );
		$this->addElement ( $push_message_id);
		
		// Push Message Detail ID
		$push_message_detail_id = $this->createElement ( "hidden", "push_message_detail_id", array (
				'value' => '',
				'filters' => array (
						'StringTrim'
				)
		) );
		$this->addElement ( $push_message_detail_id);
		
		// Language ID
		$language_id = $this->createElement ( "hidden", "language_id", array (
				'filters' => array (
						'StringTrim'
				)
		) );
		$this->addElement ( $language_id );
		
		// Push Message Title
		$title = $this->createElement ( "text", "title", array (
				'label' => 'Push Message Title:',
				'size' => '64',
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
						'Invalid Image Title'
				)
		) );
		$title->setAttrib("required", "required");
		$this->addElement ($title);
		
		//Push Message Description
		$description = $this->createElement("textarea","description",array(
				'label' => 'Push Message Description:',
				'size' => '255',
				'required' => true,
				'filters' => array(
						'StringTrim'
				),
				'validators' => array(
						array(
								$notEmptyValidator,
								true
						)
				),
				'errorMessages' => array(
						'Invalid Message Description'
				)
		));
		$description->setAttrib("required", "required");
		$this->addElement($description);
		$this->addElement('checkbox', 'status', array(
				'label'      => 'Active',
				'value'      => '1'
		));
		
		// Submit button
		$submit = $this->addElement ( 'submit', 'submit', array (
				'ignore' => true,
				'class' => "button"
		) );
		
		// Reset button
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