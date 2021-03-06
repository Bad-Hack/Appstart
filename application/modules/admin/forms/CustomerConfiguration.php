<?php
class Admin_Form_CustomerConfiguration extends Zend_Form {
	public function init() {
		$this->setMethod ( 'POST' );
		
		// Customer Configuration ID
		$customer_configuration_id = $this->createElement ( "hidden", "customer_configuration_id", array (
				'value' => '',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $customer_configuration_id );
		
		// Customer  ID
		$customer_id = $this->createElement ( "hidden", "customer_id", array (
				'value' => '',
				'filters' => array (
						'StringTrim'
				)
		) );
		$this->addElement ( $customer_id );
		
		// Font Type
		$font_type = $this->createElement ( "text", "font_type", array (
				'label' => 'Font Type:',
				'size' => '90',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $font_type );
		
		// Font Color
		$font_color = $this->createElement ( "text", "font_color", array (
				'label' => 'Font Color:',
				'size' => '15',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $font_color );
		
		// Font Size
		$font_size = $this->createElement ( "text", "font_size", array (
				'label' => 'Font Size:',
				'size' => '15',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $font_size );
		
		// Spacing
		$spacing = $this->createElement ( "text", "spacing", array (
				'label' => 'Spacing:',
				'size' => '15',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $spacing );
		
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
}