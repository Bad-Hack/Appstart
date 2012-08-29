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
    
    public function gridAction()
    {
    	// action body
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ();
    	
    	$grid = array(
    			"sEcho" => intval($this->_request->getParam("sEcho")),
    			"iTotalRecords" => "0",
    			"iTotalDisplayRecords" => "0",
    			"aaData" => array()
    	);
    	 
    	$sortingColumn = explode ( ",", $this->_request->getParam ( "sColumns" ) );
    	 
    	$grid["iTotalRecords"] = 1;
    	$gridArray ["iTotalDisplayRecords"] = 1;
    	$gridArray ["debug"] = $sortingColumn;
    	 
    	// action body
    	//$data["user_id"] = "1";
    	$data[0][] = "admin@aksystems.com";
    	$data[0][] = "Admin";
    	$data[0][] = "Edit";
    	
    	$grid ["aaData"] = $data;
    	$jsonGrid = Zend_Json::encode($grid);
    	$this->_response->appendBody($jsonGrid);
    }

    public function addAction()
    {
    	// action body
    	$form = new Admin_Form_SystemUser();
    	$request = $this->getRequest();
    	if ($this->getRequest()->isPost()) {
    		if($form->isValid($request->getPost()))
    		{
    			// Save Record In DB
    			
    		}
    		else {
    			$this->view->assign(array("password"=>$this->getRequest()->getParam('password',"")));
    		}
    	}
    	
    	foreach ($form->getElements() as $element) {
    		if($element->getDecorator('Label')) $element->getDecorator('Label')->setTag(null);
    	}
    	$this->view->form = $form;
    }
    
    public function editAction()
    {
    	// action body
    	$form = new Admin_Form_SystemUser();
    	$request = $this->getRequest();
    	
    	if ($this->getRequest()->isPost()) {
    		if($form->isValid($request->getPost()))
    		{
    			// Save Record In DB
    			
    		}
    		else {
    			$this->view->assign(array("password"=>$this->getRequest()->getParam('password',"")));
    		}
    	}
    	else
    	{
    		$mapper = new Admin_Model_Mapper_BusinessType();
    		$data = $mapper->find($request->getParam("id",""));
    		$data["confirm_password"] = $data["password"];
    	}

    	foreach ($form->getElements() as $element) {
    		if($element->getDecorator('Label')) $element->getDecorator('Label')->setTag(null);
    	}
    	$this->view->form = $form;
    }
}

