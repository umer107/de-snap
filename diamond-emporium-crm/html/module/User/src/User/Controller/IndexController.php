<?php


namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{
   //Upload File Method
  public static function uploadFileUser($file, $targetFolder, $config, $fileTypes = null){
		try{
			
            $targetFileName = sha1($file['name'] . uniqid('',true));	
                        
			$tempFile = $file['tmp_name'];
			
			$objFileInfo = new \SplFileInfo($file['name']);
			$ext = $objFileInfo->getExtension();
			
			$targetPath = $config['documentRoot'] . $targetFolder;			
			
            $targetFile = rtrim($targetPath,'/') . '/' . $targetFileName.'.'.$ext;
			
			// Validate the file type
			if (!empty($fileTypes) && !in_array($ext, $fileTypes)) {
				
				return 1;
			}
			
			if(move_uploaded_file($tempFile, $targetFile)){               
             return $targetFileName.'.'.$ext;
             }                               
			else {                       
              return 2;
             }
				
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
    
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
			
			//$msg = $this->flashMessenger()->getMessages();
			//print_r($msg);exit;
			
			return array('recordsPerPage' => $config['recordsPerPage'], 'identity' => $identity, 'masterPasswordForm' => $masterPasswordForm, 'flashMessages' => $this->flashMessenger()->getMessages());
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
				$user_id = $request->getPost()->toArray()['user_id'];
				
				$userForm = $sm->get('User\Form\UserForm');
				if ($user_id) {
					$objUserTable = $sm->get('User\Model\UserTable');
					$userData = (array)$objUserTable->fetchUserById($user_id);
					$userForm->setData($userData);			
				} else {
					$userForm->get('password')->setAttribute('required', true);
				}
				
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
                            //parse_str($request->getPost()->toArray()['frm_user'], $posts);
				$posts = $request->getPost()->toArray();
                                if($posts['user_budget'] == 0)
                                {
                                    $posts['user_budget'] = '$2-5K';
                                }
                                else if($posts['user_budget'] == 1)
                                {
                                    $posts['user_budget'] = '$5-10K';
                                }
                                else if($posts['user_budget'] == 2)
                                {
                                    $posts['user_budget'] = '$10-20K';
                                }
                                else if($posts['user_budget'] == 3)
                                {
                                    $posts['user_budget'] = '$20K+';
                                }
			        // $File = $request->getFiles()->toArray();
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
	 * Delete user data in db
	 */
	public function deleteuserAction(){		
	   try{
	   		$sm = $this->getServiceLocator();
			$config = $sm->get('Config');
			
			$identity = $sm->get('AuthService')->getIdentity();
	   
			$id = $this->params('id');			
			$objUserTable = $sm->get('User\Model\UserTable');
			$objUserData = $objUserTable->updateUser($id, $identity['user_id']);
			
			$message = 'User deleted successfully';					
			$this->flashMessenger()->addMessage($message);
			return $this->redirect()->toUrl('/users');  
			 
			echo $objUserData; exit;
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
	 
	 /**
	  * User emiail listing
	  */
	   public function emailsAction(){
	 	try{
			$sm = $this->getServiceLocator();
			$config = $sm->get('Config');
			$identity = $sm->get('AuthService')->getIdentity();
			
			return(array('config' => $config));
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
         
         public function uploadfileimageAction(){
	    
             try{ 
               
              // Write your code here

		$sm = $this->getServiceLocator();
		
		$config = $sm->get('Config');
		
		$request = $this->getRequest();
                
		if($request->isPost()){
			
			if(!empty($_FILES)){
				
				
			
				$fileParts = pathinfo($_FILES['item_image']['name']);
			
				$tempFile = $_FILES['item_image']['tmp_name'];
				$targetPath = $config['documentRoot'] . 'profile_image'; //For Production Site
                //$targetPath = $config['documentRootProfile'] . 'profile_image';//For Beta Testing
				$targetFileName =  sha1($file['name'] . uniqid('',true)).$fileParts['extension'];
				$targetFile = rtrim($targetPath,'/') . '/' . $targetFileName;
				
				// Validate the file type
				$fileTypes = array('gif', 'png', 'jpeg', 'jpg'); // File extensions
				
				if (in_array($fileParts['extension'],$fileTypes)) {
					if(move_uploaded_file($tempFile, $targetFile)){

						echo $targetFileName;
					}
				}
			}
		}
		
		exit;
		
	}catch(Exception $e){
		\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
	}


	}
        
        
        //UpdateUserStatusFunction
        
        public function ajaxuserstatusupdateAction(){
            
            try{
                        $config = $this->getServiceLocator()->get('Config');                        
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			$request = $this->getRequest();
                        if($request->isPost()){
                        $post = $request->getPost()->toArray()['status'];
                        $data = array('status' => $post, 'user_id' => $identity['user_id']);                       		
			$objUserTable = $sm->get('User\Model\UserTable');
			$objUserData = $objUserTable->updateUserStatus($data);
                        }
			
                        
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
        }
        
        //GetUserStatus

       public function  ajaxGetUserStatusAction()
       {
           try
           {
               $sm = $this->getServiceLocator();
	       $config = $sm->get('Config');			
	       $identity = $sm->get('AuthService')->getIdentity();
	       $id = $identity['user_id'];			
	       $objUserTable = $sm->get('User\Model\UserTable');
	       $objUserData = $objUserTable->fetchUserById($id);	
	        echo json_encode($objUserData);	
               
               exit;
           }
           catch(Exception $e)
           {
               \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
           }
       }
       
}
