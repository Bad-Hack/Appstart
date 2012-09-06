<?php

class Contact_IndexController extends Zend_Controller_Action
{

    public function init()
    {
		/* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    	$this->view->addlink = $this->view->url ( array (
					    			"module" => "contact",
					    			"controller" => "index",
					    			"action" => "add"
					    	), "default", true );
    }
    
    public function addAction()
    {
    	// action body
    	$form = new Contact_Form_Contact();
    	foreach ( $form->getElements () as $element ) {
    		if ($element->getDecorator ( 'Label' ))
    			$element->getDecorator ( 'Label' )->setTag ( null );
    	}
    	$action = $this->view->url ( array (
			    			"module" => "contact",
			    			"controller" => "index",
			    			"action" => "save"
			    	), "default", true );
    	$form->setAction($action);
    	$this->view->form = $form;
    	
    	$this->view->assign ( array (
    			"partial" => "index/partials/add.phtml"
    	) );
    	$this->render ( "add-edit" );
    }
    
    public function editAction()
    {
    	// action body
    	$form = new Contact_Form_Contact ();
    	$request = $this->getRequest ();
    	if ($request->getParam ( "id", "" ) != "") {
    		$mapper = new Contact_Model_Mapper_Contact();
    		$data = $mapper->find ( $request->getParam ( "id", "" ) )->toArray ();
    		$form->populate ( $data );
    		foreach ( $form->getElements () as $element ) {
    			if ($element->getDecorator ( 'Label' ))
    				$element->getDecorator ( 'Label' )->setTag ( null );
    		}
    		$action = $this->view->url ( array (
    				"module" => "contact",
    				"controller" => "index",
    				"action" => "save",
    				"id" => $request->getParam ( "id", "" )
    		), "default", true );
    		$form->setAction($action);
    	}
    	$this->view->form = $form;
    	$this->view->assign ( array (
    			"partial" => "index/partials/edit.phtml"
    	) );
    	$this->render ( "add-edit" );
    }
    
    public function saveAction()
    {
    	// action body
    	$form = new Contact_Form_Contact ();
    	$request = $this->getRequest ();
    	$response = array ();
    	
    	if ($this->_request->isPost ()) {
    		if($request->getParam ( "upload", "" ) != "") {
    			$upload = new Zend_Form();
    			$upload->addElement($form->getElement('logo'));
    			if($upload->valid($this->_request->getParams ())){
    				$upload->logo->receive();
    				if($upload->logo->getFileName()!="")
    				{
    					$response = array (
    							"success" => array_pop(explode('\\',$upload->logo->getFileName()))
    					);
    				} else {
    					$response = array (
    							"errors" => "Error Occured"
    					);
    				}
    			}
    			echo Zend_Json::encode($response);
    			//$this->_helper->json ( $response );
    			exit;
    		}
    		$form->removeElement("logo");
    		
    		if ($form->isValid ( $this->_request->getParams () )) {
    			//$form->logo->receive();
    			
    			try {
	    			$model = new Contact_Model_Contact($form->getValues());
	    			if ($request->getParam ( "contact_id", "" ) != "") {
	    				$model->setCreatedBy ( 1);
	    				$model->setCreatedAt ( Standard_Functions::getCurrentDateTime () );
	    			}
	    			if($request->getParam ( "logo_path", "" ) != "")
	    			{
	    				$model->setLogo($request->getParam ("logo_path", ""));
	    			}
	    			$model->setLastUpdatedBy ( 1 );
	    			$model->setLastUpdatedAt ( Standard_Functions::getCurrentDateTime () );
	    			$model = $model->save ();
	    			if($model && $model->getContactId()!="") {
	    				$response = array (
	    						"success" => $model->toArray ()
	    				);
	    			}
    			} catch (Exception $ex) {
    				$response = array (
    						"errors" => $ex->getMessage()
    				);
    			}
    		}
    		else
    		{
    			$errors = $form->getMessages ();
    			foreach ( $errors as $name => $error ) {
    				$errors [$name] = $error [0];
    			}
    			$response = array (
    					"errors" => $errors
    			);
    		}
    	} 
		// Send error or success message accordingly
		$this->_helper->json ( $response );
    }
    
    public function deleteAction() {
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ();
    	$request = $this->getRequest ();
    
    	if (($contactId = $request->getParam ( "id", "" )) != "") {
    		$contact = new Contact_Model_Contact ();
    		$contact->populate ( $contactId );
    		if ($contact) {
    			try {
    				$deletedRows = $contact->delete ();
    					
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
    		$this->_redirect ( '/contact/index/index' );
    	}
    
    	$this->_helper->json ( $response );
    }

    public function gridAction() {
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ();
    
    	$mapper = new Contact_Model_Mapper_Contact();
    
    	$response = $mapper->getGridData ( array (
    			'column' => array (
    					'id' => array (
    							'actions'
    					),
    					'replace' => array (
    							'status' => array (
    									'1' => 'Active',
    									'0' => 'Inactive'
    							)
    					)
    			)
    	) );
    
    	$rows = $response ['aaData'];
    	foreach ( $rows as $rowId => $row ) {
    		$editUrl = $this->view->url ( array (
    				"module" => "contact",
    				"controller" => "index",
    				"action" => "edit",
    				"id" => $row [5] ["contact_id"]
    		), "default", true );
    		$deleteUrl = $this->view->url ( array (
    				"module" => "contact",
    				"controller" => "index",
    				"action" => "delete",
    				"id" => $row [5] ["contact_id"]
    		), "default", true );
    			
    		$edit = '<a href="' . $editUrl . '" class="grid_edit" >Edit</a>';
    		$delete = '<a href="' . $deleteUrl . '" class="grid_delete" >Delete</a>';
    		$sap = ($edit == "" || $delete == "") ? '' : '&nbsp;|&nbsp;';
    			
    		$response ['aaData'] [$rowId] [5] = $edit . $sap . $delete;
    	}
    
    	$jsonGrid = Zend_Json::encode ( $response );
    	$this->_response->appendBody ( $jsonGrid );
    }
}

