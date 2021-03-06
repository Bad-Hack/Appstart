<?php
class Document_IndexController extends Zend_Controller_Action
{
	var $_module_id;
	
    public function init()
    {
		/* Initialize action controller here */
    	$modulesMapper = new Admin_Model_Mapper_Module();
    	$module = $modulesMapper->fetchAll("name ='document'");
    	if(is_array($module)) {
    		$this->_module_id = $module[0]->getModuleId();
    	}
    }

    public function indexAction()
    {
        // action body
    	$this->view->addlink = $this->view->url ( array (
					    			"module" => "document",
					    			"controller" => "index",
					    			"action" => "add"
					    	), "default", true );
    	$this->view->reorderlink = $this->view->url ( array (
    			"module" => "document",
    			"controller" => "index",
    			"action" => "reorder"
    	), "default", true );
    }
    
    public function addAction()
    {
    	// action body
    	$lang_id = Standard_Functions::getCurrentUser ()->default_language_id;
    	$language = new Admin_Model_Mapper_Language();
    	$lang = $language->find($lang_id);
    	$this->view->language = $lang->getTitle();
    	
    	$form = new Document_Form_Document();
    	foreach ( $form->getElements () as $element ) {
    		if ($element->getDecorator ( 'Label' ))
    			$element->getDecorator ( 'Label' )->setTag ( null );
    	}
    	$action = $this->view->url ( array (
				    			"module" => "document",
				    			"controller" => "index",
				    			"action" => "save"
				    	), "default", true );
    	$form->setAction($action);
    	$this->view->form = $form;
    	$this->view->document_path="";
    	
    	$this->view->assign ( array (
    			"partial" => "index/partials/add.phtml"
    	) );
    	$this->render ( "add-edit" );
    }
    
    public function editAction()
    {
    	$form = new Document_Form_Document();
    	foreach ( $form->getElements () as $element ) {
    		if ($element->getDecorator ( 'Label' ))
    			$element->getDecorator ( 'Label' )->setTag ( null );
    	}
    	
    	$request = $this->getRequest ();
    	if ($request->getParam ( "id", "" ) != "" && $request->getParam ( "lang", "" ) != "") {
    		$document_id = $request->getParam ( "id", "" );
    		$lang_id = $request->getParam ( "lang", "" );
    		
    		$language = new Admin_Model_Mapper_Language();
    		$lang = $language->find($lang_id);
    		$this->view->language = $lang->getTitle();
    		 
    		$default_lang_id = Standard_Functions::getCurrentUser ()->default_language_id;
    		
    		$mapper = new Document_Model_Mapper_ModuleDocument();
    		$data = $mapper->find ( $document_id )->toArray ();
    		$form->populate ( $data );
    		 
    		$dataDetails = array();
    		$details = new Document_Model_Mapper_ModuleDocumentDetail();
    		 
    		if($details->countAll("module_document_id = ".$document_id." AND language_id = ".$lang_id) > 0) {
    			// Record For Language Found
    			$dataDetails = $details->getDbTable()->fetchAll("module_document_id = ".$document_id." AND language_id = ".$lang_id)->toArray();
    		} else {
    			// Record For Language Not Found
    			$dataDetails = $details->getDbTable()->fetchAll("module_document_id = ".$document_id." AND language_id = ".$default_lang_id)->toArray();
    			$dataDetails[0]["module_document_detail_id"] = "";
    			$dataDetails[0]["language_id"] = $lang_id;
    		}
    		
    		if(isset($dataDetails[0]) && is_array($dataDetails[0])) {
    			$this->view->document_path = $dataDetails[0]["document_path"];
    			$form->populate ( $dataDetails[0] );
    		}
    		
    		$action = $this->view->url ( array (
    				"module" => "document",
    				"controller" => "index",
    				"action" => "save",
    				"id" => $request->getParam ( "id", "" )
    		), "default", true );
    		$form->setAction($action);
    	} else {
    		$this->_redirect ( '/' );
    	}
    	 
    	$this->view->form = $form;
    	$this->view->assign ( array (
    			"partial" => "index/partials/edit.phtml"
    	) );
    	$this->render ( "add-edit" );
    }
    
    public function saveAction()
    {
    	$form = new Document_Form_Document();
    	$request = $this->getRequest ();
    	$response = array ();
    	 
    	if ($this->_request->isPost ()) {
    		if($request->getParam ( "upload", "" ) != "") {
    			$adapter = new Zend_File_Transfer_Adapter_Http();
    			$adapter->setDestination(Standard_Functions::getResourcePath(). "document/uploads");
    			$adapter->receive();
    			if($adapter->getFileName("document")!="")
    			{
    				$response = array (
    						"success" => array_pop(explode('\\',$adapter->getFileName("document")))
    				);
    			} else {
    				$response = array (
    						"errors" => "Error Occured"
    				);
    			}
    		
    			echo Zend_Json::encode($response);
    			exit;
    		}
    		
    		$form->removeElement("document");
    		
    		if ($form->isValid ( $this->_request->getParams () )) {
    			$mapper = new Document_Model_Mapper_ModuleDocument();
    			$mapper->getDbTable()->getAdapter()->beginTransaction();
    			
    			try {
    				// Save Document
    				$arrFormValues = $form->getValues();
    				$customer_id = Standard_Functions::getCurrentUser ()->customer_id;
    				$user_id = Standard_Functions::getCurrentUser ()->user_id;
    				$date_time = Standard_Functions::getCurrentDateTime ();
    				$document_path = $request->getParam ("document_path", "");
    				$arrFormValues["document_path"] = $document_path;
    				$arrFormValues["size"] = filesize(Standard_Functions::getResourcePath(). "document/uploads/" . $document_path);
    				$type = strtoupper(array_pop(explode(".", $document_path)));
    				$arrFormValues["type"] = $type;
    				
    				$model = new Document_Model_ModuleDocument($arrFormValues);
    				
    				if($request->getParam ( "module_document_id", "" ) == "") {
    					// Add New
    					$maxOrder = $mapper->getNextOrder($customer_id);
    					
    					$model->setOrder($maxOrder+1);
    					$model->setCustomerId ( $customer_id );
    					$model->setCreatedBy ( $user_id );
    					$model->setCreatedAt ( $date_time );
    					$model->setLastUpdatedBy ( $user_id );
    					$model->setLastUpdatedAt ( $date_time );
    					$model = $model->save ();
    					
    					// Save Details
    					$document_id = $model->getModuleDocumentId();
    					$mapperLanguage = new Admin_Model_Mapper_CustomerLanguage();
    					$modelLanguages = $mapperLanguage->fetchAll("customer_id = ".$customer_id);
    					if(is_array($modelLanguages)) {
    						foreach($modelLanguages as $languages) {
    							$modelDetails = new Document_Model_ModuleDocumentDetail($arrFormValues);
    							$modelDetails->setModuleDocumentId($document_id);
    							$modelDetails->setLanguageId ($languages->getLanguageId());
    							$modelDetails->setCreatedBy ( $user_id );
    							$modelDetails->setCreatedAt ( $date_time );
    							$modelDetails->setLastUpdatedBy ( $user_id );
    							$modelDetails->setLastUpdatedAt ( $date_time );
    							$modelDetails = $modelDetails->save();
    						}
    					}
    					
    				} else {
    					// Update ExistingRecord
    					$model->setLastUpdatedBy ( $user_id );
    					$model->setLastUpdatedAt ( $date_time);
    					$model = $model->save ();
    					
    					$modelDetails = new Document_Model_ModuleDocumentDetail($arrFormValues);
    					if(!$modelDetails || $modelDetails->getModuleDocumentDetailId()=="") {
	    					$modelDetails->setCreatedBy ( $user_id );
	    					$modelDetails->setCreatedAt ( $date_time );
    					}
    					$modelDetails->setLastUpdatedBy ( $user_id );
    					$modelDetails->setLastUpdatedAt ( $date_time );
    					$modelDetails = $modelDetails->save();
    				}
    				
    				$mapper->getDbTable()->getAdapter()->commit();
    				if($model && $model->getModuleDocumentId()!="") {
    					$response = array (
    							"success" => $model->toArray ()
    					);
    				}
    			} catch (Exception $ex) {
    				$response = array (
    						"errors" => $ex->getMessage()
    				);
    				try {
    					$mapper->getDbTable()->getAdapter()->rollBack();
    				} catch (Exception $e) {}
    			}
    		} else {
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
    			$response = array (
    					"errors" => $error
    			);
    		}
    	}
    	$this->_helper->json ( $response );
    }
    
    public function deleteAction() {
    	 $this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ();
    	$request = $this->getRequest ();
    
    	if (($document_id = $request->getParam ( "id", "" )) != "") {
    		$document = new Document_Model_ModuleDocument();
    		$document->populate($document_id);
    		if($document) {
    			$mapper = new Document_Model_Mapper_ModuleDocument();
    			$mapper->getDbTable()->getAdapter()->beginTransaction();
    			try {
    				$detailsMapper = new Document_Model_Mapper_ModuleDocumentDetail();
    				$details = $detailsMapper->fetchAll("module_document_id=".$document->getModuleDocumentId());
    				if(is_array($details)) {
    					foreach($details as $documentDetail) {
    						$documentDetail->delete();
    					}
    				}
    				
    				$deletedRows = $document->delete ();
    				
    				// set is pulish to false
    				$customerId = Standard_Functions::getCurrentUser ()->customer_id;
    				$customermoduleMapper = new Admin_Model_Mapper_CustomerModule();
    				$customermodule = $customermoduleMapper->fetchAll("customer_id=".$customerId." AND module_id=".$this->_module_id);
    				if(is_array($customermodule)) {
    					$customermodule = $customermodule[0];
    					$customermodule->setIsPublish("NO");
    					$customermodule->save();
    				}
    				
    				$mapper->getDbTable()->getAdapter()->commit();
    				
    				$response = array (
    						"success" => array (
    								"deleted_rows" => $deletedRows
    						)
    				);
    				
    			} catch (Exception $ex) {
    				$mapper->getDbTable()->getAdapter()->rollBack();
    				$response = array (
    						"errors" => array (
    								"message" => $ex->getMessage ()
    						)
    				);
    			}
    		} else {
    			$response = array (
    					"errors" => array (
    							"message" => "No document to delete."
    					)
    			);
    		}
    	 } else {
    		$this->_redirect ( '/' );
    	}
    	
    	$this->_helper->json ( $response );
    }
    
    public function reorderAction() {
    	$active_lang_id = Standard_Functions::getCurrentUser ()->active_language_id;
    	$default_lang_id = Standard_Functions::getCurrentUser ()->default_language_id;
    	$customer_id = Standard_Functions::getCurrentUser ()->customer_id;
    	
    	if ($this->_request->isPost ()) {
    		$this->_helper->layout ()->disableLayout ();
    		$this->_helper->viewRenderer->setNoRender ();
    		
    		$user_id = Standard_Functions::getCurrentUser ()->user_id;
    		$date_time = Standard_Functions::getCurrentDateTime ();
    		$response = array();
    		
    		$order = $this->_request->getParam ("order");
    		
    		$mapper = new Document_Model_Mapper_ModuleDocument();
    		$mapper->getDbTable()->getAdapter()->beginTransaction();
    		try {
	    		foreach($order as $key=>$value) {
	    			$model = $mapper->find($value);
	    			$model->setOrder($key);
	    			$model->setLastUpdatedBy ( $user_id );
	    			$model->setLastUpdatedAt ( $date_time );
	    			$model->save();
	    		}
	    		
	    		// set is pulish to false
	    		$customermoduleMapper = new Admin_Model_Mapper_CustomerModule();
	    		$customermodule = $customermoduleMapper->fetchAll("customer_id=". $customer_id ." AND module_id=".$this->_module_id);
	    		if(is_array($customermodule)) {
	    			$customermodule = $customermodule[0];
	    			$customermodule->setIsPublish("NO");
	    			$customermodule->save();
	    		}
	    		
	    		$mapper->getDbTable()->getAdapter()->commit();
    			$response = array (
    					"success" => true
    			);
    		}catch(Exception $e) {
    			$mapper->getDbTable()->getAdapter()->rollBack();
    			$response = array (
    					"errors" => $e->getMessage()
    			);
    		}
    		echo Zend_Json::encode($response);
    		exit;
    	}
    	
    	$mapper = new Document_Model_Mapper_ModuleDocument();
    	$select = $mapper->getDbTable ()->
				    	select ( false )->
				    	setIntegrityCheck ( false )->
				    	from ( array ("d" => "module_document"),
				    			array (
				    					"d.module_document_id" => "module_document_id",
				    					"d.status" => "status",
				    					"d.order" => "order"))->
    					joinLeft ( array ("dd" => "module_document_detail"),
    							"dd.module_document_id = d.module_document_id AND dd.language_id = ".$active_lang_id,
    							array (
    									"dd.module_document_detail_id" => "module_document_detail_id",
    									"dd.title" => "title",
    									"dd.type" => "type",
    									"dd.size" => "size",
    									"dd.keywords" => "keywords",
    							))->
    					where("d.customer_id=".$customer_id)->order("d.order");
    	$response = $mapper->getDbTable()->fetchAll($select)->toArray();
    	$this->view->data = $response;
    }
    
    public function gridAction() {
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ();
    	 
    	$active_lang_id = Standard_Functions::getCurrentUser ()->active_language_id;
    	$default_lang_id = Standard_Functions::getCurrentUser ()->default_language_id;
    	$customer_id = Standard_Functions::getCurrentUser ()->customer_id;
    	
    	$mapper = new Document_Model_Mapper_ModuleDocument();
    	
    	$select = $mapper->getDbTable ()->
				    	select ( false )->
				    	setIntegrityCheck ( false )->
				    	from ( array ("d" => "module_document"),
				    			array (
				    					"d.module_document_id" => "module_document_id",
				    					"d.status" => "status",
				    					"d.order" => "order"))->
    					joinLeft ( array ("dd" => "module_document_detail"),
    							"dd.module_document_id = d.module_document_id AND dd.language_id = ".$active_lang_id,
    							array (
    									"dd.module_document_detail_id" => "module_document_detail_id",
    									"dd.title" => "title",
    									"dd.type" => "type",
    									"dd.size" => "size",
    									"dd.keywords" => "keywords",
    							))->
    					where("d.customer_id=".$customer_id)->order("d.order");
		$response = $mapper->getGridData ( array (
    					'column' => array (
    							'id' => array (
    								'actions'
    							),
    							'replace' => array (
    								'd.status' => array (
    									'1' => $this->view->translate('Active'),
    									'0' => $this->view->translate('Inactive')
    								)
    							)
    						)
    					),null, $select );
    	
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
    		$edit = array();
    		if($row [6] ["dd.module_document_detail_id"]=="") {
    			$mapper = new Document_Model_Mapper_ModuleDocumentDetail();
    			$details = $mapper->fetchAll("module_document_id=".$row [6] ["d.module_document_id"]." AND language_id=".$default_lang_id);
    			if(is_array($details)) {
    				$details = $details[0];
    				$row [6] ["dd.title"] = $row[0] = $details->getTitle();
    				$row [6] ["dd.type"] = $row[1] = $details->getType();
    				$row [6] ["dd.size"] = $row[2] = $details->getSize();
    				$row [6] ["dd.keywords"] = $row[3] = $details->getSize();
    			}
    		}
    		
    		$response ['aaData'] [$rowId] = $row;
    		if($languages) {
    			foreach ($languages as $lang) {
    				$editUrl = $this->view->url ( array (
    						"module" => "document",
    						"controller" => "index",
    						"action" => "edit",
    						"id" => $row [6] ["d.module_document_id"],
    						"lang" => $lang["l.language_id"]
    				), "default", true );
    				$edit[] = '<a href="'. $editUrl .'"><img src="images/lang/'.$lang["logo"].'" alt="'.$lang["l.title"].'" /></a>';
    			}
    		}
    		$deleteUrl = $this->view->url ( array (
    				"module" => "document",
    				"controller" => "index",
    				"action" => "delete",
    				"id" => $row [6] ["d.module_document_id"]
    		), "default", true );
    		$defaultEdit = '<div id="editLanguage">&nbsp;<div class="flag-list">'.implode("",$edit).'</div></div>';
    		$delete = '<a href="' . $deleteUrl . '" class="grid_delete" >'.$this->view->translate('Delete').'</a>';
    		$sap = '';
    		
    		$response ['aaData'] [$rowId] [6] = $defaultEdit. $sap .$delete;
    	}
    	$jsonGrid = Zend_Json::encode ( $response );
    	$this->_response->appendBody ( $jsonGrid );
    }
}