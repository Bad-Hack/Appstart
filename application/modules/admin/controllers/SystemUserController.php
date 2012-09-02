<?php

class Admin_SystemUserController extends Zend_Controller_Action
{

    public function init()
    {
		/* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
		
    }
    
    public function addAction()
    {
    	// action body
    	$form = new Admin_Form_SystemUser();
    	foreach ($form->getElements() as $element) {
    		if($element->getDecorator('Label')) $element->getDecorator('Label')->setTag(null);
    	}
    	$this->view->form = $form;
    	$this->view->assign(array("partial"=>"system-user/partials/add.phtml"));
    	$this->render("add-edit");
    }
    
    public function editAction()
    {
    	$form = new Admin_Form_SystemUser();
    	$request = $this->getRequest();
    	if($request->getParam("id","")!="")
    	{
	    	$mapper = new Admin_Model_Mapper_SystemUser();
	    	$data = $mapper->find($request->getParam("id",""))->toArray();
	    	
	    	$data["confirm_password"] = $data["password"];
			$form->populate($data);
			$this->view->password = $data["password"];
	    	foreach ($form->getElements() as $element) {
	    		if($element->getDecorator('Label')) $element->getDecorator('Label')->setTag(null);
	    	}
    	}
    	$this->view->form = $form;
    	$this->view->assign(array("partial"=>"system-user/partials/edit.phtml"));
    	$this->render("add-edit");
    }
    
    public function saveAction()
    {
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ();
    	$form = new Admin_Form_SystemUser();
    	$request = $this->getRequest();
    	$msg = "Error";
    	$error = true;
    	if ($this->getRequest()->isPost()) {
    		if($form->isValid($request->getPost()))
    		{
    			// Save Record In DB
	    		try {
    				$auth = Zend_Auth::getInstance();
	    			$activeUser = $auth->getStorage ()->read ()->system_user_id;
	    			
	    			$model = new Admin_Model_SystemUser();
	    			
	    			if($request->getParam("system_user_id","")!="") {
	    				$model->setSystemUserId($request->getParam("system_user_id"));
	    			}
	    			else {
	    				$model->setCreatedBy($activeUser);
	    				$model->setCreatedAt(date("Y-m-d h:i:s"));
	    			}
	    			
	    			$model->setEmail($request->getParam("email"));
	    			$model->setPassword($request->getParam("password"));
	    			$model->setRole($request->getParam("role"));
	    			$model->setLastUpdatedBy($activeUser);
	    			$model->setLastUpdatedAt(date("Y-m-d h:i:s"));
	    			$model->save();
	    			
	    			$msg = "Record save successfully";
	    			$error = false;
	    		}
	    		catch (Exception $e) {
	    			$msg = "Error: [".$e->getCode()."] ".$e->getMessage()."";
	    		}
    		}
    		else {
    			// Invalid Request
    			$msg = "Please verify your information";
    		}
    	}
    	
    	$response["error"] = $error;
    	$response["message"] = $msg;
    	$jsonResponse = Zend_Json::encode($response);
    	$this->_response->appendBody($jsonResponse);
    }
    
    public function gridAction()
    {
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ();
    	
    	$mapper = new Admin_Model_Mapper_SystemUser();
    	
    	$response = $mapper->getDataTableList(array(
    			'column' => array (
    					'id' => array('actions'),
    					'replace' => array(
    							'role' => array (
    									'1' => 'Administrator',
    									'2' => 'User'
    							),
    							'email' => array (
    									'admin@aksystems.com' => "Super Admin",
    									'dharmesh@aksystems.com' => 'Super Super Admin'
    							)
    					),
    					'ignore' => array("actions")
    			)
    	));
    	$jsonGrid = Zend_Json::encode($response);
    	$this->_response->appendBody($jsonGrid);
    }
}
