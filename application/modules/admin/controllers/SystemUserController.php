<?php
class Admin_SystemUserController extends Zend_Controller_Action {
	public function init() {
		/* Initialize action controller here */
	}
	public function indexAction() {
		// action body
		$this->view->addlink = $this->view->url ( array (
										"module" => "admin",
										"controller" => "system-user",
										"action" => "add" 
								), "default", true );
	}
	public function addAction() {
		// action body
		$form = new Admin_Form_SystemUser ();
		foreach ( $form->getElements () as $element ) {
			if ($element->getDecorator ( 'Label' ))
				$element->getDecorator ( 'Label' )->setTag ( null );
		}
		$this->view->form = $form;
		$this->view->assign ( array (
				"partial" => "system-user/partials/add.phtml" 
		) );
		$this->render ( "add-edit" );
	}
	public function editAction() {
		$form = new Admin_Form_SystemUser ();
		$request = $this->getRequest ();
		if ($request->getParam ( "id", "" ) != "") {
			$mapper = new Admin_Model_Mapper_SystemUser ();
			$data = $mapper->find ( $request->getParam ( "id", "" ) )->toArray ();
			
			$data ["confirm_password"] = $data ["password"];
			$form->populate ( $data );
			$this->view->password = $data ["password"];
			foreach ( $form->getElements () as $element ) {
				if ($element->getDecorator ( 'Label' ))
					$element->getDecorator ( 'Label' )->setTag ( null );
			}
		}
		$this->view->form = $form;
		$this->view->assign ( array (
				"partial" => "system-user/partials/edit.phtml" 
		) );
		$this->render ( "add-edit" );
	}
	public function saveAction() {
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ();
		$form = new Admin_Form_SystemUser ();
		$request = $this->getRequest ();
		$msg = "Error";
		$error = true;
		if ($this->getRequest ()->isPost ()) {
			if ($form->isValid ( $request->getPost () )) {
				// Save Record In DB
				try {
					$model = new Admin_Model_SystemUser ();
					
					if ($request->getParam ( "system_user_id", "" ) != "") {
						$model->setSystemUserId ( $request->getParam ( "system_user_id" ) );
					} else {
						$model->setCreatedBy ( Standard_Functions::getCurrentUser ()->system_user_id );
						$model->setCreatedAt ( Standard_Functions::getCurrentDateTime () );
					}
					
					$model->setEmail ( $request->getParam ( "email" ) );
					$model->setPassword ( $request->getParam ( "password" ) );
					$model->setRole ( $request->getParam ( "role" ) );
					$model->setLastUpdatedBy ( Standard_Functions::getCurrentUser ()->system_user_id );
					$model->setLastUpdatedAt ( Standard_Functions::getCurrentDateTime () );
					$model->save ();
					
					$msg = "Record save successfully";
					$error = false;
				} catch ( Exception $e ) {
					$msg = "Error: [" . $e->getCode () . "] " . $e->getMessage () . "";
				}
			} else {
				// Invalid Request
				$msg = "Please verify your information";
			}
		}
		
		$response ["error"] = $error;
		$response ["message"] = $msg;
		$jsonResponse = Zend_Json::encode ( $response );
		$this->_response->appendBody ( $jsonResponse );
	}
	public function deleteAction() {
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ();
		$request = $this->getRequest ();
		
		if (($systemUserId = $request->getParam ( "id", "" )) != "") {
			$systemUser = new Admin_Model_SystemUser ();
			$systemUser->populate ( $systemUserId );
			if ($systemUser) {
				try {
					$deletedRows = $systemUser->delete ();
					
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
			$this->_redirect ( '/admin/system-user' );
		}
		
		$this->_helper->json ( $response );
	}
	public function gridAction() {
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ();
		
		$mapper = new Admin_Model_Mapper_SystemUser ();
		
		$response = $mapper->getDataTableList ( array (
				'column' => array (
						'id' => array (
								'actions' 
						),
						'replace' => array (
								'role' => array (
										'1' => 'Administrator',
										'2' => 'User' 
								) 
						) 
				) 
		) );
		
		$rows = $response ['aaData'];
		foreach ( $rows as $rowId => $row ) {
			$editUrl = $this->view->url ( array (
					"module" => "admin",
					"controller" => "system-user",
					"action" => "edit",
					"id" => $row [2] ["system_user_id"] 
			), "default", true );
			$deleteUrl = $this->view->url ( array (
					"module" => "admin",
					"controller" => "system-user",
					"action" => "delete",
					"id" => $row [2] ["system_user_id"] 
			), "default", true );
			
			$edit = '<a href="' . $editUrl . '" class="grid_edit" >Edit</a>';
			$delete = ($row [2] ["system_user_id"] == 1) ? '' : '<a href="' . $deleteUrl . '" class="grid_delete" >Delete</a>';
			$sap = ($edit == "" || $delete == "") ? '' : '&nbsp;|&nbsp;';
			
			$response ['aaData'] [$rowId] [2] = $edit . $sap . $delete;
		}
		
		$jsonGrid = Zend_Json::encode ( $response );
		$this->_response->appendBody ( $jsonGrid );
	}
}
