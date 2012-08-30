<?php
class Admin_Model_Mapper_BusinessType extends Standard_ModelMapper {
	protected $_dbTableClass = "Admin_Model_DbTable_BusinessType";
	
	public function getList(array $columns = array(), $where = null){
		$businessTypeModels = $this->fetchAll($where);
		$gridData = array();
		foreach($businessTypeModels as $businessTypeModel) {
			
			$record = array();
			foreach($columns as $column){
				$record[] = $businessTypeModel->get($column);
			}
			$record[] = "";
			$gridData[] = $record;
		}
		
		return $gridData;
	}
}