<?php
/**
 * Controller for Customer
 */

namespace Customer\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Opportunities\Form\OpportunitiesForm;

class IndexController extends AbstractActionController
{
	/**
	 * Creates customer listing view
	 */
    public function indexAction()
    {
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $this->getServiceLocator()->get('Config');
			$leadsTable = $this->getServiceLocator()->get('Customer\Model\LeadsTable');
			$columnList = $leadsTable->listColumns('customers');
			$gridViewTable = $this->getServiceLocator()->get('Customer\Model\GridViewTable');
			$gridViewOptions = $gridViewTable->getGridViews($identity['user_id'], 'customers');
			
			$newCustomerForm = $this->getServiceLocator()->get('Customer\Form\NewCustomerForm');
			
			return array('recordsPerPage' => $config['recordsPerPage'], 'columnList' => $columnList,
						 'form' => $newCustomerForm, 'gridViewOptions' => $gridViewOptions, 'loginUserId' => $identity['user_id']);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	/**
	 * Creates after login landing page
	 */	
	public function dashboardAction()
    {
		try{
			// Write your code here
		
			return;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	/**
	 * Saves grid view for customers
	 */
	public function savemygridviewAction()
    {
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			$request = $this->getRequest();
			 if($request->isXmlHttpRequest()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				$coulmnNames = array_values($data['coulmn_names']);
				$coulmnNames = implode(",", $coulmnNames);
				$savedata['customer_id'] = $identity['user_id'];
				$savedata['view_title'] = $data['view_title'];
				$savedata['columns_list'] = $coulmnNames;
				$savedata['grid_type'] = $data['grid_type'];
				$savedata['hiddenSelectGridView'] = $data['hiddenSelectGridView'];
				$savedata['hiddenGenerateNewGridView'] = $data['hiddenGenerateNewGridView'];
				$gridViewTable = $this->getServiceLocator()->get('Customer\Model\GridViewTable');
				echo $gridViewTable->saveGridView($savedata);
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	/**
	 * Delete grid view
	 */
	
	public function deletegridviewAction()
    {
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			$request = $this->getRequest();
			 if($request->isXmlHttpRequest()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				$gridViewTable = $this->getServiceLocator()->get('Customer\Model\GridViewTable');
				echo $deleteResult = $gridViewTable->deleteGridView($data['id']);
			}
			exit;
		}catch(Exception $e){echo $e->getMessage ();exit;
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	/**
	 * Edit existing grid view
	 */
	
	public function editgridviewAction()
    {
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			$request = $this->getRequest();
			 if($request->isPost()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				$gridViewTable = $this->getServiceLocator()->get('Customer\Model\GridViewTable');
				$gridViewData = (array) $gridViewTable->getGridViewById($data['id']);
				/*
				 * Set the default type for this view so we always default to the last view shown.
				 * TODO: maybe this should be in it's own request that the user can set at will.
				 */
				$gridViewTable->setGridViewDefaultId($identity['user_id'], $gridViewData['id'], $gridViewData['grid_type']);
			 }
			
			echo json_encode($gridViewData);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	/**
	 * Fetch data on page load and alse fetch data for search to populate grid
	 */
    
	public function ajaxcustomerslistAction(){
    	try{
			// Write your code here
			$keyword = '';
    		$params = $this->getRequest()->getQuery()->toArray();
			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
			$keyword = $params['keyword'];
			$sortdatafield = $params['sortdatafield'];
			$sortorder = $params['sortorder'];
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');
			if($keyword != ''){
				$offset = 0;
			}
			$customersArr = $customersTable->fetchCustomers($limit, $offset, $keyword, $sortdatafield, $sortorder);
			echo json_encode($customersArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
	
	/**
	 * Fetch customer details
	 */
    
    public function customerdetailsAction()
    {
    	try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$authService = $sm->get('AuthService');
			$identity = $authService->getIdentity();
    		$id = $this->params('id');
			$partnerFormData = $partnerData = null;
			$canDelete = $sm->get('ControllerPluginManager')->get('AuthPlugin')->checkResource($authService, 'Customer\Controller\Index::deletecustomer');
			
			$config = $this->getServiceLocator()->get('Config');
			
    		$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');
			$customerFormData = $customerData = $customersTable->fetchCustomerdetails($id);
			
			$customerFormData['wedding_anniversary_date'] = empty($customerFormData['wedding_anniversary_date']) ? '' : date($config['formDateFormat'], strtotime($customerFormData['wedding_anniversary_date']));
			$customerFormData['engagement_anniversary_date'] = empty($customerFormData['engagement_anniversary_date']) ? '' : date($config['formDateFormat'], strtotime($customerFormData['engagement_anniversary_date']));
			$customerFormData['date_of_birth'] = empty($customerFormData['date_of_birth']) ? '' : date($config['formDateFormat'], strtotime($customerFormData['date_of_birth']));
			
			$customerData['wedding_anniversary_date'] = empty($customerData['wedding_anniversary_date']) ? '' : date($config['phpDateFormat'], strtotime($customerData['wedding_anniversary_date']));
			$customerData['engagement_anniversary_date'] = empty($customerData['engagement_anniversary_date']) ? '' : date($config['phpDateFormat'], strtotime($customerData['engagement_anniversary_date']));
			$customerData['date_of_birth'] = empty($customerData['date_of_birth']) ? '' : date($config['phpDateFormat'], strtotime($customerData['date_of_birth']));
			
			if(!empty($customerData['partner_id'])){
				$partnerFormData = $partnerData = $customersTable->fetchCustomerdetails($customerData['partner_id']);
				
				$partnerFormData['wedding_anniversary_date'] = empty($partnerFormData['wedding_anniversary_date']) ? '' : date($config['formDateFormat'], strtotime($partnerFormData['wedding_anniversary_date']));
				$partnerFormData['engagement_anniversary_date'] = empty($partnerFormData['engagement_anniversary_date']) ? '' : date($config['formDateFormat'], strtotime($partnerFormData['engagement_anniversary_date']));
				$partnerFormData['date_of_birth'] = empty($partnerFormData['date_of_birth']) ? '' : date($config['formDateFormat'], strtotime($partnerFormData['date_of_birth']));
				
				$partnerData['wedding_anniversary_date'] = empty($partnerData['wedding_anniversary_date']) ? '' : date($config['phpDateFormat'], strtotime($partnerData['wedding_anniversary_date']));
				$partnerData['engagement_anniversary_date'] = empty($partnerData['engagement_anniversary_date']) ? '' : date($config['phpDateFormat'], strtotime($partnerData['engagement_anniversary_date']));
				$partnerData['date_of_birth'] = empty($partnerData['date_of_birth']) ? '' : date($config['phpDateFormat'], strtotime($partnerData['date_of_birth']));
			}
			
			$partnerFormData['customer_id'] = $partnerData['customer_id'] = $id;
			$leadsTable = $this->getServiceLocator()->get('Customer\Model\LeadsTable');
			$columnList = $leadsTable->listColumns('opportunities');
			$gridViewTable = $this->getServiceLocator()->get('Customer\Model\GridViewTable');
			$gridViewOptions = $gridViewTable->getGridViews($identity['user_id'], 'opportunities');
			
			$form = $this->getServiceLocator()->get('Customer\Form\CustomerForm');
			$form->setData($customerFormData);
			
			$partnerForm = $this->getServiceLocator()->get('Customer\Form\PartnerForm');			
			$partnerForm->setData($partnerFormData);
			
			$opportunitiesForm = $this->getServiceLocator()->get('Opportunities\Form\OpportunitiesForm');

			/* Get all open tasks for this specific customer */
			
			$tasksTable = $sm->get('Task\Model\TasksTable');
			$tasks = $tasksTable->fetchAll($id, 'customer', 1);
				
			/* Get all closed tasks for this specific customer */
					
			$closedTasks = $tasksTable->fetchAll($id, 'customer', 2);
			foreach($closedTasks['data'] as $key => $value){
				$closedTasks['data'][$key]['is_overdue'] = \De\Lib::isTaskOverDue($value);
				
				if(!empty($value['lead_name']))
					$closedTasks['data'][$key]['task_created_by_fullname'] = 'Lead >> '.$value['lead_name'];
				elseif(!empty($value['opportunity_name']))
					$closedTasks['data'][$key]['task_created_by_fullname'] = 'Opportunity >> '.$value['opportunity_name'];
				elseif(!empty($value['customer_name']))
					$closedTasks['data'][$key]['task_created_by_fullname'] = 'Customer >> '.$value['customer_name'];
			}

			/* Get all opportunities for this customer and any open tasks for each */
			
			$opportunitiesTable = $this->getServiceLocator()->get('Opportunities\Model\OpportunitiesTable');
			$opportunities = $opportunitiesTable->fetchAll(null, null, null, $id);
			foreach($opportunities['Rows'] as $opportunity) {
				$oppTasks = $tasksTable->fetchAll($opportunity['id'], 'opportunity', 1);
				$tasks['data'] = array_merge($tasks['data'], $tasksTable->fetchAll($opportunity['id'], 'opportunity', 1)['data']);
			}

			/* Reset count for merged tasks */
			$tasks['total_count'] = count($tasks['data']);
				
			foreach($tasks['data'] as $key => $value){
				$tasks['data'][$key]['is_overdue'] = \De\Lib::isTaskOverDue($value);
			
				if(!empty($value['lead_name']))
					$tasks['data'][$key]['task_created_by_fullname'] = 'Lead >> '.$value['lead_name'];
					elseif(!empty($value['opportunity_name']))
					$tasks['data'][$key]['task_created_by_fullname'] = 'Opportunity >> '.$value['opportunity_name'];
					elseif(!empty($value['customer_name']))
					$tasks['data'][$key]['task_created_by_fullname'] = 'Customer >> '.$value['customer_name'];
			}
			
			$usersTable = $this->getServiceLocator()->get('Customer\Model\UsersTable');
			$usersList = $usersTable->fetchUsersForTasks();
			$ownerOptions = $usersTable->fetchSelectOptions();
				
			$tasksCategoryTable = $this->getServiceLocator()->get('Task\Model\TasksCategoryTable');
			$CategoryList = $tasksCategoryTable->fetchAll();
			$tasksSubjectTable = $this->getServiceLocator()->get('Task\Model\TasksSubjectTable');
			$subjectList = $tasksSubjectTable->fetchAll();
			$tasksPriorityTable = $this->getServiceLocator()->get('Task\Model\TasksPriorityTable');
			$priorityList = $tasksPriorityTable->fetchAll();
			
			$newCustomerForm = $this->getServiceLocator()->get('Customer\Form\NewCustomerForm');
			$newCustomerForm->get('save')->setAttribute('onclick', 'createNewCustomer($(\'#frm_new_customer\'));');
			
			foreach($tasks['data'] as $key => $value){
				$tasks['data'][$key]['is_overdue'] = \De\Lib::isTaskOverDue($value);
			}
			
			$newOrderForm = $sm->get('Order\Form\OrderForm');
			$newOrderForm->get('cust_id')->setAttributes(array('value' => $id));
							
			return array('customerData' => $customerData, 'partnerData' => $partnerData,
						 'form' => $form, 'partnerForm' => $partnerForm,
						 'recordsPerPage' => $config['recordsPerPage'],
						 'columnList' => $columnList, 'gridViewOptions' => $gridViewOptions,
						 'loginUserId' => $identity['user_id'], 'oppForm' => $opportunitiesForm,
						 'tasks' => $tasks, 'closedTasks' => $closedTasks, 'config' => $config,
						 'usersList' => $usersList, 'CategoryList' => $CategoryList, 'subjectList' => $subjectList,
						 'priorityList' => $priorityList, 'newCustomerForm' => $newCustomerForm,
						 'newOrderForm' => $newOrderForm, 'identity' => $identity,
						 'ownerOptions' => $ownerOptions, 'canDelete' => $canDelete
			);
			
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		} 
    }
	
	/**
	 * Lookup for view and select partners
	 */
    
	public function ajaxpartnerslookupAction(){
    	try{
			// Write your code here
			
    		$config = $this->getServiceLocator()->get('Config');
    		
    		$params = $this->getRequest()->getQuery()->toArray();
			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
			settype($limit, 'int');
			$offset = $pagenum * $limit;

			$keyword = $params['keyword'];
			$customer_id = $this->params('id');
			settype($customer_id, 'int');
			
			$sortdatafield = $params['sortdatafield'];
			$sortorder = $params['sortorder'];
			
			$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');
			$customersArr = $customersTable->partnerLookup($limit, $offset, $customer_id, $keyword, $sortdatafield, $sortorder);			
						
			foreach($customersArr['Rows'] as $key => $value){
				$customersArr['Rows'][$key]['wedding_anniversary_date'] = empty($value['wedding_anniversary_date']) ? '' : date($config['phpDateFormat'], strtotime($value['wedding_anniversary_date']));
				$customersArr['Rows'][$key]['engagement_anniversary_date'] = empty($value['engagement_anniversary_date']) ? '' : date($config['phpDateFormat'], strtotime($value['engagement_anniversary_date']));
				$customersArr['Rows'][$key]['date_of_birth'] = empty($value['date_of_birth']) ? '' : date($config['phpDateFormat'], strtotime($value['date_of_birth']));
			}
			
			echo json_encode($customersArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
	
	/**
	 * 
	 */
	
	public function ajaxsearchgridsAction(){
    	try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			$request = $this->getRequest();
			 if($request->isPost()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				if($data['grid_type'] == 'leads'){
					$gridViewTable = $this->getServiceLocator()->get('Customer\Model\GridViewTable');
					$gridViewData = (array) $gridViewTable->getGridViewById($data['id']);
				}
			}
			
			echo json_encode($gridViewData);
			//exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
	
	public function ajaxrecordscountAction(){
    	try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			$request = $this->getRequest();
			 if($request->isPost()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				$customerId = $data['customerId'];
				//if($data['grid_type'] == 'gridType'){
					$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');
					$countsData = $customersTable->fetchAllCounts($data['grid_type'], $customerId);
				//}
			}
			echo json_encode($countsData);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	/**
	 * Store customer into database
	 */
    
    public function savecustomerAction(){
    	try{
    		$request = $this->getRequest();
    		if($request->isPost()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				
				if(!empty($data['wedding_anniversary_date'])){
					list($d ,$m, $y) = explode('/', $data['wedding_anniversary_date']);				
					$data['wedding_anniversary_date'] = date('Y-m-d', strtotime("$y-$m-$d"));
				}else{
					$data['wedding_anniversary_date'] = NULL;
				}
				
				if(!empty($data['engagement_anniversary_date'])){
					list($d ,$m, $y) = explode('/', $data['engagement_anniversary_date']);				
					$data['engagement_anniversary_date'] = date('Y-m-d', strtotime("$y-$m-$d"));
				}else{
					$data['engagement_anniversary_date'] = NULL;
				}
				
				if(!empty($data['date_of_birth'])){
					list($d ,$m, $y) = explode('/', $data['date_of_birth']);				
					$data['date_of_birth'] = date('Y-m-d', strtotime("$y-$m-$d"));
				}else{
					$data['date_of_birth'] = NULL;
				}
				
				$data['updated_date'] = date('Y-m-d H:i:s');
				
				$sm = $this->getServiceLocator();
				$identity = $sm->get('AuthService')->getIdentity();
				$data['updated_by'] = $identity['user_id'];
				if($data['facebook'] != ''){
					$fbSubstr = substr($data['facebook'], 0, 7);
					if($fbSubstr != 'http://' && $fbSubstr != 'https:/'){
						$data['facebook'] = 'http://'.$data['facebook'];
					}
				}
				if($data['facebook'] != ''){
					$fbSubstr = substr($data['facebook'], 0, 7);
					if($fbSubstr != 'http://' && $fbSubstr != 'https:/'){
						$data['facebook'] = 'http://'.$data['facebook'];
					}
				}
				if($data['instagram'] != ''){
					$insSubstr = substr($data['instagram'], 0, 7);
					if($insSubstr != 'http://' && $insSubstr != 'https:/'){
						$data['instagram'] = 'http://'.$data['instagram'];
					}
				}
				if($data['twitter'] != ''){
					$twitterSubstr = substr($data['twitter'], 0, 7);
					if($twitterSubstr != 'http://' && $twitterSubstr != 'https:/'){
						$data['twitter'] = 'http://'.$data['twitter'];
					}
				}
				if($data['linkedin'] != ''){
					$linkedinSubstr = substr($data['linkedin'], 0, 7);
					if($linkedinSubstr != 'http://' && $linkedinSubstr != 'https:/'){
						$data['linkedin'] = 'http://'.$data['linkedin'];
					}
				}
				
				/* Which fields is it OK to save blanks (nulls) for? */
		    	$nullSaveOK = array(
		    			'mobile',
		    			'facebook',
		    			'instagram',
		    			'twitter',
		    			'linkedin',
		    			'address1',
		    			'state_id',
		    			'postcode',
		    			'country_id'
		    	);
		    	
				foreach($data as $key => $value){
					if(empty($value)){
						if(array_search($key, $nullSaveOK) !== FALSE){
							$data[$key] = null;
						} else {
							unset($data[$key]);
						}
					}
				}
				
				$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');
				echo $customersTable->saveCustomer($data);
			}
			
			exit;
    	}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	/**
	 * Delete customers from database
	 */
    
	public function deletecustomerAction(){
    	try{
    		$request = $this->getRequest();
    		if($request->isPost()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				
				$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');
				if($customersTable->deleteCustomer($data['customer_id'])){
					$opportunitiesTable = $this->getServiceLocator()->get('Opportunities\Model\OpportunitiesTable');
					$opportunitiesTable->deleteOpportunity(array('user_id' => $data['customer_id']));
					echo 1;
				}
			}
			
			exit;
    	}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	/**
	 * Crease and save partner
	 */
    
	public function savepartnerAction(){
    	try{
    		$request = $this->getRequest();
    		if($request->isPost()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				
				if(!empty($data['wedding_anniversary_date'])){
					list($d ,$m, $y) = explode('/', $data['wedding_anniversary_date']);				
					$data['wedding_anniversary_date'] = date('Y-m-d', strtotime("$y-$m-$d"));
				}else{
					$data['wedding_anniversary_date'] = NULL;
				}
				
				if(!empty($data['engagement_anniversary_date'])){
					list($d ,$m, $y) = explode('/', $data['engagement_anniversary_date']);				
					$data['engagement_anniversary_date'] = date('Y-m-d', strtotime("$y-$m-$d"));
				}else{
					$data['engagement_anniversary_date'] = NULL;
				}
				
				if(!empty($data['date_of_birth'])){
					list($d ,$m, $y) = explode('/', $data['date_of_birth']);				
					$data['date_of_birth'] = date('Y-m-d', strtotime("$y-$m-$d"));
				}else{
					$data['date_of_birth'] = NULL;
				}
				
				$data['updated_date'] = date('Y-m-d H:i:s');
				
				$sm = $this->getServiceLocator();
				$identity = $sm->get('AuthService')->getIdentity();
				$data['updated_by'] = $identity['user_id'];
				
				$customer_id = $data['customer_id'];
				unset($data['customer_id']);
				
				$data['partner_id'] = $customer_id;
				if($data['facebook'] != ''){
					$fbSubstr = substr($data['facebook'], 0, 7);
					if($fbSubstr != 'http://' && $fbSubstr != 'https:/'){
						$data['facebook'] = 'http://'.$data['facebook'];
					}
				}
				if($data['instagram'] != ''){
					$insSubstr = substr($data['instagram'], 0, 7);
					if($insSubstr != 'http://' && $insSubstr != 'https:/'){
						$data['instagram'] = 'http://'.$data['instagram'];
					}
				}
				if($data['twitter'] != ''){
					$twitterSubstr = substr($data['twitter'], 0, 7);
					if($twitterSubstr != 'http://' && $twitterSubstr != 'https:/'){
						$data['twitter'] = 'http://'.$data['twitter'];
					}
				}
				if($data['linkedin'] != ''){
					$linkedinSubstr = substr($data['linkedin'], 0, 7);
					if($linkedinSubstr != 'http://' && $linkedinSubstr != 'https:/'){
						$data['linkedin'] = 'http://'.$data['linkedin'];
					}
				}
				$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');
				if($customersTable->saveCustomer($data)){
					$partnerData = array('id' => $customer_id, 'partner_id' => $data['id']);
					$customersTable->saveCustomer($partnerData);
					echo 1;
				} else {
					echo "Failed to save partner";
				}
			}
			
			exit;
    	}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	/**
	 * Unassin partner
	 */
    
	public function unassignpartnerAction(){
    	try{
    		$request = $this->getRequest();
    		if($request->isPost()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				
				$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');
				//$customerData = array('id' => $data['customer_id'], 'partner_id' => null);
				echo $customersTable->unassignPartner($data['customer_id'], $data['partner_id']);
			}
			
			exit;
    	}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	/**
	 * Creates customer / store customer info into database
	 */
    
    public function createcustomerAction(){
    	try{
    		$request = $this->getRequest();
    		if($request->isPost()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				
				$sm = $this->getServiceLocator();
				$identity = $sm->get('AuthService')->getIdentity();
				
				foreach($data as $key => $value){
					if(empty($value))
						unset($data[$key]);
				}
				$mainCustomerData = array();
				if(isset($data['mainCustomerId'])){
					$data['partner_id'] = $data['mainCustomerId'];
				}
				unset($data['mainCustomerId']);
				unset($data['partner_name']);
				
				$data['created_by'] = $identity['user_id'];
				$data['created_date'] = date('Y-m-d H:i:s');
				
				$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');
				
				$latestCustomerId = $customersTable->saveCustomer($data);
				if(isset($data['partner_id'])){
					$mainCustomerData['id'] = $data['partner_id'];
					$mainCustomerData['partner_id'] = $latestCustomerId;
					$customersTable->saveCustomer($mainCustomerData);
					echo $data['partner_id'];
				} else {
					echo $latestCustomerId;
				}
			}
    		exit;
    	}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }

	public function checkduplicateAction(){
		try{
			$except_id = $this->params('except_id');
			$request = $this->getRequest();
    		if($request->isPost()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				$where = array();
				if($data['checkfor'] == 'email')
					//$where = array('email' => $data['value']);
					$where = "email = '".$data['value']."'";
				elseif($data['checkfor'] == 'mobile')
					//$where = array('mobile' => $data['value']);
					$where = "mobile = '".$data['value']."'";
					
				if(!empty($except_id))
					$where .= " AND id != '".$except_id."'";
				
				$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');
				echo $customersTable->checkDuplicate($where);
			}
			
			exit;
    	}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function checkviewexistAction(){
		try{
			$request = $this->getRequest();
    		if($request->isPost()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				$where = array();
				if($data['view_title'] != ''){
					if($data['selected_view'] != ''){
						$where = "customer_id = ".$data['user_id']." AND grid_type = '".$data['grid_type']."' AND view_title = '".$data['view_title']."' AND id != ".$data['selected_view']."";
					} else {
						$where = "customer_id = ".$data['user_id']." AND grid_type = '".$data['grid_type']."' AND view_title = '".$data['view_title']."'";
					}
				}
				
				$GridViewTable = $this->getServiceLocator()->get('Customer\Model\GridViewTable');
				echo $GridViewTable->chkViewExist($where);
			}
			
			exit;
    	}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
		
	}
	
	/**
	 * Uploads customer's profile photo
	 */
	
	public function upoadprofilephotoAction()
	{
		try{
			// Write your code here
			
			$sm = $this->getServiceLocator();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			if($request->isPost()){
				
				if(!empty($_FILES)){
					
					$customer_id = $_POST['id'];
				
					$fileParts = pathinfo($_FILES['profile_photo_file']['name']);
				
					$tempFile = $_FILES['profile_photo_file']['tmp_name'];
					$targetPath = $config['documentRoot'] . 'profile_photo';
					$targetFileName = time().rand(99999, 99999999).'_'.$customer_id.'.'.$fileParts['extension'];
					$targetFile = rtrim($targetPath,'/') . '/' . $targetFileName;
					
					// Validate the file type
					$fileTypes = array('gif', 'png', 'jpeg', 'jpg'); // File extensions
					
					if (in_array($fileParts['extension'],$fileTypes)) {
						if(move_uploaded_file($tempFile, $targetFile)){
							$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');
							$customerData = $customersTable->fetchCustomerdetails($customer_id);
							if(!empty($customerData['profile_photo'])){
								$imagePath = $config['documentRoot'].'profile_photo/'.$customerData['profile_photo'];
								if(file_exists($imagePath))
									unlink($imagePath);
							}
							
							$data = array('id' => $customer_id, 'profile_photo' => $targetFileName);
							$customersTable->saveCustomer($data);
							
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
}
