<?php


namespace Alert\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
	/**
	 * Creates Alert listing view for user
	 */
    public function indexAction()
    {
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('config');
		
			return array('recordsPerPage' => $config['recordsPerPage']);
		} catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }

	/**
	 * Fetch alerts and print it in json format for JQXGrid
	 */	
    public function ajaxuseralertlistAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();

			$params = $this->getRequest()->getQuery()->toArray();

			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
				
			$sortdatafield = $params['sortdatafield'];
			$sortorder = $params['sortorder'];
				
			settype($limit, 'int');
			$offset = $pagenum * $limit;
				
			$objAlertTable = $sm->get('Alert\Model\AlertTable');
			
			$alertArr = $objAlertTable->fetchAllForUser($limit, $offset, $identity['user_id'], $identity['role_id'], $sortdatafield, $sortorder);
			
			echo json_encode($alertArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
    
	/**
	 * Fetch All alerts and print it in json format for JQXGrid
	 */	
    public function ajaxallalertlistAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();

			$params = $this->getRequest()->getQuery()->toArray();

			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
				
			$sortdatafield = $params['sortdatafield'];
			$sortorder = $params['sortorder'];
				
			settype($limit, 'int');
			$offset = $pagenum * $limit;
				
			$objAlertTable = $sm->get('Alert\Model\AlertTable');
			
			$alertArr = $objAlertTable->fetchAll($limit, $offset, $sortdatafield, $sortorder);
			
			echo json_encode($alertArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

	/**
	 * Get count of unread alerts for user
	 */	
	public function ajaxuseralertcountAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$objAlertTable = $sm->get('Alert\Model\AlertTable');
			
			$alertArr = $objAlertTable->countUnseenForUser($identity['user_id'], $identity['role_id']);
			
			echo json_encode($alertArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

    /**
	 * Clear a specific alert ID.
	 */	
	public function ajaxuseralertclearAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			$objAlertTable = $sm->get('Alert\Model\AlertTable');
			/* 
			 * TODO: currently there is no check to see if the current user actually owns this alert
			 * or is in the correct role.
			 */
			echo $objAlertTable->clearAlert($this->getRequest()->getPost('id'), $identity['user_id']);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}
