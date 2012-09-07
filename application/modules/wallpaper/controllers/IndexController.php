<?php

class Wallpaper_IndexController extends Zend_Controller_Action
{

	public function init(){
		
	}
	
	public function indexAction(){ 
		 
	}
	
	public function gridAction() 
	{
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		$wallpaperMapper = new Wallpaper_Model_Mapper_Wallpaper();
		$response = $wallpaperMapper->getDataTableList ( array (
				'column' => array (
								'id' => array (
											'actions'
								)
				)
		));
	
		$rows = $response ['aaData']; 
		
		foreach ( $rows as $rowId => $row ) 
		{	
			$editUrl = $this->view->url ( array (
											"module" => "wallpaper",
												"controller" => "index",
												"action" => "edit",
												"id" => $row [3] ["wallpaper_id"]
										), "default", true ); 
			  
			$status = ($row [1] ["status"] == "1")?"Active":"Inactive";
			//$ipad = "<img src='upload/ipad/'.$row[3]['image_ipad'].' width='30'>";
			$edit = '<a href="'.$editUrl.'" class="grid_edit" >Edit</a>';
			$response ['aaData'] [$rowId] [1] = $status;
 			$response ['aaData'] [$rowId] [3] = $edit;
		}
		echo $this->_helper->json ( $response );
	}
	
	public function addAction()
	{	 
		$form = new Wallpaper_Form_Wallpaper();  
		 $form->getElement("submit")->setLabel("Add");
		 $form->getElement("cancel")->setLabel("Cancel");
		 $form->getElement("reset")->setLabel("Reset");
		 $form->getElement("image_ipad")->setAttrib("required", "required");
		 $form->getElement("image_ipad_3")->setAttrib("required", "required");
		 $form->getElement("image_iphone")->setAttrib("required", "required");
		 $form->getElement("image_android")->setAttrib("required", "required");
    	$request = $this->getRequest();
    	 
    	$action = $this->view->url ( array (
    			"module" => "wallpaper",
    			"controller" => "index",
    			"action" => "save"
    	), "default", true );

    	$form->setAction($action); 
    	
    	$this->view->form = $form;
    	$this->render("add");
	}
	
	public function saveAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$request = $this->getRequest();
		$form = new Wallpaper_Form_Wallpaper();
		$response = array();
		
		if ($this->getRequest()->isPost())
		{	
			if($request->getParam ( "upload", "" ) != "")
			{ 
				$rand = rand(1,9999);
				$form->addElement($form->getElement('image_ipad'));
				$form->addElement($form->getElement('image_ipad_3'));
				$form->addElement($form->getElement('image_iphone'));
				$form->addElement($form->getElement('image_android'));
				 
				if($form->valid($this->_request->getParams ()))
				{
					$form->image_ipad->receive();
					$form->image_ipad_3->receive();
					$form->image_iphone->receive();
					$form->image_android->receive();
					 
					if($form->image_ipad->getFileName()!="")
					{
						$response = array (
								"success_ipad" => $form->image_ipad->getFileName()
						);
					} 
					else 
					{
						$response = array (
								"errors_ipad" => "Ipad Logo not uplaoded"
						);
					}
					
					if($form->image_ipad_3->getFileName()!="")
					{
						$response = array (
								"success_ipad3" => $form->image_ipad_3->getFileName()
						);
					}
					else
					{
						$form = array (
								"errors_ipad3" => "Ipad 3 logo not uploaded"
						);
					}
					
					if($form->image_iphone->getFileName()!="")
					{
						$response = array (
								"success_iphone" => $form->image_iphone->getFileName()
						);
					}
					else
					{
						$response = array (
								"errors_iphone" => "Iphone Logo not uploaded"
						);
					}
					
					if($form->image_android->getFileName()!="")
					{
						$response = array (
								"success_android" => $form->image_android->getFileName()
						);
					}
					else
					{
						$response = array (
								"errors_android" => "Android Logo not uplaoded"
						);
					}
				} 
				$this->_helper->json ( $response ); 
			} 
			  
			try
			{
				$wallpapermodel = new Wallpaper_Model_Wallpaper();
				 
				if ($request->getParam ( "wallpaper_id", "" ) != "") {
					$wallpapermodel->setWallpaperId ( $request->getParam ( "wallpaper_id" ) );
				}else{
					$wallpapermodel->setCreatedAt(Standard_Functions::getCurrentDateTime());
					$wallpapermodel->setCreatedBy('2');
				}
				
				$wallpapermodel->setOrder($this->_request->getParam("order"));
				$wallpapermodel->setStatus($this->_request->getParam("status"));
				
				$wallpapermodel->setImageIpad($this->_request->getParam("ipad_logo"));
				$wallpapermodel->setImageIpad3($this->_request->getParam("ipad3_logo"));
				$wallpapermodel->setImageIphone($this->_request->getParam("iphone_logo"));
				$wallpapermodel->setImageAndroid($this->_request->getParam("android_logo"));
				
				$wallpapermodel->setLinkToModule($this->_request->getParam("link_to_module"));
				$wallpapermodel->setLastUpdatedAt(Standard_Functions::getCurrentDateTime ());
				$wallpapermodel->setLastUpdatedBy('2');
	
				$wallpapermodel->save ( $wallpapermodel );
				$response = array (
						"success" => $wallpapermodel->toArray ()
				); 
			}
			catch (Exception $ex){
				$response["error"] = $ex->getMessage();
			}
		}
		
		$this->_helper->json($response);
	}

	public function editAction()
	{
		$form = new Wallpaper_Form_Wallpaper();
		$form->getElement("submit")->setLabel("Update");
		$form->getElement("cancel")->setLabel("Cancel");  
		$request = $this->getRequest();
		
		$action = $this->view->url ( array (
				"module" => "wallpaper",
				"controller" => "index",
				"action" => "save"
		), "default", true );
		
		$form->setAction($action);
 		
		$wallpapermodel = new Wallpaper_Model_Mapper_Wallpaper();
		if($request->getParam("id") != ""){
			$wallpaper = $wallpapermodel->find($request->getParam("id"))->ToArray();
			$form->populate ( $wallpaper );
 		} 
		 $this->view->form= $form;
	}
}

