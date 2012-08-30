<?php
class Admin_Model_Mapper_TemplateModule extends Standard_ModelMapper {
	protected $_dbTableClass = "Admin_Model_DbTable_TemplateModule";
	
	public function findByTemplateAndModuleId($template_id,$module_id) {
		return  new $this->_modelClass ($this->getDbTable()->fetchRow("template_id=$template_id AND module_id=$module_id")->toArray());
	}
}