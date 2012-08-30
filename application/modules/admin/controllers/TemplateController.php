<?php

class Admin_TemplateController extends Zend_Controller_Action
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
    	$form = new Admin_Form_Template();
    	foreach ($form->getElements() as $element) {
    		if($element->getDecorator('Label')) $element->getDecorator('Label')->setTag(null);
    	}
    	$this->view->form = $form;
    }
    
    public function editAction()
    {
    	$form = new Admin_Form_Template();
    	$request = $this->getRequest();
    	if($request->getParam("id","")!="")
    	{ 
	    	$mapper = new Admin_Model_Mapper_BusinessType();
	    	$data = $mapper->find($request->getParam("id",""))->toArray();
	    	$form->populate($data);
	    	
	    	foreach ($form->getElements() as $element) {
	    		if($element->getDecorator('Label')) $element->getDecorator('Label')->setTag(null);
	    	}
    	}
    	$this->view->form = $form;
    }
    
    public function saveAction()
    {
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ();
    	$form = new Admin_Form_SystemUser();
    	$request = $this->getRequest();
    	$error = true;
    	$msg = "";
    	if ($this->getRequest()->isPost()) {
    		if($form->isValid($request->getPost()))
    		{
    			// Save Record In DB
    			try {
    				$auth = Zend_Auth::getInstance();
    				$activeUser = $auth->getStorage ()->read ()->user_id;
    				
    				$template = new Admin_Model_Template();
    				$template_id = $request->getParam("template_id","");
    				if($template_id!="") {
    					$template->setTemplateId($template_id);
    					
    					// Save Modules
    					$modules = $request->getParam("modules");
    					foreach($modules as $key => $value) {
    						// Insert In Template Modules
    						$mapper = new Admin_Model_Mapper_TemplateModule();
    						$model = $mapper->findByTemplateAndModuleId($template_id, $value);
    						
    						$model->setStatus(1);
    						$model->setLastUpdatedBy($activeUser);
    						$model->setLastUpdatedAt(date("Y-m-d h:i:s"));
    						
    						$model->save();
    					}
    					
    					// Inactivate Removed Modules
    					
    					$mapper = new Admin_Model_Mapper_TemplateModule();
    					$models = $mapper->fetchAll("template_id=".$template_id);
    					foreach ($models as $model)
    					{
    						if(!in_array($model->getModuleId(), $modules)) {
    							$model->setStatus(0);
    							$model->setLastUpdatedBy($activeUser);
    							$model->setLastUpdatedAt(date("Y-m-d h:i:s"));
    							
    							$model->save();
    						}    							
    					}
    				}
    				else {
    					$template->setCreatedBy($activeUser);
    					$template->setCreatedAt(date("Y-m-d h:i:s"));
    					
    					$modules = $request->getParam("modules");
    					foreach($modules as $key => $value) {
	    					$model = new Admin_Model_TemplateModule();
	    					$model->setTemplate($template_id);
	    					$model->setModule($value);
	    						
	    					$model->setLastUpdatedBy($activeUser);
	    					$model->setLastUpdatedAt(date("Y-m-d h:i:s"));
	    						
	    					$model->setCreatedBy($activeUser);
	    					$model->setCreatedAt(date("Y-m-d h:i:s"));
	    					$model->save();
    					}
    				}
    				
    				$template->setName($request->getParam("name"));
    				$template->setBusinessTypeId($request->getParam("business_type_id"));
    				$template->setStatus($request->getParam("status"));
    				$template->setLastUpdatedBy($activeUser);
    				$template->setLastUpdatedAt(date("Y-m-d h:i:s"));
    				
    				$template->save();
    				
    				$error = false;
    				$msg = "Record save successfully";
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
    
    public function deleteAction()
    {
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ();
    	
    }
    
    public function gridAction()
    {
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ();
    	
    }
}

