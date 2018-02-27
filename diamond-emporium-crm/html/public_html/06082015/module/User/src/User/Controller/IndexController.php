<?php


namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{
	/**
	 * Creates User listing view
	 */
    public function indexAction()
    {
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('config');
			
			$masterPasswordForm = $sm->get('User\Form\MasterPasswordForm');
			
			return array('recordsPerPage' => $config['recordsPerPage'], 'identity' => $identity, 'masterPasswordForm' => $masterPasswordForm);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }

	/**
	 * Fetch users and print it in json format for JQXGrid
	 */	
	public function ajaxuserlistAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
    		$params = $this->getRequest()->getQuery()->toArray();
			
			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
			$keyword = $params['keyword'];
			
			$sortdatafield = $params['sortdatafield'];
			$sortorder = $params['sortorder'];
			
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			
			$objUserTable = $sm->get('User\Model\UserTable');
			
			$userArr = $objUserTable->fetchAll($limit, $offset, $keyword,  $sortdatafield, $sortorder);
			
			echo json_encode($userArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Print user form HTML
	 */
	public function userformAction(){
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$config = $sm->get('Config');
			
			$identity = $sm->get('AuthService')->getIdentity();
			
			$viewRender = $sm->get('ViewRenderer');
    		
			$request = $this->getRequest();
			
			$htmlViewPart = new ViewModel();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				
				$objUserTable = $sm->get('User\Model\UserTable');
				$userData = (array)$objUserTable->fetchUserById($posts['user_id']);
				
				$userForm = $sm->get('User\Form\UserForm');
				$userForm->setData($userData);				
				
				$htmlViewPart->setTemplate('user/index/userform')
							 ->setTerminal(true)
							 ->setVariables(array('userForm' => $userForm, 'email' => $userData['email'], 'color' => $userData['color']));
			}
			
			$html = $viewRender->render($htmlViewPart);	
			
			return $this->getResponse()->setContent($html);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Store user data in db
	 */
	public function saveuserAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
			
				$objUserTable = $sm->get('User\Model\UserTable');
				if(isset($posts['password']) && !empty($posts['password']))
					$posts['password'] = md5($posts['password']);
				echo $objUserTable->saveUser($posts);
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Check duplicate email
	 */
	public function checkduplicateemailAction(){
		try{
			$sm = $this->getServiceLocator();
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$objUserTable = $sm->get('User\Model\UserTable');			
				$result = (array)$objUserTable->checkDuplicateEmail($posts['email'], $posts['user_id']);
				echo $result['counter'];
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Save master passworn in db
	 */
	 
	 public function setmasterpassAction(){
	 	try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
			
				$objUserTable = $sm->get('User\Model\UserTable');
				
				echo $objUserTable->saveMasterPassword($posts['mp_password']);
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
}
