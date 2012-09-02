<?php
class Admin_Model_Mapper_TemplateModule extends Standard_ModelMapper {
	protected $_dbTableClass = "Admin_Model_DbTable_TemplateModule";
	
	public function findByTemplateAndModuleId($template_id,$module_id) {
		return $this->getDbTable()->fetchRow("template_id=$template_id AND module_id=$module_id");
	}
	
	public function addModules($template_id,$arrModules) {
		foreach($arrModules as $module_id) {
			$model = $this->findByTemplateAndModuleId($template_id, $module_id);
			if($model != null) {
				$model = new $this->_modelClass($model->toArray());
				$model->setStatus(1);
				$model->save();
			} else {
				$model = new $this->_modelClass();
				$model->setModuleId($module_id);
				$model->setTemplateId($template_id);
				$model->setStatus(1);
				
				$auth = Zend_Auth::getInstance();
				$activeUser = $auth->getStorage ()->read ()->user_id;
				
				$model->setLastUpdatedBy($activeUser);
				$model->setLastUpdatedAt(date("Y-m-d h:i:s"));
				$model->setCreatedBy($activeUser);
				$model->setCreatedAt(date("Y-m-d h:i:s"));
				
				$model->save();
			}
				
		}
	}	
}