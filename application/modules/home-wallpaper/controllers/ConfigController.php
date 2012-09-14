<?php

class HomeWallpaper_ConfigController extends Zend_Controller_Action
{

    public function init()
    {
		/* Initialize action controller here */
    }

    public function installAction() {
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ( true );
    	
    	$db = Standard_Functions::getDefaultDbAdapter();
    	
    	// Create Table Contact If Not Exsist
    	$sql = "CREATE TABLE IF NOT EXISTS tbl_Module_Home_Wallpaper (
    				`home_wallpaper_id`  int NULL AUTO_INCREMENT ,
					`customer_id`  int NULL ,
                    `image_title`  varchar(255) NULL ,
					`image_path_ipad`  varchar(255) NULL ,
					`image_path_iphone`  varchar(255) NULL ,
					`image_path_android`  varchar(255) NULL ,
					`image_path_ipad3`  varchar(255) NULL ,
					`link_to_module`  varchar(255) NULL ,
					`order` tinyint unique NULL ,
					`status`  tinyint NULL ,
					`last_updated_by`  int NULL ,
					`last_updated_at`  datetime NULL ,
					`created_by`  int NULL ,
					`created_at`  datetime NULL ,
					PRIMARY KEY (`home_wallpaper_id`)
    			) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;";
    	try {
    		$db->query($sql);
    		
    		// Create Resource Dir
    		mkdir(Standard_Functions::getResourcePath()."home-wallpaper",0777);
    		mkdir(Standard_Functions::getResourcePath()."home-wallpaper/images",0777);
            mkdir(Standard_Functions::getResourcePath()."home-wallpaper/images/ipad",0777);
            mkdir(Standard_Functions::getResourcePath()."home-wallpaper/images/iphone",0777);
            mkdir(Standard_Functions::getResourcePath()."home-wallpaper/images/android",0777);
            mkdir(Standard_Functions::getResourcePath()."home-wallpaper/images/ipad3",0777);
    		echo "Success";
    	}
    	catch (Exception $ex) {
    		echo $ex->getMessage();
    	}
    } 
    public function indexAction()
    {
        // action body
		
    }


}

