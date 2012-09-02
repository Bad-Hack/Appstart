<?php
class Admin_Model_Mapper_CustomerModule extends Standard_ModelMapper
{
	protected $_dbTableClass = "Admin_Model_DbTable_CustomerModule";
	
	public function inactivateModulesByTemplateId($template_id,$ignore_modules)
	{
		$customerMapper = new Admin_Model_Mapper_Customer();
		$templateCustomer = $customerMapper->fetchAll("template_id=".$template_id);
		foreach($templateCustomer as $customer) {
			$inactivateList = $this->getDbTable()->fetchAll("customer_id = ".$customer." AND module_id not in(".$ignore_modules.") AND status=1");
			foreach($inactivateList as $inactive) {
				$inactive->setStatus(0);
				$inactive->save();
			}
		}
	}
	
	public function activateModulesByTemplateId($template_id,$ignore_modules)
	{
		$customerMapper = new Admin_Model_Mapper_Customer();
		$templateCustomer = $customerMapper->fetchAll("template_id=".$template_id);
		foreach($templateCustomer as $customer) {
			$activateList = $this->getDbTable()->fetchAll("customer_id = ".$customer." AND module_id in(".$ignore_modules.") AND status=0");
			foreach($activateList as $active) {
				$active->setStatus(0);
				$active->save();
			}
		}
	}
}