<?php
class Default_LoginController extends Zend_Controller_Action {
	public function init() {
		
	}
	public function indexAction() {
		// action body
		$request = $this->getRequest ();
		$form = new Default_Form_Login ();
		
		if ($this->getRequest ()->isPost ()) {
			if ($form->isValid ( $request->getPost () )) {
				// set up the auth adapter
				// get the default db adapter
				$db = Zend_Db_Table::getDefaultAdapter ();
				// create the auth adapter
				$authAdapter = new Zend_Auth_Adapter_DbTable ( $db, 'user', 'username', 'password', 'MD5(CONCAT(?,username))' );
				
				$authAdapter->setIdentity ( $request->getPost ( 'username' ) );
				$authAdapter->setCredential ( $request->getPost ( 'password' ) );
				// authenticate
				$result = $authAdapter->authenticate (); // var_dump($result);exit();
				if ($result->isValid ()) {
					
					// store the username, first and last names of the user
					$auth = Zend_Auth::getInstance ();
					$identity = new stdClass ();
					$identity = $authAdapter->getResultRowObject ( array (
							'user_id',
							'username',
                            'name',
                            'user_group_id',
					) );
					
					$groupMapper = new Default_Model_Mapper_UserGroup();
					$group = $groupMapper->find($identity->user_group_id);
					$customerMapper = new Admin_Model_Mapper_Customer();
					$customer = $customerMapper->find($group->getCustomerId());
					if($customer->getStatus()==1) {
						if($group) {
							$identity->customer_id = $group->getCustomerId();
						} else {
							$identity->customer_id = 0;
						}
						$identity->group_id = $identity->user_group_id;
						$storage = $auth->getStorage ();
						$storage->write ( $identity );
						
						if ($request->getPost ( 'remember' ) == "1") {
							// Zend_Session::rememberMe();
							$url = $this->getRequest ()->getScheme () . '://' . $this->getRequest ()->getHttpHost () . str_replace ( "/login", "", $this->getRequest ()->getRequestUri () );
							
							setcookie ( "username", base64_encode ( $request->getPost ( 'username' ) ), time () + ((24 * 3600) * 7) );
							setcookie ( "password", base64_encode ( $request->getPost ( 'password' ) ), time () + ((24 * 3600) * 7) );
						} 					// }
						else {
							// Zend_Session::forgetMe();
							setcookie ( "username", "", time () + ((24 * 3600) * 7) );
							setcookie ( "password", "", time () + ((24 * 3600) * 7) );
						}
						$this->_redirect ( '/' );
						return;
					} else {
						$this->view->assign ( array (
								'loginMessage' => 'Your Account is Inactivated. Please Contact The Administrator.'
						) );
					}
				} else {
					$this->view->assign ( array (
							'loginMessage' => 'Invalid Username/Password' 
					) );
				}
				$this->_helper->viewRenderer->setRender ( 'index' );
			} else {
				$this->view->assign ( array (
						'loginMessage' => 'Invalid Username/Password' 
				) );
				$this->_helper->viewRenderer->setRender ( 'index' );
			}
		}
		
		if (isset ( $_COOKIE ['username'] ) && isset ( $_COOKIE ['password'] ) && ! $this->getRequest ()->isPost ()) {
			$form->getElement ( "username" )->setValue ( base64_decode ( $_COOKIE ['username'] ) );
			$this->view->assign ( array (
					"password" => base64_decode ( $_COOKIE ['password'] ) 
			) );
		} else if ($this->getRequest ()->isPost ()) {
			$this->view->assign ( array (
					"password" => $this->getRequest ()->getParam ( 'password', "" ) 
			) );
		} else {
			$this->view->assign ( array (
					"password" => "" 
			) );
		}
		
		foreach ($form->getElements() as $element) {
			if($element->getDecorator('Label')) $element->getDecorator('Label')->setTag(null);
		}
		$this->view->form = $form;
	}
	public function logoutAction() {
		$authAdapter = Zend_Auth::getInstance ();
		$identity = $authAdapter->getIdentity ();
		$authAdapter->clearIdentity ();
		$this->_redirect ( '/' );
	}
}

