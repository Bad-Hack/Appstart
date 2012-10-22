<?php
class Events_RestController extends Standard_Rest_Controller {
	/*
	 * (non-PHPdoc) @see Zend_Rest_Controller::getAction()
	 */
	public function getAction() {
		// TODO Auto-generated method stub
		$service = $this->_request->getParam("service",null);
		if($service==null) {
			$this->_sendError("No service called");
		} else {
			if($service == "sync") {
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
	
	protected function _sync() {
		$customer_id = $this->_request->getParam("customer_id",null);
		$device_type = $this->_request->getParam("device_type",null);
		if($customer_id===null || $device_type == null) {
			$this->_sendError("Invalid request");
		} else {
			try{
				$mapper = new Admin_Model_Mapper_Customer();
				$customer = $mapper->find($customer_id);
				if($customer) {
					$response = array();
					$eventMapper = new Events_Model_Mapper_ModuleEvents();
					$eventModel = $eventMapper->fetchAll("customer_id=".$customer_id);
					if($eventModel) {
						foreach($eventModel as $event) {
							$eventDetails = array();
							$eventDetailMapper = new Events_Model_Mapper_ModuleEventsDetail();
							$eventDetailModel = $eventDetailMapper->fetchAll("module_events_id=".$event->getEventsId());
							if($eventDetailModel) {
								foreach($eventDetailModel as $event_detail) {
									$details = $event_detail->toArray();
									if(isset($details["image"])) {
										$details["image"] = "resource/event/images/".$details["image"];
									}
									$eventDetails[] = $details;
								}
							}
							
							$response["data"][] = array("tbl_module_events"=>$event->toArray(),"tbl_module_events_detail"=>$eventDetails);
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