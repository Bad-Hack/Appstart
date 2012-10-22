<?php
class Default_RestController extends Standard_Rest_Controller {
	/*
	 * (non-PHPdoc) @see Zend_Rest_Controller::getAction()
	 */
	public function getAction() {
		// TODO Auto-generated method stub
		$service = $this->_request->getParam("service",null);
		if($service==null) {
			$this->_sendError("No service called");
		} else {
			if($service == "authenticate") {
				$this->_authenticate();
			} else if($service == "sync") {
				$this->_sync();
			} else {
				$this->_sendError("Invalid service");
			}
		}
	}
	
	/*
	 * (non-PHPdoc) @see Zend_Rest_Controller::postAction()
	 */
	public function postAction() {
		// TODO Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc) @see Zend_Rest_Controller::putAction()
	 */
	public function putAction() {
		// TODO Auto-generated method stub
	}
	
	/*
	 * (non-PHPdoc) @see Zend_Rest_Controller::deleteAction()
	 */
	public function deleteAction() {
		// TODO Auto-generated method stub
	}
	
	protected function _authenticate() {
		$appAccessID = $this->_request->getParam("app_access_id",null);
		$password = $this->_request->getParam("password",null);
		$response = array();
		if($appAccessID===null || $password === null) {
			$this->_sendError("Invalid request");
		} else {
			try{
				$mapper = new Admin_Model_Mapper_Customer();
				$customer = $mapper->fetchAll("app_access_id='".$appAccessID."'");
				if($customer) {
					$customer = $customer[0];
					$userMapper = new Default_Model_Mapper_User();
					$user = $userMapper->fetchAll("user_id='".$customer->getUserId()."' AND password=MD5(concat('".$password."',username))");
					if($user) {
							
						$data["customer_id"] = $customer->getCustomerId();
						$data["app_access_id"] = $customer->getCustomerId();
						$data["password"] = $password;
						$data["status"] = $customer->getStatus();
						$response["status"] = "success";
						$response["data"] = $data;
						$this->_sendData($response);
							
					} else {
						$this->_sendError("Invalid password");
					}
				} else {
					$this->_sendError("Invalid App Access ID");
				}
			} catch (Exception $ex) {
				$this->_sendError($ex->getMessage());
			}
		}
	}
	
	protected function _sync() {
		$customer_id = $this->_request->getParam("customer_id",null);
		if($customer_id===null) {
			$this->_sendError("Invalid request");
		} else {
			try{
				$mapper = new Admin_Model_Mapper_Customer();
				$customer = $mapper->find($customer_id);
				if($customer) {
					$response = array();
					$moduleMapper = new Admin_Model_Mapper_CustomerModule();
					$customerModule = $moduleMapper->fetchAll("customer_id=".$customer_id);
					if($customerModule) {
						foreach($customerModule as $module) {
							$details = array();
							$customerModuleDetailsMapper = new Admin_Model_Mapper_CustomerModuleDetail();
							$customerModuleDetails = $customerModuleDetailsMapper->fetchAll("customer_module_id=".$customer_id);
							if($customerModuleDetails) {
								foreach($customerModuleDetails as $moduleDetail) {
									$tempDetail = $moduleDetail->toArray();
									//$response["tbl_customer_module_detail"][] = $moduleDetail->toArray();
									if(isset($tempDetail["background_image"]) && $tempDetail["background_image"]!="") {
										$tempDetail["background_image"] = "resource/default/images/background/".$tempDetail["background_image"];
									} else {
										$tempDetail["background_image"]= "";
									}
									$details[] = $tempDetail;
								}
							}
							$module = $module->toArray();
							if(isset($module["icon"]) && $module["icon"]!="") {
								$module["icon"] = "resource/default/images/icon/".$module["icon"];
							} else {
								$module["icon"]= "";
							}
							
							$response["customer_module"][] = array("tbl_customer_module" => $module,
																		"tbl_customer_module_detail" => $details);
						}
					}
										
					$configMapper = new Admin_Model_Mapper_CustomerConfiguration();
					$customerConfig = $configMapper->fetchAll("customer_id=".$customer_id);
					if($customerConfig) {
						foreach($customerConfig as $config) {
							$response["tbl_customer_configuration"][] = $config->toArray();
						}
					}
					
					$languageMapper = new Admin_Model_Mapper_CustomerLanguage();
					$customerLanguage = $languageMapper->fetchAll("customer_id=".$customer_id);
					if($customerLanguage) {
						foreach($customerLanguage as $language) {
							$response["tbl_customer_language"][] = $language->toArray();
						}
					}
					$data["status"] = "success";
					$data["data"] = $response;
					$this->_sendData($data);
				} else {
					$this->_sendError("Invalid customer ID");
				}
			} catch (Exception $ex) {
				$this->_sendError($ex->getMessage());
			}
		}
	}
}