<?php

class HomeWallpaper_IndexController extends Zend_Controller_Action
{

    public function init()
    {
		/* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    	$this->view->addlink = $this->view->url ( array (
					    			"module" => "home-wallpaper",
					    			"controller" => "index",
					    			"action" => "add"
					    	), "default", true );
    }
    
    public function addAction()
    {
    	// action body
    	$form = new HomeWallpaper_Form_HomeWallpaper();
    	foreach ( $form->getElements () as $element ) {
    		if ($element->getDecorator ( 'Label' ))
    			$element->getDecorator ( 'Label' )->setTag ( null );
    	}
    	$action = $this->view->url ( array (
			    			"module" => "home-wallpaper",
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
    	$form = new HomeWallpaper_Form_HomeWallpaper ();
    	$request = $this->getRequest ();
    	if ($request->getParam ( "id", "" ) != "") {
    		$mapper = new HomeWallpaper_Model_Mapper_HomeWallpaper();
    		$data = $mapper->find ( $request->getParam ( "id", "" ) )->toArray ();
    		$form->populate ( $data );
    		foreach ( $form->getElements () as $element ) {
    			if ($element->getDecorator ( 'Label' ))
    				$element->getDecorator ( 'Label' )->setTag ( null );
    		}
    		$action = $this->view->url ( array (
    				"module" => "home-wallpaper",
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
    	$form = new HomeWallpaper_Form_HomeWallpaper ();
    	$request = $this->getRequest ();
    	$response = array ();
    	
    	if ($this->_request->isPost ()) {
    		if($request->getParam ( "upload", "" ) != "") {
    			$response = $this->fileUplaod();
    			echo Zend_Json::encode($response);
    			exit;
    		}
    		
    		$form->removeElement("image_ipad");
    		$form->removeElement("image_iphone");
    		$form->removeElement("image_android");
    		$form->removeElement("image_ipad3");
    		
    		if ($form->isValid ( $this->_request->getParams () )) {
    			//$form->logo->receive();
    			
    			try {
    				$customerId = Standard_Functions::getCurrentUser ()->customer_id;
    				
    				$model = new HomeWallpaper_Model_HomeWallpaper($form->getValues());
	    			if ($request->getParam ( "home_wallpaper_id", "" ) == "") {
	    				$mapper = new HomeWallpaper_Model_Mapper_HomeWallpaper();
	    				$maxOrder = $mapper->getNextOrder($customerId);
	    				$model->setOrder($maxOrder+1);
	    				
	    				$model->setCreatedBy ( Standard_Functions::getCurrentUser ()->user_id);
	    				$model->setCreatedAt ( Standard_Functions::getCurrentDateTime () );
	    				$model->setCustomerId ( $customerId);
	    			}
	    			
	    			$source_dir = Standard_Functions::getResourcePath(). "home-wallpaper/tmp/images";
	    			$upload_dir = Standard_Functions::getResourcePath(). "home-wallpaper/wallpapers/C".$customerId;
	    			if(!is_dir($upload_dir)) {
	    				mkdir($upload_dir,755);
	    			}
	    			
	    			if($request->getParam ( "image_ipad_path", "" ) != "")
	    			{
	    				$filename = $this->moveUploadFile($source_dir."/ipad/",$upload_dir."/ipad/",$request->getParam ( "image_ipad_path"));
	    				$model->setImageIpad("ipad/".$filename);
	    			}
	    			if($request->getParam ( "image_iphone_path", "" ) != "")
	    			{
	    				$filename = $this->moveUploadFile($source_dir."/iphone/",$upload_dir."/iphone/",$request->getParam ("image_iphone_path"));
	    				$model->setImageIphone("iphone/".$filename);
	    			}
	    			if($request->getParam ( "image_android_path", "" ) != "")
	    			{
	    				$filename = $this->moveUploadFile($source_dir."/android/",$upload_dir."/android/",$request->getParam ("image_android_path"));
	    				$model->setImageAndroid("android/".$filename);
	    			}
	    			if($request->getParam ( "image_ipad3_path", "" ) != "")
	    			{
	    				$filename = $this->moveUploadFile($source_dir."/ipad3/",$upload_dir."/ipad3/",$request->getParam ("image_ipad3_path"));
	    				$model->setImageAndroid("ipad3/".$filename);
	    			}
	    			
	    			$model->setLastUpdatedBy ( Standard_Functions::getCurrentUser ()->user_id );
	    			$model->setLastUpdatedAt ( Standard_Functions::getCurrentDateTime () );
	    			$model = $model->save ();
	    			if($model && $model->getHomeWallpaperId()!="") {
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
    
    private function moveUploadFile($source_dir,$dest_dir,$filename) {
    	$source_file_name = $filename;
    	$expension = array_pop(explode(".",$filename));
    	try {
	    	$i=1;
	    	while(file_exists($dest_dir.$filename)) {
	    		$filename = str_replace(".".$expension, "_".$i++.".".$expension, $filename);
	    	}
	    	if(!is_dir($dest_dir)){
	    		mkdir($dest_dir,755);
	    	}
	    	
	    	if(copy($source_dir.$source_file_name, $dest_dir.$filename)) {
	    		unlink($source_dir.$source_file_name);
	    	}
	    	$thumbname = str_replace(".".$expension, "_thumb.".$expension, $filename);
	    	$this->generateThumb($dest_dir.$filename, $dest_dir.$thumbname,0,75);
	    	
    	} catch (Exception $ex) {
    		var_dump($ex);die;
    	}
    	return $filename;
    }
    
    public function generateThumb($src, $dest, $destWidth=0 , $destHeight=0)
    {
    	/* read the source image */
    	$stype = array_pop(explode(".",$src));
    	switch($stype) {
    		case 'gif':
    			$source_image = imagecreatefromgif($src);
    			break;
    		case 'jpg':
    			$source_image = imagecreatefromjpeg($src);
    			break;
    		case 'png':
    			$source_image = imagecreatefrompng($src);
    			break;
    	}
    	
    	
    	$width = imagesx($source_image);
    	$height = imagesy($source_image);
    	
    	$desired_height = 0;
    	$desired_width = 0;
    	if($destWidth==0) {
    		$desired_height = $destHeight;
    		$desired_width = floor($width * ($destHeight / $height));
    	} else {
    		$desired_height = floor($destHeight * ($destWidth / $width));
    		$desired_width = $destWidth;
    	}
    	
    	/* create a new, "virtual" image */
    	$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
    	
    	/* copy source image at a resized size */
    	imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
    	
    	/* create the physical thumbnail image to its destination */
    	imagejpeg($virtual_image, $dest);
    }
    
    public function fileUplaod()
    {
    	$form = new HomeWallpaper_Form_HomeWallpaper ();
    	$request = $this->getRequest ();
    	$response = array ();
        if($request->getParam ( "upload", "" ) != "") {
        	$element = $request->getParam ( "upload") ;
        	$adapter = new Zend_File_Transfer_Adapter_Http();
        	$adapter->setDestination(Standard_Functions::getResourcePath(). "home-wallpaper/tmp/images/".str_replace("image_", "", $element));
        	$adapter->receive();
        	
        	if($adapter->getFileName($element)!="") {
                $response = array (
                   	"success" => array_pop(explode('\\',$adapter->getFileName($element)))
                   );
            } else {
               	$response = array (
                               	"errors" => "Error Occured"
               				);
            }
            return $response;
		}
		return "";
    }

    public function deleteAction() {
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ();
    	$request = $this->getRequest ();
    
    	if (($homeWallpaperId = $request->getParam ( "id", "" )) != "") {
    		$homeWallpaper = new HomeWallpaper_Model_HomeWallpaper ();
    		$homeWallpaper->populate ( $homeWallpaperId );
    		if ($homeWallpaper) {
    			try {
    				
    				$image_dir = Standard_Functions::getResourcePath(). "home-wallpaper/wallpapers/C". Standard_Functions::getCurrentUser ()->customer_id."/";
    				$model = $homeWallpaper->toArray();
    				$ext_ipad = array_pop(explode(".",$model["image_ipad"]));
    				$ext_iphone = array_pop(explode(".",$model["image_iphone"]));
    				$ext_ipad_3 = array_pop(explode(".",$model["image_ipad_3"]));
    				$ext_android = array_pop(explode(".",$model["image_android"]));
    				
    				if($model["image_ipad"]!="" && file_exists($image_dir . str_replace(".".$ext_ipad,"_thumb.".$ext_ipad,$model["image_ipad"]))) {
    					unlink($image_dir . str_replace(".".$ext_ipad,"_thumb.".$ext_ipad,$model["image_ipad"]));
    					unlink($image_dir . $model["image_ipad"]);
    				} 
    				if($model["image_iphone"]!="" && file_exists($image_dir . str_replace(".".$ext_iphone, "_thumb.".$ext_iphone, $model["image_iphone"]))) {
    					unlink($image_dir . str_replace(".".$ext_ipad,"_thumb.".$ext_ipad,$model["image_iphone"]));
    					unlink($image_dir . $model["image_iphone"]);
    				} 
    				if($model["image_ipad_3"]!="" && file_exists($image_dir . str_replace(".".$ext_ipad_3, "_thumb.".$ext_ipad_3, $model["image_ipad_3"]))) {
    					unlink($image_dir . str_replace(".".$ext_ipad,"_thumb.".$ext_ipad,$model["image_ipad_3"]));
    					unlink($image_dir . $model["image_ipad_3"]);
    				} 
    				if($model["image_android"]!="" && file_exists($image_dir . str_replace(".".$ext_android, "_thumb.".$ext_android, $model["image_android"]))) {
    					unlink($image_dir . str_replace(".".$ext_ipad,"_thumb.".$ext_ipad,$model["image_android"]));
    					unlink($image_dir . $model["image_android"]);
    				}
    				
    				$deletedRows = $homeWallpaper->delete ();
    					
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
    							"message" => "No wallpaper to delete."
    					)
    			);
    		}
    	} else {
    		$this->_redirect ( '/' );
    	}
    
    	$this->_helper->json ( $response );
    }

    public function gridAction() {
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ();
    
    	$mapper = new HomeWallpaper_Model_Mapper_HomeWallpaper();
    
    	$response = $mapper->getGridData ( array (
    			'column' => array (
    					'id' => array (
    							'actions'
    					),
    					'ignore' => array (
    							'thumbnail'
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
    	$image_dir = Standard_Functions::getResourcePath(). "home-wallpaper/wallpapers/C". Standard_Functions::getCurrentUser ()->customer_id."/";
    	$image_uri = "resource/home-wallpaper/wallpapers/C". Standard_Functions::getCurrentUser ()->customer_id."/";
    	foreach ( $rows as $rowId => $row ) {
    		$model =$row [4];
    		$image_path ="";
    		
    		$ext_ipad = array_pop(explode(".",$model["image_ipad"]));
    		$ext_iphone = array_pop(explode(".",$model["image_iphone"]));
    		$ext_ipad_3 = array_pop(explode(".",$model["image_ipad_3"]));
    		$ext_android = array_pop(explode(".",$model["image_android"]));
    		
    		if(file_exists($image_dir . str_replace(".".$ext_ipad,"_thumb.".$ext_ipad,$model["image_ipad"]))) {
    			$image_path = str_replace(".".$ext_ipad,"_thumb.".$ext_ipad,$model["image_ipad"]);
    		} else if(file_exists($image_dir . str_replace(".".$ext_iphone, "_thumb.".$ext_iphone, $model["image_iphone"]))) {
    			$image_path = str_replace(".".$ext_iphone,"_thumb.".$ext_iphone,$model["image_iphone"]);
    		} else if(file_exists($image_dir . str_replace(".".$ext_ipad_3, "_thumb.".$ext_ipad_3, $model["image_ipad_3"]))) {
    			$image_path = str_replace(".".$ext_ipad_3,"_thumb.".$ext_ipad_3,$model["image_ipad_3"]);
    		} else if(file_exists($image_dir . str_replace(".".$ext_android, "_thumb.".$ext_android, $model["image_android"]))) {
    			$image_path = str_replace(".".$ext_android,"_thumb.".$ext_android,$model["image_android"]);
    		}
    		
    		if($image_path!="") {
    			$response ['aaData'] [$rowId] [0] = "<img src='".$image_uri.$image_path."' title='".$model["image_title"]."' />";
    		} else {
    			$response ['aaData'] [$rowId] [0] = "No Image Found";
    		}
    		
    		$editUrl = $this->view->url ( array (
    				"module" => "home-wallpaper",
    				"controller" => "index",
    				"action" => "edit",
    				"id" => $row [4] ["home_wallpaper_id"]
    		), "default", true );
    		$deleteUrl = $this->view->url ( array (
    				"module" => "home-wallpaper",
    				"controller" => "index",
    				"action" => "delete",
    				"id" => $row [4] ["home_wallpaper_id"]
    		), "default", true );
    			
    		$edit = '<a href="' . $editUrl . '" class="grid_edit" >Edit</a>';
    		$delete = '<a href="' . $deleteUrl . '" class="grid_delete" >Delete</a>';
    		$sap = ($edit == "" || $delete == "") ? '' : '&nbsp;|&nbsp;';
    			
    		$response ['aaData'] [$rowId] [4] = $edit . $sap . $delete;
    	}
    
    	$jsonGrid = Zend_Json::encode ( $response );
    	$this->_response->appendBody ( $jsonGrid );
    }
}

