<?php
/**
 * Default controller to manage login
 */

namespace AuthACL\Controller;

use Zend\Validator\File\Md5;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Authentication\Adapter\DbTable as AuthAdapter;

use AuthACL\Form\LoginForm;
use AuthACL\Model\Login;

class IndexController extends AbstractActionController
{
	/**
	 * Represents landing / login page
	 */
	public function indexAction()
    {
		try{
			/*$mysampleListener = $this->getServiceLocator()->get('AuthenticateAccessControlListener');
			$this->getEventManager()->attachAggregate($mysampleListener);
			
			$this->getEventManager()->trigger('dispatch', $this);*/
			
			$message = '';
			
			$form = new LoginForm();
			$request = $this->getRequest();
			$sm = $this->getServiceLocator();
			
			$authService = $sm->get('AuthService');
			$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
			
			if($authService->hasIdentity()){
				return $this->redirect()->toRoute('customer/dashboard');
				exit;
			}
					
			if($request->isPost()){
				$loginFilter = new Login();
				$form->setInputFilter($loginFilter->getInputFilter());
				 
				$data = $request->getPost();
				$form->setData($request->getPost());
				
				if($form->isValid()){
					
					//check authentication...
					$authService->getAdapter()
										   ->setIdentity($request->getPost('email'))
										   ->setCredential($request->getPost('password'));
					$result = $authService->authenticate();
	
					/*foreach($result->getMessages() as $message)
					{
						//save message temporary into flashmessenger
						$this->flashmessenger()->addMessage($message);
					}*/
	
					if ($result->isValid()) {
						
						$storageData = (array)$authService->getAdapter()->getResultRowObject(null, array('password'));
						$authService->getStorage()->write($storageData);
						return $this->redirect()->toRoute('index/dashboard');
						exit;
						//check if it has rememberMe :
						/*if ($request->getPost('rememberme') == 1 ) {
							$this->getSessionStorage()->setRememberMe(1);
							//set storage again
							$authService->setStorage($this->getSessionStorage());
						}
						$authService->getStorage()->write($request->getPost('email'));*/
					}else{
						 //$this->flashMessenger()->addMessage('Invalid Username / Password');
						 $message = 'Invalid Username / Password';
					}
				}
			}
			
			return array('form' => $form, 'message' => $message);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
    
	/**
	 * Destroy session to logout
	 */
	public function logoutAction()
	{
		try{
			$sessionStorage = $this->getServiceLocator()->get('AuthACL\Model\MyAuthStorage');
			$authService = $this->getServiceLocator()->get('AuthService');
			
			$sessionStorage->forgetMe();
			$authService->clearIdentity();
			
			$this->flashmessenger()->addMessage("You've been logged out");
			return $this->redirect()->toRoute('authacl');
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}
