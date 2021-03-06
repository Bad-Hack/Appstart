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
    	$this->view->addlink = $this->view->url ( array (
						    			"module" => "admin",
						    			"controller" => "template",
						    			"action" => "add"
						    	), "default", true );
    }

    public function addAction()
    {
    	$form = new Admin_Form_Template();
    	foreach ($form->getElements() as $element) {
    		if($element->getDecorator('Label')) $element->getDecorator('Label')->setTag(null);
    	}
    	$this->view->hasData = true;
    	$mapper = new Admin_Model_Mapper_BusinessType ();
    	$models = $mapper->countAll();
    	$module = new Admin_Model_Mapper_Module();
    	$module = $module->countAll();
    	if($models == 0 || $module == 0) {
    		$this->view->hasData = false;
    	}
    	
    	$this->view->form = $form;
    	$this->view->assign ( array (
    			"partial" => "template/partials/add.phtml"
    	) );
    	$this->render ( "add-edit" );
    }
    
    public function editAction()
    {
    	$form = new Admin_Form_Template();
    	$request = $this->getRequest();
    	$this->view->hasData = true;
    	if($request->getParam("id","")!="")
    	{ 
	    	$mapper = new Admin_Model_Mapper_Template();
	    	$data = $mapper->find($request->getParam("id",""))->toArray();
	    	$form->populate($data);
	    	
	    	$mapper = new Admin_Model_Mapper_TemplateModule();
	    	$modules = $mapper->fetchAll("template_id=".$request->getParam("id",""));
	    	if($modules) {
		    	$values = array();
		    	foreach($modules as $module) {
		    		$values[] = $module->getModuleId();
		    	}
		    	$form->getElement("modules")->setValue($values);
	    	}
	    	foreach ($form->getElements() as $element) {
	    		if($element->getDecorator('Label')) $element->getDecorator('Label')->setTag(null);
	    	}
    	}
    	$this->view->form = $form;
    	$this->view->assign ( array (
    			"partial" => "template/partials/edit.phtml"
    	) );
    	$this->render ( "add-edit" );
    }
    
    public function saveAction()
    {
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ();
    	$form = new Admin_Form_Template();
    	$request = $this->getRequest();
    	$error = true;
    	$msg = "";
    	$templateMapper = new Admin_Model_Mapper_Template();
    	
    	if ($this->getRequest()->isPost()) {
    		if($form->isValid($request->getPost()))
    		{
    			// Save Record In DB
    			$templateMapper->getDbTable()->getAdapter()->beginTransaction();
    			try {
    				$template = new Admin_Model_Template();
    				$template_id = $request->getParam("template_id","");
    				if($template_id!="") {
    					$template->setTemplateId($template_id);
    					
    					// Save Modules
    					$modules = $request->getParam("modules");
    					
    					// Customer Module Changes START
	    					// Inactivate Removed Modules from Customer_Modules
	    					$customerModuleMapper = new Admin_Model_Mapper_CustomerModule();
	    					$customerModuleMapper->inactivateModulesByTemplateId($template_id,implode(",",$modules));
	    					
	    					// Activate Added Modules In Custoemr
	    					$customerModuleMapper->activateModulesByTemplateId($template_id,implode(",",$modules));
	    				// Customer Module Changes END
	    				
    					// Remove Modules From Template Module
    					$templateModuleMapper = new Admin_Model_Mapper_TemplateModule();
    					$templateModuleMapper->delete("template_id=".$template_id." AND module_id NOT IN (".implode(",", $modules).")");
    					
    					// Add New Modules To Template Modules
    					$templateModuleMapper->addModules($template_id,$modules);
    					
    				}
    				else {
    					$template->setCreatedBy(Standard_Functions::getCurrentUser ()->system_user_id);
    					$template->setCreatedAt(Standard_Functions::getCurrentDateTime ());
    				}
    				
    				$template->setName($request->getParam("name"));
    				$template->setBusinessTypeId($request->getParam("business_type_id"));
    				$template->setStatus($request->getParam("status"));
    				$template->setLastUpdatedBy(Standard_Functions::getCurrentUser ()->system_user_id);
    				$template->setLastUpdatedAt(Standard_Functions::getCurrentDateTime ());
    				
    				$id = $template->save();
    				
    				if($template_id=="") {
	    				$modules = $request->getParam("modules");
	    				foreach($modules as $key => $value) {
	    					$model = new Admin_Model_TemplateModule();
	    					$model->setTemplateId($id->getTemplateId());
	    					$model->setModuleId($value);
	    					$model->setLastUpdatedBy(Standard_Functions::getCurrentUser ()->system_user_id);
	    					$model->setLastUpdatedAt(Standard_Functions::getCurrentDateTime ());
	    					$model->setStatus(1);
	    					$model->setCreatedBy(Standard_Functions::getCurrentUser ()->system_user_id);
	    					$model->setCreatedAt(Standard_Functions::getCurrentDateTime ());
	    					$model->save();
	    				}
    				}
    				$error = false;
    				$msg = "Record save successfully";
    				$templateMapper->getDbTable()->getAdapter()->commit();
    			}
    			catch (Exception $e) {
    				$templateMapper->getDbTable()->getAdapter()->rollBack();
    				$msg = "Error: [".$e->getCode()."] ".$e->getMessage()."";
	    		}
    		}
    		else {
    			// Invalid Request
    			$error ="";
    			$messages = $form->getMessages();
    			foreach ($messages as $key=>$msg) {
    				$error .= "<br>".$key.": ";
    				if(is_array($msg)) {
    					foreach($msg as $m) {
    						$error .= $m."<br>";
    					}
    				} else {
    					$error .= $msg;
    				}
    			}
    			$msg = "Please verify your information: <br />".$error;
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
    	
    	$request = $this->getRequest ();
    	
    	if (($templateId = $request->getParam ( "id", "" )) != "") {
    		$template = new Admin_Model_Template ();
    		$template->populate ( $templateId );
    		if ($template) {
    			try {
    				$customerMapper = new Admin_Model_Mapper_Customer();
    				if($customerMapper->countAll("template_id=".$templateId)>0)
    				{
    					$response = array (
								"errors" => array (
										"message" => "Template is linked to one or more customer."
								)
						);
    				}
    				$deletedRows = $template->delete ();
    					
    				$response = array (
    						"success" => array (
    								"deleted_rows" => $deletedRows
    						)
    				);
    			} catch ( Exception $e ) {
    				$response = array (
    						"errors" => array (
    								"message" => $e->getMessage ()
    						)
    				);
    			}
    		} else {
    			$response = array (
    					"errors" => array (
    							"message" => "No user to delete."
    					)
    			);
    		}
    	} else {
    		$this->_redirect ( '/admin/template' );
    	}
    	
    	$this->_helper->json ( $response );
    }
    
    public function gridAction()
    {
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ();
    	
    	$mapper = new Admin_Model_Mapper_Template();
    	
    	$select = $mapper->getDbTable ()
				    	->select ( false )
				    	->setIntegrityCheck ( false )
				    	->from ( array ("t" => "template"),
				    			array ("t.template_id",
				    					"t_name" => "t.name",
				    					"t_status" => "t.status",
				    					"t_last_updated_at" => "t.last_updated_at" ) )
    					->joinLeft ( array ("bt" => "business_type"), "bt.business_type_id=t.business_type_id",
    							array ("business_type" => "bt.name") )
    					->joinLeft ( array ("tm" => "template_module"), "tm.template_id=t.template_id",
    								array ("total_modules" => "count(tm.template_module_id)") )
    					->group ( "t.template_id" );
    	
    	$response = $mapper->getGridData(array (
							'column' => array (
									'id' => array (
											'actions' 
									),
							'replace' => array (
									't_status' => array (
											'1' => 'Active',
											'0' => 'Inactive' 
									) 
							))
					),null,$select);
    	
    	$rows = $response ['aaData'];
    	foreach ( $rows as $rowId => $row ) {
    		$editUrl = $this->view->url ( array (
    				"module" => "admin",
    				"controller" => "template",
    				"action" => "edit",
    				"id" => $row [5] ["template_id"]
    		), "default", true );
    		$deleteUrl = $this->view->url ( array (
    				"module" => "admin",
    				"controller" => "template",
    				"action" => "delete",
    				"id" => $row [5] ["template_id"]
    		), "default", true );
    			
    		$edit = '<a href="' . $editUrl . '" class="grid_edit" >'.$this->view->translate('Edit').'</a>';
    		$delete = '<a href="' . $deleteUrl . '" class="grid_delete" >'.$this->view->translate('Delete').'</a>';
    		$sap = ($edit == "" || $delete == "") ? '' : '&nbsp;|&nbsp;';
    			
    		$response ['aaData'] [$rowId] [5] = $edit . $sap . $delete;
    	}
    	
    	$jsonGrid = Zend_Json::encode ( $response );
    	$this->_response->appendBody ( $jsonGrid );
    }
}