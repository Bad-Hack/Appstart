<?php
class HomeWallpaper_Form_HomeWallpaper extends Zend_Form {
	public function init() {
		$this->setMethod ( 'POST' );
		
		$notEmptyValidator = new Zend_Validate_NotEmpty ();
		$notEmptyValidator->setMessage ( 'Enter Valid Value For The Field.' );
		
		// home_wallpaper ID
		$home_wallpaper_id = $this->createElement ( "hidden", "home_wallpaper_id", array (
				'value' => '',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $home_wallpaper_id);
		
		// Iamge Title
		$image_title = $this->createElement ( "text", "image_title", array (
				'label' => 'Image Title:',
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
						'Invalid Image Title' 
				) 
		) );
		$image_title->setAttrib("required", "required");
		$this->addElement ( $image_title);

		//Image Path IPAD 
		$image_path_ipad = $this->createElement('file','image_ipad');
		$image_path_ipad->setLabel('iPad Image :')
			 ->setDestination(Standard_Functions::getResourcePath(). "home-wallpaper/tmp/images/ipad")
			 ->addValidator('Size', false, 102400)
			 ->addValidator('Extension', false, 'jpg,png,gif');
		$this->addElement($image_path_ipad);

		//image_path_iphone
		$image_path_iphone = $this->createElement('file','image_iphone');
		$image_path_iphone->setLabel('iPhone Image :')
			 ->setDestination(Standard_Functions::getResourcePath(). "home-wallpaper/tmp/images/iphone")
			 ->addValidator('Size', false, 102400)
			 ->addValidator('Extension', false, 'jpg,png,gif');
		$this->addElement($image_path_iphone);

		//image_path_android
		$image_path_android = $this->createElement('file','image_android');
		$image_path_android->setLabel('Android Image :')
			 ->setDestination(Standard_Functions::getResourcePath(). "home-wallpaper/tmp/images/android")
			 ->addValidator('Size', false, 102400)
			 ->addValidator('Extension', false, 'jpg,png,gif');
		$this->addElement($image_path_android);

		//image_path_ipad3
		$image_path_ipad3 = $this->createElement('file','image_ipad3');
		$image_path_ipad3->setLabel('iPad3 Image :')
			 ->setDestination(Standard_Functions::getResourcePath(). "home-wallpaper/tmp/images/ipad3")
			 ->addValidator('Size', false, 102400)
			 ->addValidator('Extension', false, 'jpg,png,gif');
		$this->addElement($image_path_ipad3);
		
		// link_to_module
		$link_to_module = $this->createElement ( "text", "link_to_module", array (
				'label' => 'Link to module:',
				'size' => '90',
				'filters' => array (
						'StringTrim' 
				) 
		) );
		$this->addElement ( $link_to_module );
		
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