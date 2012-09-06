<?php
class Admin_Model_Mapper_Customer extends Standard_ModelMapper {
	protected $_dbTableClass = "Admin_Model_DbTable_Customer";
	
	/**
	 * Save customer and user information with the help of options provided
	 * 
	 * @param array $options
	 * @throws Zend_Exception
	 * @throws Exception
	 * @return multitype:Array
	 */
	public function saveCustomer(array $options = array()) {
		$returnData = array();
		if (empty ( $options ))
			throw new Zend_Exception ( "Invalid Options provided to save customer. Please provide options for Customer and User tables" );
			
			// Begin the transaction
		$db = $this->getDbTable ()->getAdapter ();
		$db->beginTransaction ();
		try {
			$customer = new Admin_Model_Customer ();
			
			// Set the options user Customer
			$customer->setOptions ( $options );
			$customer->setLastUpdatedBy ( Standard_Functions::getCurrentUser ()->system_user_id );
			$customer->setCreatedBy ( Standard_Functions::getCurrentUser ()->system_user_id );
			
			// Save the customer and update the customer model
			$customer = $customer->save ();
			// Setting the User Model
			$user = new Default_Model_User ();
			$userOptions = $options;
			$userOptions ["name"] = $options ["customer_name"];
			$userOptions ["phone"] = $options ["contact_person_phone"];
			$userOptions ["email"] = $options ["contact_person_phone"];
			
			// Setting the UserGroup Model
			$userGroup = new Default_Model_UserGroup ();
			if($options['customer_id']!="" && $options['customer_id']!=null){
				$userGroup->setOptions ( array (
						'customer_id' => $customer->getCustomerId (),
						'name' => 'Administrator',
						'last_updated_at' => Standard_Functions::getCurrentDateTime (),
						'created_at' => Standard_Functions::getCurrentDateTime ()
				) );
				$userGroup = $userGroup->save ();
				
				$userOptions ["user_group_id"] = $userGroup->getUserGroupId ();
				$userOptions ["customer_id"] = $customer->getCustomerId ();
				
			} else {
				
			}
			// Save the user Group and update the usergroup model
					
			
			// Set the options for user
			$user->setOptions ( $userOptions );
			$user = $user->save ();
			
			$user->setLastUpdatedBy ( $user->getUserId () );
			$user->setCreatedBy ( $user->getUserId () );
			$user = $user->save ();
			
			// Set created and updated by for user group
			$userGroup->setLastUpdatedBy ( $user->getUserId () );
			$userGroup->setCreatedBy ( $user->getUserId () );
			$userGroup->save ();
			
			$customer->setUserId ( $user->getUserId () );
			$customer->save ();
			
			// Update User Group Modules according to the template_module table
			$userGroupModule = new Default_Model_UserGroupModule ();
			$templateModuleMapper = new Admin_Model_Mapper_TemplateModule();
			
			$templateModuleSql = $this->getDbTable()->select()
			->setIntegrityCheck(false)
			->from(array('tm' => 'template_module'),'*')
			->join(array("m"=>"module"), " m.module_id = tm.module_id ")
			->where(' m.status = 1 AND tm.status = 1 ');

			$templateModules = $templateModuleMapper->fetchAll($templateModuleSql);
			
			$userId = $user->getUserId();  
			$currentDateTime = Standard_Functions::getCurrentDateTime();
			foreach($templateModules as $templateModule){
				$userGroupModuleOptions = array(
						'user_group_module_id' => "",
						'user_group_id' => $userGroup->getUserGroupId(),
						'module_id' => $templateModule->getModuleId(),
						'last_updated_by' => $userId,
						'created_by' => $userId,
						'last_updated_at' => $currentDateTime,
						'created_at' => $currentDateTime,
				);
				$userGroupModule->setOptions($userGroupModuleOptions);
				$userGroupModule->save();
			}
			$db->commit ();
			$returnData['customer'] = $customer->toArray();
			$returnData['user'] = $user->toArray();
		} catch ( Exception $ex ) {
			$db->rollBack ();
			throw $ex;
		}
		return $returnData;
	}
}