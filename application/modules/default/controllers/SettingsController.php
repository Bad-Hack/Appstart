<?php
class Default_SettingsController extends Zend_Controller_Action {
	public function init() {
		// <?php
	}
	public function indexAction() {
		
			$form   = new Default_Form_Settings();
			$user = new Default_Model_User();
			$user->populate(Standard_Functions::getCurrentUser()->user_id);
			if($this->_request->isPost()){
				if($form->isValid($this->_request->getParams())){
					$request = $this->getRequest ();
					$user->setOptions($form->getValues());
					$user->setLastUpdatedBy ( Standard_Functions::getCurrentUser ()->user_id );
					$user->setLastUpdatedAt ( Standard_Functions::getCurrentDateTime () );
					$user->setPassword (md5($request->getParam ( "password" ). $user->getUsername()));
					$user->save();
				}	
				
			}
			$form->populate($user->toArray());
			$this->view->form = $form;
	}
}
