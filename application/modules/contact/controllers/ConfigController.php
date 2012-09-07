<?php

class Contact_ConfigController extends Zend_Controller_Action
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
    	$sql = "CREATE TABLE IF NOT EXISTS contact (
    				`contact_id`  int NULL AUTO_INCREMENT ,
					`customer_id`  int NULL ,
					`location`  varchar(50) NULL ,
					`address`  varchar(90) NULL ,
					`phone_1`  varchar(20) NULL ,
					`phone_2`  varchar(20) NULL ,
					`phone_3`  varchar(20) NULL ,
					`fax`  varchar(20) NULL ,
					`latitude`  varchar(15) NULL ,
					`longitude`  varchar(15) NULL ,
					`email_1`  varchar(60) NULL ,
					`email_2`  varchar(60) NULL ,
					`email_3`  varchar(60) NULL ,
					`website`  varchar(60) NULL ,
					`timings`  varchar(30) NULL ,
					`order`  tinyint NULL ,
					`status`  tinyint NULL ,
					`logo`  varchar(120) NULL ,
					`last_updated_by`  int NULL ,
					`last_updated_at`  datetime NULL ,
					`created_by`  int NULL ,
					`created_at`  datetime NULL ,
					PRIMARY KEY (`contact_id`)
    			) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;";
    	try {
    		$db->query($sql);
    		
    		// Create Resource Dir
    		mkdir(Standard_Functions::getResourcePath()."contact",0755);
    		mkdir(Standard_Functions::getResourcePath()."contact/images",0755);
    		mkdir(Standard_Functions::getResourcePath()."contact/uploads",0755);
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

