<?php
class HomeWallpaper_IndexController extends Zend_Controller_Action {
	var $_module_id;
	
	public function init() {
		/* Initialize action controller here */
		$modulesMapper = new Admin_Model_Mapper_Module();
		$module = $modulesMapper->fetchAll("name ='home-wallpaper'");
		if(is_array($module)) {
			$this->_module_id = $module[0]->getModuleId();
		}
	}
	public function indexAction() {
		// action body
		$this->view->addlink = $this->view->url ( array (
				"module" => "home-wallpaper",
				"controller" => "index",
				"action" => "add" 
		), "default", true );
		$this->view->reorderlink = $this->view->url ( array (
				"module" => "home-wallpaper",
				"controller" => "index",
				"action" => "reorder"
		), "default", true );
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
	
			$mapper = new HomeWallpaper_Model_Mapper_HomeWallpaper();
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
				if($model && $model->getHomeWallpaperId()!="") {
					$response = array (
							"success" => true
					);
				}
			}catch(Exception $e) {
				$mapper->getDbTable()->getAdapter()->rollBack();
				$response = array (
						"errors" => $e->getMessage()
				);
			}
			echo Zend_Json::encode($response);
			exit;
		}
		 
		$mapper = new HomeWallpaper_Model_Mapper_HomeWallpaper();
		$select = $mapper->getDbTable ()->
							select ( false )->
							setIntegrityCheck ( false )->
							from ( array ("h" => "module_home_wallpaper"), 
									array (
										"h.home_wallpaper_id" => "home_wallpaper_id",
										"h.status" => "status",
										"h.order" => "order") )->
							joinLeft ( array ("hd" => "module_home_wallpaper_detail"), 
										"hd.home_wallpaper_id = h.home_wallpaper_id AND hd.language_id=" . $active_lang_id, 
									array (
										"hd.image_title" => "image_title",
										"hd.home_wallpaper_detail_id" => "home_wallpaper_detail_id",
										"image_ipad",
										"image_iphone",
										"image_ipad_3",
										"image_android",
										"image_ios_6") )->
							where ( "h.customer_id=" . $customer_id )->order("h.order");
		$response = $mapper->getDbTable()->fetchAll($select)->toArray();
		$this->view->data = $response;
	}
	public function addAction() {
		// action body
		$form = new HomeWallpaper_Form_HomeWallpaper ();
		foreach ( $form->getElements () as $element ) {
			if ($element->getDecorator ( 'Label' ))
				$element->getDecorator ( 'Label' )->setTag ( null );
		}
		$action = $this->view->url ( array (
				"module" => "home-wallpaper",
				"controller" => "index",
				"action" => "save" 
		), "default", true );
		$form->setAction ( $action );
		$this->view->form = $form;
		
		$this->view->assign ( array (
				"partial" => "index/partials/add.phtml" 
		) );
		$this->render ( "add-edit" );
	}
	public function editAction() {
		// action body
		$form = new HomeWallpaper_Form_HomeWallpaper ();
		$request = $this->getRequest ();
		if ($request->getParam ( "id", "" ) != "" && $request->getParam ( "lang", "" ) != "") {
			$homeWallpaperMapper = new HomeWallpaper_Model_Mapper_HomeWallpaper ();
			$home_wallpaper_id = $request->getParam ( "id", "" );
			$language_id = $request->getParam ( "lang", "" );
			$languageMapper = new Admin_Model_Mapper_Language ();
			$languageData = $languageMapper->find ( $language_id );
			$this->view->language = $languageData->getTitle ();
			
			$default_lang_id = Standard_Functions::getCurrentUser ()->default_language_id;
			$data = $homeWallpaperMapper->find ( $home_wallpaper_id )->toArray ();
			$form->populate ( $data );
			$datadetails = array ();
			$homeWallpaperDetailMapper = new HomeWallpaper_Model_Mapper_HomeWallpaperDetail ();
			if ($homeWallpaperDetailMapper->countAll ( "home_wallpaper_id = " . $home_wallpaper_id . " AND language_id = " . $language_id ) > 0) {
				// Record For Language Found
				$dataDetails = $homeWallpaperDetailMapper->getDbTable ()->fetchAll ( "home_wallpaper_id = " . $home_wallpaper_id . " AND language_id = " . $language_id )->toArray ();
			} else {
				// Record For Language Not Found
				$dataDetails = $homeWallpaperDetailMapper->getDbTable ()->fetchAll ( "home_wallpaper_id = " . $home_wallpaper_id . " AND language_id = " . $default_lang_id )->toArray ();
				$dataDetails [0] ["home_wallpaper_id"] = "";
				$dataDetails [0] ["language_id"] = $language_id;
			}
			if (isset ( $dataDetails [0] ) && is_array ( $dataDetails [0] )) {
				$form->populate ( $dataDetails [0] );
				//$this->view->logo_path = $dataDetails [0] ["logo"];
				$customerId = Standard_Functions::getCurrentUser ()->customer_id;
				$img_uri = "resource/home-wallpaper/wallpapers/C" . $customerId;
				$ext = array_pop(explode(".",$dataDetails [0] ["image_ipad"]));
				$img_ipad = str_replace(".".$ext, "_thumb.".$ext, $dataDetails [0] ["image_ipad"]);
				$ext = array_pop(explode(".",$dataDetails [0] ["image_iphone"]));
				$img_iphone = str_replace(".".$ext, "_thumb.".$ext, $dataDetails [0] ["image_iphone"]);
				$ext = array_pop(explode(".",$dataDetails [0] ["image_android"]));
				$img_android = str_replace(".".$ext, "_thumb.".$ext, $dataDetails [0] ["image_android"]);
				$ext = array_pop(explode(".",$dataDetails [0] ["image_ipad_3"]));
				$img_ipad3 = str_replace(".".$ext, "_thumb.".$ext, $dataDetails [0] ["image_ipad_3"]);
				$ext = array_pop(explode(".",$dataDetails [0] ["image_ios_6"]));
				$img_ios6 = str_replace(".".$ext, "_thumb.".$ext, $dataDetails [0] ["image_ios_6"]);
				
				$this->view->image_ipad_path = $this->view->baseUrl($img_uri ."/" . $img_ipad);
				$this->view->image_iphone_path = $this->view->baseUrl($img_uri ."/" . $img_iphone);
				$this->view->image_android_path = $this->view->baseUrl($img_uri ."/" . $img_android);
				$this->view->image_ipad3_path = $this->view->baseUrl($img_uri ."/" . $img_ipad3);
				$this->view->image_ios6_path = $this->view->baseUrl($img_uri ."/" . $img_ios6);
				
			}
			foreach ( $form->getElements () as $element ) {
				if ($element->getDecorator ( 'Label' )) {
					$element->getDecorator ( 'Label' )->setTag ( null );
				}
				$action = $this->view->url ( array (
						"module" => "home-wallpaper",
						"controller" => "index",
						"action" => "save",
						"id" => $request->getParam ( "id", "" ) 
				), "default", true );
				$form->setAction ( $action );
			}
		} else {
			$this->_redirect ( 'index' );
		}
		$this->view->form = $form;
		$this->view->assign ( array (
				"partial" => "index/partials/edit.phtml" 
		) );
		$this->render ( "add-edit" );
	}
	public function saveAction() {
		// action body
		$form = new HomeWallpaper_Form_HomeWallpaper ();
		$request = $this->getRequest ();
		$response = array ();
		
		if ($this->_request->isPost ()) {
			if ($request->getParam ( "upload", "" ) != "") {
				$response = $this->fileUplaod ();
				echo Zend_Json::encode ( $response );
				exit ();
			}
			
			$form->removeElement ( "image_ipad" );
			$form->removeElement ( "image_iphone" );
			$form->removeElement ( "image_android" );
			$form->removeElement ( "image_ipad3" );
			$form->removeElement ( "image_ios6" );
			
			if ($form->isValid ( $this->_request->getParams () )) {
				// $form->logo->receive();
				
				try {
					$allFormValues = $form->getValues ();
					$customerId = Standard_Functions::getCurrentUser ()->customer_id;
					$user_id = Standard_Functions::getCurrentUser ()->user_id;
					$date_time = Standard_Functions::getCurrentDateTime ();
					$homeWallpaperMapper = new HomeWallpaper_Model_Mapper_HomeWallpaper ();
					$homeWallpaperMapper->getDbTable ()->getAdapter ()->beginTransaction ();
					$homeWallpaperModel = new HomeWallpaper_Model_HomeWallpaper ( $allFormValues );
					$source_dir = Standard_Functions::getResourcePath () . "home-wallpaper/tmp/images";
					$upload_dir = Standard_Functions::getResourcePath () . "home-wallpaper/wallpapers/C" . $customerId;
					if ($request->getParam ( "home_wallpaper_id", "" ) == "") {
						// Adding new record
						$maxOrder = $homeWallpaperMapper->getNextOrder ( $customerId );
						$homeWallpaperModel->setOrder ( $maxOrder + 1 );
						$homeWallpaperModel->setCreatedBy ( Standard_Functions::getCurrentUser ()->user_id );
						$homeWallpaperModel->setCreatedAt ( Standard_Functions::getCurrentDateTime () );
						$homeWallpaperModel->setCustomerId ( $customerId );
						$homeWallpaperModel->setLastUpdatedBy ( Standard_Functions::getCurrentUser ()->user_id );
						$homeWallpaperModel->setLastUpdatedAt ( Standard_Functions::getCurrentDateTime () );
						$homeWallpaperModel = $homeWallpaperModel->save ();
						
						// saving homewallpaer details
						$homeWallpaperId = $homeWallpaperModel->get ( "home_wallpaper_id" );
						$customerLanguageMapper = new Admin_Model_Mapper_CustomerLanguage ();
						$customerLanguageModel = $customerLanguageMapper->fetchAll ( "customer_id = " . $customerId );
						if (is_array ( $customerLanguageModel )) {
							$is_uploaded_ipad = 
							$is_uploaded_iphone = 
							$is_uploaded_android =
							$is_uploaded_ipad3 =
							$is_uploaded_ios6 = false;
							foreach ( $customerLanguageModel as $languages ) {
								$homeWallpaperDetailModel = new HomeWallpaper_Model_HomeWallpaperDetail ( $allFormValues );
								$homeWallpaperDetailModel->setHomeWallpaperId ( $homeWallpaperId );
								$homeWallpaperDetailModel->setLanguageId ( $languages->getLanguageId () );
								$homeWallpaperDetailModel->setCreatedBy ( Standard_Functions::getCurrentUser ()->user_id );
								$homeWallpaperDetailModel->setCreatedAt ( Standard_Functions::getCurrentDateTime () );
								$homeWallpaperDetailModel->setLastUpdatedBy ( Standard_Functions::getCurrentUser ()->user_id );
								$homeWallpaperDetailModel->setLastUpdatedAt ( Standard_Functions::getCurrentDateTime () );
								if (! is_dir ( $upload_dir )) {
									mkdir ( $upload_dir, 755 );
								}
								
								if (!$is_uploaded_ipad && $request->getParam ( "image_ipad_path", "" ) != "") {
									$filename = $this->moveUploadFile ( $source_dir . "/ipad/", $upload_dir . "/ipad/", $request->getParam ( "image_ipad_path" ) );
									$homeWallpaperDetailModel->setImageIpad ( "ipad/" . $filename );
									$is_uploaded_ipad = true;
								} else if($request->getParam ( "image_ipad_path" ,null) !== null) {
									$homeWallpaperDetailModel->setImageIpad ( "ipad/" . $request->getParam ( "image_ipad_path" ) );
								}
								if (!$is_uploaded_iphone && $request->getParam ( "image_iphone_path", "" ) != "") {
									$filename = $this->moveUploadFile ( $source_dir . "/iphone/", $upload_dir . "/iphone/", $request->getParam ( "image_iphone_path" ) );
									$homeWallpaperDetailModel->setImageIphone ( "iphone/" . $filename );
									$is_uploaded_iphone = true;
								} else if($request->getParam ( "image_iphone_path" ,null) !== null) {
									$homeWallpaperDetailModel->setImageIphone ( "iphone/" . $request->getParam ( "image_iphone_path" ) );
								}
								if (!$is_uploaded_android && $request->getParam ( "image_android_path", "" ) != "") {
									$filename = $this->moveUploadFile ( $source_dir . "/android/", $upload_dir . "/android/", $request->getParam ( "image_android_path" ) );
									$homeWallpaperDetailModel->setImageAndroid ( "android/" . $filename );
									$is_uploaded_android = true;
								} else if($request->getParam ( "image_android_path" ,null) !== null) {
									$homeWallpaperDetailModel->setImageAndroid ( "android/" . $request->getParam ( "image_android_path" ) );
								}
								if (!$is_uploaded_ipad3 && $request->getParam ( "image_ipad3_path", "" ) != "") {
									$filename = $this->moveUploadFile ( $source_dir . "/ipad3/", $upload_dir . "/ipad3/", $request->getParam ( "image_ipad3_path" ) );
									$homeWallpaperDetailModel->setImageIpad3 ( "ipad3/" . $filename );
									$is_uploaded_ipad3 = true;
								} else if($request->getParam ( "image_ipad3_path" ,null) !== null){
									$homeWallpaperDetailModel->setImageIpad3 ( "ipad3/" . $request->getParam ( "image_ipad3_path" ) );
								}
								if (!$is_uploaded_ios6 && $request->getParam ( "image_ios6_path", "" ) != "") {
									$filename = $this->moveUploadFile ( $source_dir . "/ios6/", $upload_dir . "/ios6/", $request->getParam ( "image_ios6_path" ) );
									$homeWallpaperDetailModel->setImageIos6 ( "ios6/" . $filename );
									$is_uploaded_ios6 = true;
								} else if($request->getParam ( "image_ios6_path" ,null) !== null) {
									$homeWallpaperDetailModel->setImageIos6 ( "ios6/" . $request->getParam ( "image_ios6_path" ) );
								}
								$homeWallpaperDetailModel = $homeWallpaperDetailModel->save ();
							}
						}
					} else {
						// Update homewallpaper record
						$homeWallpaperModel->setLastUpdatedBy ( $user_id );
						$homeWallpaperModel->setLastUpdatedAt ( $date_time );
						$homeWallpaperModel = $homeWallpaperModel->save ();
						
						// update homewallpaper details record
						// $homeWallpaperId =
						// $homeWallpaperModel->get("home_wallpaper_id");
						$homeWallpaperDetailModel = new HomeWallpaper_Model_HomeWallpaperDetail ( $allFormValues );
						$homeWallpaperDetailModel->setCreatedBy ( $user_id );
						$homeWallpaperDetailModel->setCreatedAt ( $date_time );
						$homeWallpaperDetailModel->setLastUpdatedBy ( $user_id );
						$homeWallpaperDetailModel->setLastUpdatedAt ( $date_time );
						if (! is_dir ( $upload_dir )) {
							mkdir ( $upload_dir, 755 );
						}
						
						if ($request->getParam ( "image_ipad_path", "" ) != "") {
							$filename = $this->moveUploadFile ( $source_dir . "/ipad/", $upload_dir . "/ipad/", $request->getParam ( "image_ipad_path" ) );
							$homeWallpaperDetailModel->setImageIpad ( "ipad/" . $filename );
						}
						if ($request->getParam ( "image_iphone_path", "" ) != "") {
							$filename = $this->moveUploadFile ( $source_dir . "/iphone/", $upload_dir . "/iphone/", $request->getParam ( "image_iphone_path" ) );
							$homeWallpaperDetailModel->setImageIphone ( "iphone/" . $filename );
						}
						if ($request->getParam ( "image_android_path", "" ) != "") {
							$filename = $this->moveUploadFile ( $source_dir . "/android/", $upload_dir . "/android/", $request->getParam ( "image_android_path" ) );
							$homeWallpaperDetailModel->setImageAndroid ( "android/" . $filename );
						}
						if ($request->getParam ( "image_ipad3_path", "" ) != "") {
							$filename = $this->moveUploadFile ( $source_dir . "/ipad3/", $upload_dir . "/ipad3/", $request->getParam ( "image_ipad3_path" ) );
							$homeWallpaperDetailModel->setImageIpad3 ( "ipad3/" . $filename );
						}
						if ($request->getParam ( "image_ios6_path", "" ) != "") {
							$filename = $this->moveUploadFile ( $source_dir . "/ios6/", $upload_dir . "/ios6/", $request->getParam ( "image_ios6_path" ) );
							$homeWallpaperDetailModel->setImageIos6 ( "ios6/" . $filename );
						}
						$homeWallpaperDetailModel = $homeWallpaperDetailModel->save ();
					}
					$customermoduleMapper = new Admin_Model_Mapper_CustomerModule();
					$customermodule = $customermoduleMapper->fetchAll("customer_id=". $customerId ." AND module_id=".$this->_module_id);
					if(is_array($customermodule)) {
						$customermodule = $customermodule[0];
						$customermodule->setIsPublish("NO");
						$customermodule->save();
					}
					
					$homeWallpaperMapper->getDbTable ()->getAdapter ()->commit ();
					
					if ($homeWallpaperModel && $homeWallpaperModel->getHomeWallpaperId () != "") {
						$response = array (
								"success" => $homeWallpaperModel->toArray () 
						);
					}
				} catch ( Exception $ex ) {
					$homeWallpaperMapper->getDbTable ()->getAdapter ()->rollBack ();
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
		// Send error or success message accordingly
		$this->_helper->json ( $response );
	}
	private function moveUploadFile($source_dir, $dest_dir, $filename) {
		$source_file_name = $filename;
		$expension = array_pop ( explode ( ".", $filename ) );
		try {
			$i = 1;
			while ( file_exists ( $dest_dir . $filename ) ) {
				$filename = str_replace ( "." . $expension, "_" . $i ++ . "." . $expension, $source_file_name );
			}
			if (! is_dir ( $dest_dir )) {
				mkdir ( $dest_dir, 755 );
			}
			
			while(!file_exists($source_dir . $source_file_name)) {}
			
			if (copy ( $source_dir . $source_file_name, $dest_dir . $filename )) {
				unlink ( $source_dir . $source_file_name );
			}
			$thumbname = str_replace ( "." . $expension, "_thumb." . $expension, $filename );
			$this->generateThumb ( $dest_dir . $filename, $dest_dir . $thumbname, 0, 75 );
		} catch ( Exception $ex ) {
			
		}
		return $filename;
	}
	public function generateThumb($src, $dest, $destWidth = 0, $destHeight = 0) {
		/* read the source image */
		$stype = array_pop ( explode ( ".", $src ) );
		switch ($stype) {
			case 'gif' :
				$source_image = imagecreatefromgif ( $src );
				break;
			case 'jpg' :
			case 'jpeg' :
				$source_image = imagecreatefromjpeg ( $src );
				break;
			case 'png' :
				$source_image = imagecreatefrompng ( $src );
				break;
		}
		
		$width = imagesx ( $source_image );
		$height = imagesy ( $source_image );
		
		$desired_height = 0;
		$desired_width = 0;
		if ($destWidth == 0) {
			$desired_height = $destHeight;
			$desired_width = floor ( $width * ($destHeight / $height) );
		} else {
			$desired_height = floor ( $destHeight * ($destWidth / $width) );
			$desired_width = $destWidth;
		}
		
		/* create a new, "virtual" image */
		$virtual_image = imagecreatetruecolor ( $desired_width, $desired_height );
		
		/* copy source image at a resized size */
		imagecopyresampled ( $virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height );
		
		/* create the physical thumbnail image to its destination */
		imagejpeg ( $virtual_image, $dest );
	}
	public function fileUplaod() {
		$form = new HomeWallpaper_Form_HomeWallpaper ();
		$request = $this->getRequest ();
		$response = array ();
		if ($request->getParam ( "upload", "" ) != "") {
			$element = $request->getParam ( "upload" );
			$adapter = new Zend_File_Transfer_Adapter_Http ();
			$adapter->setDestination ( Standard_Functions::getResourcePath () . "home-wallpaper/tmp/images/" . str_replace ( "image_", "", $element ) );
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
					$homeWallpaperDetailMapper = new HomeWallpaper_Model_Mapper_HomeWallpaperDetail ();
					$homeWallpaperDetailModel = new HomeWallpaper_Model_HomeWallpaperDetail ();
					$homeWallpaperDetailMapper->getDbTable ()->getAdapter ()->beginTransaction ();
					$dataDetails = $homeWallpaperDetailMapper->fetchAll ( "home_wallpaper_id =" . $homeWallpaperId );
					foreach ( $dataDetails as $dataDetail ) {
						$model = $dataDetail->toArray();
						
						$dataDetail->delete ();
						$image_dir = Standard_Functions::getResourcePath () . "home-wallpaper/wallpapers/C" . Standard_Functions::getCurrentUser ()->customer_id . "/";
						$ext_ipad = array_pop ( explode ( ".", $model ["image_ipad"] ) );
						$ext_iphone = array_pop ( explode ( ".", $model ["image_iphone"] ) );
						$ext_ipad_3 = array_pop ( explode ( ".", $model ["image_ipad_3"] ) );
						$ext_android = array_pop ( explode ( ".", $model ["image_android"] ) );
						$ext_ios_6 = array_pop ( explode ( ".", $model ["image_ios_6"] ) );
						
						if ($model ["image_ipad"] != "" && file_exists ( $image_dir . str_replace ( "." . $ext_ipad, "_thumb." . $ext_ipad, $model ["image_ipad"] ) )) {
							unlink ( $image_dir . str_replace ( "." . $ext_ipad, "_thumb." . $ext_ipad, $model ["image_ipad"] ) );
							unlink ( $image_dir . $model ["image_ipad"] );
						}
						if ($model ["image_iphone"] != "" && file_exists ( $image_dir . str_replace ( "." . $ext_iphone, "_thumb." . $ext_iphone, $model ["image_iphone"] ) )) {
							unlink ( $image_dir . str_replace ( "." . $ext_ipad, "_thumb." . $ext_ipad, $model ["image_iphone"] ) );
							unlink ( $image_dir . $model ["image_iphone"] );
						}
						if ($model ["image_ipad_3"] != "" && file_exists ( $image_dir . str_replace ( "." . $ext_ipad_3, "_thumb." . $ext_ipad_3, $model ["image_ipad_3"] ) )) {
							unlink ( $image_dir . str_replace ( "." . $ext_ipad, "_thumb." . $ext_ipad, $model ["image_ipad_3"] ) );
							unlink ( $image_dir . $model ["image_ipad_3"] );
						}
						if ($model ["image_android"] != "" && file_exists ( $image_dir . str_replace ( "." . $ext_android, "_thumb." . $ext_android, $model ["image_android"] ) )) {
							unlink ( $image_dir . str_replace ( "." . $ext_ipad, "_thumb." . $ext_ipad, $model ["image_android"] ) );
							unlink ( $image_dir . $model ["image_android"] );
						}
						if ($model ["image_ios_6"] != "" && file_exists ( $image_dir . str_replace ( "." . $ext_android, "_thumb." . $ext_android, $model ["image_ios_6"] ) )) {
							unlink ( $image_dir . str_replace ( "." . $ext_ipad, "_thumb." . $ext_ipad, $model ["image_ios_6"] ) );
							unlink ( $image_dir . $model ["image_ios_6"] );
						}
					}
					
					$deletedRows = $homeWallpaper->delete ();
					
					// set is pulish to false
					$customerId = Standard_Functions::getCurrentUser ()->customer_id;
					$customermoduleMapper = new Admin_Model_Mapper_CustomerModule();
					$customermodule = $customermoduleMapper->fetchAll("customer_id=".$customerId." AND module_id=".$this->_module_id);
					if(is_array($customermodule)) {
						$customermodule = $customermodule[0];
						$customermodule->setIsPublish("NO");
						$customermodule->save();
					}
					
					$homeWallpaperDetailMapper->getDbTable ()->getAdapter ()->commit ();
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
		
		$active_lang_id = Standard_Functions::getCurrentUser ()->active_language_id;
		$default_lang_id = Standard_Functions::getCurrentUser ()->default_language_id;
		$homeWallpaperMapper = new HomeWallpaper_Model_Mapper_HomeWallpaper ();
		
		$select = $homeWallpaperMapper->getDbTable ()->select ( false )->setIntegrityCheck ( false )->from ( array (
				"h" => "module_home_wallpaper" 
		), array (
				"h.home_wallpaper_id" => "home_wallpaper_id",
				"h.status" => "status",
				"h.order" => "order" 
		) )->joinLeft ( array (
				"hd" => "module_home_wallpaper_detail" 
		), "hd.home_wallpaper_id = h.home_wallpaper_id AND hd.language_id=" . $active_lang_id, array (
				"hd.image_title" => "image_title",
				"hd.home_wallpaper_detail_id" => "home_wallpaper_detail_id",
				"image_ipad",
				"image_iphone",
				"image_ipad_3",
				"image_android",
				"image_ios_6"
		) )->where ( "h.customer_id=" . Standard_Functions::getCurrentUser ()->customer_id );
		
		$response = $homeWallpaperMapper->getGridData ( array (
				'column' => array (
						'id' => array (
								'actions' 
						),
						'replace' => array (
								'h.status' => array (
										'1' => $this->view->translate ( 'Active' ),
										'0' => $this->view->translate ( 'Inactive' ) 
								) 
						),
						'ignore' => array('thumbnail')
				) 
		), "h.customer_id=" . Standard_Functions::getCurrentUser ()->customer_id, $select );
		$customerLanguageMapper = new Admin_Model_Mapper_CustomerLanguage ();
		
		$select = $customerLanguageMapper->getDbTable ()->select ( false )->setIntegrityCheck ( false )->from ( array (
				"l" => 'language'
		), array (
				"l.language_id" => "language_id",
				"l.title" => "title",
				"logo" => "logo" 
		) )->joinLeft ( array (
				"cl" => "customer_language" 
		), "l.language_id = cl.language_id", array (
				"cl.customer_id" 
		) )->where ( "cl.customer_id=" . Standard_Functions::getCurrentUser ()->customer_id );
		$languages = $customerLanguageMapper->getDbTable ()->fetchAll ( $select )->toArray ();
		
		$rows = $response ['aaData'];
		$image_dir = Standard_Functions::getResourcePath () . "home-wallpaper/wallpapers/C" . Standard_Functions::getCurrentUser ()->customer_id . "/";
		$image_uri = "resource/home-wallpaper/wallpapers/C" . Standard_Functions::getCurrentUser ()->customer_id . "/";
		foreach ( $rows as $rowId => $row ) {
			$edit = array ();
			if($row [4] ["hd.home_wallpaper_detail_id"]=="") {
				$mapper = new HomeWallpaper_Model_Mapper_HomeWallpaperDetail();
				$details = $mapper->fetchAll("home_wallpaper_id=".$row [4] ["h.home_wallpaper_id"]." AND language_id=".$default_lang_id);
				if(is_array($details)) {
					$details = $details[0];
					$row[4]["image_title"] = $row[1] = $details->getImageTitle();
					$row [4] ["image_ipad"] = $details->getImageIpad();
					$row [4] ["image_iphone"] = $details->getImageIphone();
					$row [4] ["image_ipad_3"] = $details->getImageIpad3();
					$row [4] ["image_android"] = $details->getImageAndroid();
					$row [4] ["image_ios_6"] = $details->getImageIos6();
				}
			}
			$response ['aaData'] [$rowId] = $row;
			if ($languages) {
				foreach ( $languages as $lang ) {
					$editUrl = $this->view->url ( array (
							"module" => "home-wallpaper",
							"controller" => "index",
							"action" => "edit",
							"id" => $row [4] ["h.home_wallpaper_id"],
							"lang" => $lang ["l.language_id"] 
					), "default", true );
					$edit [] = '<a href="' . $editUrl . '" ><img src="images/lang/' . $lang ["logo"] . '" alt="' . $lang ["l.title"] . '" /></a>';
				}
			}
			$model = $row [4];
			$image_path = "";
			$ext_ipad = array_pop ( explode ( ".", $model ["image_ipad"] ) );
			$ext_iphone = array_pop ( explode ( ".", $model ["image_iphone"] ) );
			$ext_ipad_3 = array_pop ( explode ( ".", $model ["image_ipad_3"] ) );
			$ext_android = array_pop ( explode ( ".", $model ["image_android"] ) );
			$ext_ios_6 = array_pop ( explode ( ".", $model ["image_ios_6"] ) );
			if ($model ["image_ipad"]!="" && file_exists ( $image_dir . str_replace ( "." . $ext_ipad, "_thumb." . $ext_ipad, $model ["image_ipad"] ) )) {
				$image_path = str_replace ( "." . $ext_ipad, "_thumb." . $ext_ipad, $model ["image_ipad"] );
			} else if ($model ["image_iphone"] && file_exists ( $image_dir . str_replace ( "." . $ext_iphone, "_thumb." . $ext_iphone, $model ["image_iphone"] ) )) {
				$image_path = str_replace ( "." . $ext_iphone, "_thumb." . $ext_iphone, $model ["image_iphone"] );
			} else if ($model ["image_ipad_3"]  && file_exists ( $image_dir . str_replace ( "." . $ext_ipad_3, "_thumb." . $ext_ipad_3, $model ["image_ipad_3"] ) )) {
				$image_path = str_replace ( "." . $ext_ipad_3, "_thumb." . $ext_ipad_3, $model ["image_ipad_3"] );
			} else if ($model ["image_android"] && file_exists ( $image_dir . str_replace ( "." . $ext_android, "_thumb." . $ext_android, $model ["image_android"] ) )) {
				$image_path = str_replace ( "." . $ext_android, "_thumb." . $ext_android, $model ["image_android"] );
			} else if ($model ["image_ios_6"] && file_exists ( $image_dir . str_replace ( "." . $ext_android, "_thumb." . $ext_android, $model ["image_ios_6"] ) )) {
				$image_path = str_replace ( "." . $ext_android, "_thumb." . $ext_android, $model ["image_ios_6"] );
			}
			if ($image_path != "") {
				$response ['aaData'] [$rowId] [0] = "<img src='" . $image_uri . $image_path . "' title='" . $model ["hd.image_title"] . "' />";
			} else {
				$response ['aaData'] [$rowId] [0] = "No Image Found";
			}
			
			$deleteUrl = $this->view->url ( array (
					"module" => "home-wallpaper",
					"controller" => "index",
					"action" => "delete",
					"id" => $row [4] ["h.home_wallpaper_id"] 
			), "default", true );
			$defaultEdit = '<div id="editLanguage">&nbsp;<div class="flag-list">'.implode("",$edit).'</div></div>';
			$delete = '<a href="' . $deleteUrl . '" class="grid_delete" >Delete</a>';
			$sap = '';
			$response ['aaData'] [$rowId] [4] = $defaultEdit . $sap . $delete;
		}
		
		$jsonGrid = Zend_Json::encode ( $response );
		$this->_response->appendBody ( $jsonGrid );
	}
}

