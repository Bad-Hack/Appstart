<?php

class Default_ConfigurationController extends Zend_Controller_Action
{

    public function init()
    {
		/* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    	$form = new Default_Form_GeneralConfiguration();
    	$moduleForm = new Default_Form_CustomerModule();
    	
    	$customer_id = Standard_Functions::getCurrentUser ()->customer_id;
    	$configMapper = new Admin_Model_Mapper_CustomerConfiguration();
    	$config = $configMapper->fetchAll("customer_id=".$customer_id);
    	if($config) {
    		$config = $config[0];
    		$form->populate($config->toArray());
    	} else {
    		$form->populate(array(
    			"customer_id" => $customer_id
    			));
    	}
    	$this->view->generalConfigurationForm = $form;
    	$this->view->moduleForm = $moduleForm;
    }
    
    public function saveGeneralConfigurationAction() {
    	$form = new Default_Form_GeneralConfiguration();
    	
    	$response = array();
    	if($this->_request->isPost()){
    		if($form->isValid($this->_request->getParams())){
    			$request = $this->getRequest ();
    			$config = new Admin_Model_CustomerConfiguration();
    			$config->setOptions($request->getParams());
    			$config->setLastUpdatedBy ( Standard_Functions::getCurrentUser ()->user_id );
    			$config->setLastUpdatedAt ( Standard_Functions::getCurrentDateTime () );
    			
    			if($request->getParam ( "customer_configuration_id", "") == "") {
    				$config->setCreatedBy ( Standard_Functions::getCurrentUser ()->user_id );
    				$config->setCreatedAt ( Standard_Functions::getCurrentDateTime () );
    			}
    			$config->save();
    			$response = array(
    					'success' => array(
    							'message' => $config->toArray()
    					)
    			);
    		} else {
    			$errors = $form->getMessages ();
    			
    			foreach ( $errors as $name => $error ) {
    				$errors [$name] = array_pop ( $error );
    			}
    			$response = array (
    					"errors" => $errors
    			);
    		}
    		$this->_helper->json ( $response );
    	}
    }
    
    public function gridAction() {
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ();
    	
    	$active_lang_id = Standard_Functions::getCurrentUser ()->active_language_id;
    	$default_lang_id = Standard_Functions::getCurrentUser ()->default_language_id;
    	$mapper = new Admin_Model_Mapper_CustomerModule();
    	
    	$select = $mapper->getDbTable ()
    					->select ( false )
    					->setIntegrityCheck ( false )
    					->from ( array ("cm" => "customer_module"),
    								array ("cm_customer_module_id"=>"cm.customer_module_id",
			    							"cm.module_id",
			    							"cm_visibility" => "cm.visibility",
    										"cm.customer_id",
    										"cm_order_number" => "cm.order_number",
    										"cm_status"=>"cm.status",
    										"cm_is_publish"=>"cm.is_publish"
			    							) )
			    		->joinLeft(array("m"=>"module"), "m.module_id=cm.module_id",array("m_description"=>"m.description"))
    					->joinLeft ( array ("d" => "customer_module_detail"), 
    									"d.customer_module_id=cm.customer_module_id AND d.language_id = ".$active_lang_id,
    									array (
    											"d_customer_module_detail_id"=>"d.customer_module_detail_id",
    											"d_language_id"=>"d.language_id",
    											"d_screen_name" => "d.screen_name",
    											"d_background_image"=>"d.background_image"
    										) )
    					->where("m.status=1 AND cm.status=1 AND cm.customer_id=".Standard_Functions::getCurrentUser ()->customer_id);
    							
    	$response = $mapper->getGridData(array (
    			'column' => array (
    					'id' => array (
    							'actions'
    					),
						'replace' => array (
								'cm_visibility' => array (
										'1' => 'Yes',
										'0' => 'No' 
								) 
						))
    	),null,$select);
    	
    	$mapper = new Admin_Model_Mapper_CustomerLanguage();
    	 
    	$select = $mapper->getDbTable ()->
				    	select ( false )->
				    	setIntegrityCheck ( false )->
				    	from ( array ("l" => "language"), array (
				    			"l.language_id" => "language_id",
				    			"l.title" => "title",
				    			"logo" => "logo") )->
		    			joinLeft ( array ("cl" => "customer_language"), "l.language_id = cl.language_id",
		    					array ("cl.customer_id") )->
    					where("cl.customer_id=".Standard_Functions::getCurrentUser ()->customer_id);
    	$languages = $mapper->getDbTable ()->fetchAll($select)->toArray();
    			
    	$rows = $response ['aaData'];
    	
    	foreach ( $rows as $rowId => $row ) {
    		if($row [5] ["d_customer_module_detail_id"]=="") {
    			$mapper = new Admin_Model_Mapper_CustomerModuleDetail();
    			$details = $mapper->fetchAll("customer_module_id=".$row [5] ["cm_customer_module_id"]." AND language_id=".$default_lang_id);
    			if(is_array($details)) {
    				$details = $details[0];
    				$row [5] ["d_customer_module_detail_id"] = $details->getCustomerModuleDetailId();
    				$row [5] ["d_language_id"] = $details->getLanguageId();
    				$row [5] ["d_screen_name"] = $row[1] = $details->getScreenName();
    				$row [5] ["d_background_image"] = $details->getBackgroundImage();
    			}
    		}
    		$edit = array();
    		if($languages) {
    			foreach ($languages as $lang) {
    				$editUrl = $this->view->url ( array (
    						"module" => "default",
    						"controller" => "configuration",
    						"action" => "edit",
    						"id" => $row [5] ["cm_customer_module_id"],
    						"lang" => $lang["l.language_id"]
    				), "default", true );
    				$edit[] = '<a href="'. $editUrl .'"  class="edit"><img src="images/lang/'.$lang["logo"].'" alt="'.$lang["l.title"].'" /></a>';
    			}
    		}
    		
    		$deleteUrl = $this->view->url ( array (
    				"module" => "default",
    				"controller" => "configuration",
    				"action" => "publish",
    				"id" => $row [5] ["cm_customer_module_id"]
    		), "default", true );
    		$defaultEdit = '<div id="editLanguage">&nbsp;<div class="flag-list">'.implode("",$edit).'</div></div>';
    		$delete = '<a href="' . $deleteUrl . '" class="grid_publish" >'.$this->view->translate('Publish').'</a>';
    		$sap = '';
    			
    		$response ['aaData'] [$rowId] [5] = $defaultEdit. $sap .$delete;
    	}
    	
    	$jsonGrid = Zend_Json::encode ( $response );
    	$this->_response->appendBody ( $jsonGrid );
    }
    
    public function publishAction() {
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ();
    	$moduleId = $this->_request->getParam ( "id", "" );
    	$response = array ();
    	$mapper = new Admin_Model_Mapper_CustomerModule();
    	$mapper->getDbTable()->getAdapter()->beginTransaction();
    	try {
    		$model = $mapper->find($moduleId);
    		if($model) {
    			$model->setSyncDateTime(Standard_Functions::getCurrentDateTime());
    			$model->setIsPublish("YES");
    			$model->save();
    			$response = array (
    					"success" => array (
    							"published_rows" => 1
    					)
    			);
    			$mapper->getDbTable()->getAdapter()->commit();
    		} else {
    			$response = array (
    					"errors" => "Nothing to publish"
    			);
    		}
    	} catch(Exception $ex) {
    		$mapper->getDbTable()->getAdapter()->rollBack();
    		$response = array (
    				"errors" => $ex->getMessage()
    		);
    	}
    	$jsonGrid = Zend_Json::encode ( $response );
    	$this->_response->appendBody ( $jsonGrid );
    }

    public function editAction() {
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ();
    	
    	$lang_id = $this->_request->getParam ( "lang", "" );
    	$customer_module_id = $this->_request->getParam ( "id", "" );
    	$default_lang_id = Standard_Functions::getCurrentUser ()->default_language_id;
    	$mapper = new Admin_Model_Mapper_CustomerModule();
    	
    	try {
	    	$select = $mapper->getDbTable ()
					    	->select ( false )
					    	->setIntegrityCheck ( false )
					    	->from ( array ("cm" => "customer_module"),
					    			array ("cm_customer_module_id"=>"cm.customer_module_id",
					    					"cm_visibility" => "cm.visibility",
					    					"cm.customer_id",
					    					"cm_order_number" => "cm.order_number",
					    					"cm_icon" => "cm.icon",
					    					"cm_status"=>"cm.status",
					    					"cm_is_publish"=>"cm.is_publish"
					    			) )
			    			->joinLeft ( array ("d" => "customer_module_detail"),
			    					"d.customer_module_id=cm.customer_module_id AND d.language_id = ".$lang_id,
			    					array (
			    							"d_customer_module_detail_id"=>"d.customer_module_detail_id",
			    							"d_language_id"=>"d.language_id",
			    							"d_screen_name" => "d.screen_name",
			    							"d_background_image"=>"d.background_image"
			    					) )
	    					->where("cm.customer_module_id=".$customer_module_id);
	    	$response = $mapper->getDbTable()->fetchAll($select)->toArray();
	    	if(!$response) {
	    		$select = $mapper->getDbTable ()
					    		->select ( false )
					    		->setIntegrityCheck ( false )
					    		->from ( array ("cm" => "customer_module"),
					    				array ("cm_customer_module_id"=>"cm.customer_module_id",
					    					"cm_visibility" => "cm.visibility",
					    					"cm.customer_id",
					    					"cm_order_number" => "cm.order_number",
					    					"cm_icon" => "cm.icon",
					    					"cm_status"=>"cm.status",
					    					"cm_is_publish"=>"cm.is_publish"
					    				) )
					    		->joinLeft ( array ("d" => "customer_module_detail"),
					    						"d.customer_module_id=cm.customer_module_id AND d.language_id = ".$default_lang_id,
					    						array (
				    							"d_customer_module_detail_id"=>"d.customer_module_detail_id",
				    							"d_language_id"=>"d.language_id",
				    							"d_screen_name" => "d.screen_name",
				    							"d_background_image"=>"d.background_image"
			    							) )
	    						->where("cm.customer_module_id=".$customer_module_id);
	    		$response = $mapper->getDbTable()->fetchAll($select)->toArray();
	    	}
	    	if($response) {
	    		$response = $response[0];
	    		$response = array (
	    				"success" => $response
	    		);
	    	} else {
	    		$response = array (
	    				"errors" => "Unable to edit record"
	    		);
	    	}
    	} catch (Exception $ex) {
    		$response = array (
    				"errors" => $ex->getMessage()
    		);
    	}
    	$jsonGrid = Zend_Json::encode ( $response );
    	$this->_response->appendBody ( $jsonGrid );
    }

	public function uploadAction() {
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ();
		
		$form = new Default_Form_CustomerModule ();
		$request = $this->getRequest ();
		$response = array ();
		if ($request->getParam ( "upload", "" ) != "") {
			$element = $request->getParam ( "upload" );
			$adapter = new Zend_File_Transfer_Adapter_Http ();
			$adapter->setDestination ( Standard_Functions::getResourcePath () . "default/images/" . str_replace ( "_image", "", $element ) );
			$adapter->receive ();
				
			if ($adapter->getFileName ( $element ) != "") {
				$response = array (
						"success" => array_pop ( explode ( '\\', $adapter->getFileName ( $element ) ) )
				);
			} else {
				$response = array (
						"errors" => "Error Occured"
				);
			}
			return $response;
		}
		echo Zend_Json::encode ( $response );
		exit;
	}
	
	public function saveModuleAction() {
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ();
		
		$form = new Default_Form_CustomerModule ();
		$request = $this->getRequest ();
		$response = array ();
		if ($this->_request->isPost ()) {
			$form->removeElement ( "icon" );
			$form->removeElement ( "background_image" );
			
			if ($form->isValid ( $this->_request->getParams () )) {
				$mapper = new Admin_Model_Mapper_CustomerModule();
				$mapper->getDbTable()->getAdapter()->beginTransaction();
				try {
					$allFormValues = $form->getValues ();
					$customerId = Standard_Functions::getCurrentUser ()->customer_id;
					$default_lang_id = Standard_Functions::getCurrentUser ()->default_language_id;
					$customer_module_id = $request->getParam("customer_module_id");
					$language_id = $request->getParam("language_id");
					
					$detailsMapper = new Admin_Model_Mapper_CustomerModuleDetail();
					$details = $detailsMapper->fetchAll("customer_module_id = ".$customer_module_id." AND language_id=".$language_id);
					if(!$details) {
						$details = $detailsMapper->fetchAll("customer_module_id = ".$customer_module_id." AND language_id=".$default_lang_id);
						$details = $details[0];
						$details->setCustomerModuleDetailId("");
						$details->setLanguageId($language_id);
					} else {
						$details = $details[0];
					}
					$details->setScreenName($request->getParam("screen_name"));
					if($request->getParam("background_image_path","")!="") {
						$details->setBackgroundImage("background/".$request->getParam("background_image_path"));
					}
					$details->save();
					
					$model = $mapper->find($customer_module_id);
					if($request->getParam("icon_path","") != "") {
						$model->setIcon("icon/".$request->getParam("icon_path"));
					}
					$model->setVisibility($request->getParam("visibility","0"));
					$model->save();
					$mapper->getDbTable ()->getAdapter ()->commit ();
					
					$response = array (
							"success" => 1
					);
					
				} catch (Exception $ex) {
					$mapper->getDbTable ()->getAdapter ()->rollBack ();
					$response = array (
							"errors" => $ex->getMessage ()
					);
				}
			} else {
				$errors = $form->getMessages ();
				foreach ( $errors as $name => $error ) {
					$errors [$name] = $error [0];
				}
				$response = array (
						"errors" => $errors
				);
			}
		}
		
		echo Zend_Json::encode ( $response );
		exit;
	}
}