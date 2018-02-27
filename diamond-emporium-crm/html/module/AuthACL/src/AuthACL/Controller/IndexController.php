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
	
	public function forgotpasswordAction(){
		try{
			return;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function sendresetpassurlAction(){
		try{		
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				
				$objUserTable = $sm->get('User\Model\UserTable');
				$result = $objUserTable->fetchUserDetails(array('email' => $posts['email']));
				
				$reset_pass_code = md5(time().rand(99999, 99999999));
				
				if($objUserTable->saveUser(array('user_id' => $result->user_id, 'reset_pass_code' => $reset_pass_code))){				
					$emailParams['toEmail'] = $posts['email'];
					$emailParams['toName'] = $result['first_name'].' '.$result['last_name'];
					$emailParams['subject'] = 'Reset Password';
					
					$emailParams['message'] = $reset_pass_code;
					$emailParams['template'] = 'reset_password.phtml';
					
					\De\Service\EmailService::sendEmail($config['smtp_details'], $emailParams);
					
					echo 1;
				}
			}
		
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function resetpassAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			return array('reset_pass_code' => $this->params('reset_pass_code'));
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function storeresetpassAction(){
		try{		
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
			
				$objUserTable = $sm->get('User\Model\UserTable');
				$result = $objUserTable->fetchUserDetails(array('reset_pass_code' => $posts['reset_pass_code']));
				
				if($result){
					echo $objUserTable->saveUser(array('user_id' => $result['user_id'], 'password' => md5($posts['password']), 'reset_pass_code' => ''));
				}
			}
		
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}
