<?php

class Wallpaper_Form_Wallpaper extends Zend_Form 
{	
	public function init()
	{
		$this->setMethod('post');   
 		$this->addElement('hidden', 'wallpaper_id', array(
				'value'		 => '',
				'filters'    => array('StringTrim')
		));
	
		$notEmpty = new Zend_Validate_NotEmpty();
		
		$order = new Zend_Form_Element_Text("order");
		$order->setAttrib('required', 'required')
			  ->setLabel("Order:")
			  ->setAttrib('size', '35') 
			  ->setFilters(array("StringTrim"))
			  ->setRequired(true)
			  ->addErrorMessage("Enter Order");
		$this->addElement($order);
		
		 
 		$this->addElement('select', 'link_to_module', array(
				'label'      => 'Link to Module:', 
				'MultiOptions' => $this->_getModules(), 				
				'required'   => true,
				'filters'    => array('StringTrim'),
				'validators' => array(
						array($notEmpty,true)
				),
				'errorMessages' => array('Select Module')
		
		));
		
		$ipad = new Zend_Form_Element_File('image_ipad' ); 
				 $ipad->setRequired(true) 
				      ->setDestination(Standard_Functions::getResourcePath().'wallpaper/ipad')
					  ->addValidator('Extension',true,'jpg'); 
        $this->addElement($ipad); 
        
        $iphone = new Zend_Form_Element_File('image_iphone');
       		     $iphone->setRequired(true) 
       		     		->setDestination(Standard_Functions::getResourcePath().'wallpaper/iphone')
       		            ->addValidator('Extension',true,'jpg');
        $this->addElement($iphone);
        
        $ipad3 = new Zend_Form_Element_File('image_ipad_3');
          		 $ipad3->setRequired(true) 
          			   ->setDestination(Standard_Functions::getResourcePath().'wallpaper/ipad3')
        	    	   ->addValidator('Extension',true,'jpg');
        $this->addElement($ipad3);
        
        $android = new Zend_Form_Element_File('image_android');
         		$android->setRequired(true) 
         				->setDestination(Standard_Functions::getResourcePath().'wallpaper/android')
        				->addValidator('Extension',true,'jpg');
        $this->addElement($android);
		
		
		$this->addElement('checkbox', 'status', array(
				'label'      => 'Active',
				'value'      => '0'
		));
		
		// Add the submit button
		$this->addElement('submit', 'submit', array(
				'ignore'   => true,
				'class' => "button"  
		));
		
		// Add the reset button
		$this->addElement('reset', 'reset', array(
				'ignore'   => true,
				'class' => "button"
		)); 
		
		// Add the cancel button
		$this->addElement('button', 'cancel', array(
				'ignore'   => true,
				'Onclick' => 'javascript: history.go(-1)',
				'class' => "button"
		));
	  
	}	
	
	public function _getModules()
	{
		$options = array();
	
		$mapper = new Admin_Model_Mapper_Module();
		$models = $mapper->fetchAll();
		$options[""] = "- Select -";
		foreach($models as $module) {
			$options[$module->getModuleId()] = $module->getName();
		}
	
		return $options;
	}

}

?>