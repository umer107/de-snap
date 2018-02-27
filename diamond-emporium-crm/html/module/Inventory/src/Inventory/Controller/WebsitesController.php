<?php

namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class WebsitesController extends AbstractActionController{

	public function indexAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();

			$objWebsiteLinksTable = $this->getServiceLocator()->get('Inventory\Model\WebsiteLinksTable');
			$results = $objWebsiteLinksTable->fetchAll();
			return array('data' => $results,'loginUserId' => $identity['user_id'], 'identity' => $identity);
		}catch(Exception $e){
			\De\Log::logApplicationInfo("Caught Exception (While fetching Websites): " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
		}
	}

}
