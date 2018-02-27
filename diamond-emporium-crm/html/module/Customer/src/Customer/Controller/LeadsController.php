<?php
/**
 * Controller for leads
 */

namespace Customer\Controller;

use Customer\Form\LeadForm;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LeadsController extends AbstractActionController
{
    public function indexAction()
    {
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost();
				
				$leadsTable = $this->getServiceLocator()->get('Customer\Model\LeadsTable');
				$data = $posts->toArray();
				if(isset($data['lead_id']) && !empty($data['lead_id'])){
					$data['updated_by'] = $identity['user_id'];
					$data['updated_date'] = date('Y-m-d H:i:s');
					
					/*
					 * We're updating an existing lead, get the old record and
					 * see if the owner has changed. If so, create an alert to
					 * notify the new owner.
					 * Don't do this for the unassigned user (zero) or if the
					 * new owner is the current user.
					 */ 
					$oldData = $leadsTable->fetchLeadDetails($data['lead_id']);
					if ($oldData['lead_owner'] != $data['lead_owner'] &&
						$data['lead_owner'] != 0 &&
						$data['lead_owner'] != $identity['user_id']) {
						$alertTable = $this->getServiceLocator()->get('Alert\Model\AlertTable');

						$lead_description = $data['lead_id'] . ' (' . $data['first_name'] . ' ' . $data['last_name'] . ')'; 
						$message = sprintf(
								'%s assigned lead <a href="%s">%s</a> to you.',
								$identity['first_name'],
								'/leaddetails/' . $data['lead_id'],
								$lead_description
								);
						
						$alertTable->createAlert($identity['user_id'], $data['lead_owner'],	$message);
					}
					
				}else{
					$data['created_by'] = $identity['user_id'];
					$data['created_date'] = date('Y-m-d H:i:s');	
					$data['lead_status'] = 'Open';				
				}
				
				foreach($data as $key => $value){
					if(empty($value)){
					    if ($key == 'lead_owner') {
						     $data[$key] = 0;
					    }
					    if($key != 'lead_owner' && $key != 'mobile') {
							unset($data[$key]);
						}
					}
				}
				
				unset($data['gridpagerlistjqxNotes']);
				unset($data['referred_by_name']);
				unset($data['follow_up_date']);
				unset($data['note_type']);
				unset($data['note_description']);
				unset($data['noteUpdateId']);
				
				unset($data['mobile_check']);
				unset($data['email_check']);
				
				echo $leadsTable->saveLead($data);
				
				exit;
			}
			$leadForm = $this->getServiceLocator()->get('Customer\Form\LeadForm');
			$config = $this->getServiceLocator()->get('Config');
			$leadsTable = $this->getServiceLocator()->get('Customer\Model\LeadsTable');
			$columnList = $leadsTable->listColumns('leads');
			$gridViewTable = $this->getServiceLocator()->get('Customer\Model\GridViewTable');
			$gridViewOptions = $gridViewTable->getGridViews($identity['user_id'], 'leads');
			
			$statusOptions  = $leadsTable->fetchLeadTypesOptions();
			$statusOptions = array_merge(array('' => 'Select'), $statusOptions);
			
			$userTable = $sm->get('Customer\Model\UsersTable');
			$ownerOptions = $userTable->fetchSelectOptions();
			
			return array('form' => $leadForm, 'recordsPerPage' => $config['recordsPerPage'], 'identity' => $identity,
						 'columnList' => $columnList, 'gridViewOptions' => $gridViewOptions, 'loginUserId' => $identity['user_id'],
						 'ownerOptions' => $ownerOptions, 'statusOptions' => $statusOptions);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	public function webleadsAction()
    {
		try{
			// get Post Data from Web to lead form
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost();				
				
				$leadsTable = $this->getServiceLocator()->get('Customer\Model\LeadsTable');
				$data = $posts->toArray();
				if(isset($data['lead_id']) && !empty($data['lead_id'])){
					$data['updated_by'] = $identity['user_id'];
					$data['updated_date'] = date('Y-m-d H:i:s');
				}else{
					$data['created_by'] = 1;
					$data['created_date'] = date('Y-m-d H:i:s');					
				}
				
				foreach($data as $key => $value){
					if(empty($value)){
					     if ($key == 'lead_owner')
						     $data['lead_owner'] = 0;
						if($key != 'lead_owner'){
							unset($data[$key]);
						}
					}
				}
				unset($data['gridpagerlistjqxNotes']);
				unset($data['referred_by_name']);
				unset($data['follow_up_date']);
				unset($data['note_type']);
				unset($data['note_description']);
				unset($data['noteUpdateId']);
				
				unset($data['mobile_check']);
				unset($data['email_check']);				
				$errorMessges  = array();
				if($data['first_name'] == "") {				
				$errorMessges[] = "Please enter First Name";				
				}  if($data['last_name'] == "") {				
				$errorMessges[] = "Please enter Last Name";					
				} if($data['mobile'] == "") {	
				 $errorMessges[] = "Please enter Mobile";		
				} if($data['email'] == "") {	
				 $errorMessges[] = "Please enter Email";		
				} if($data['product'] == "") {	
				 $errorMessges[] = "Please select Product";		
				} if($data['state'] == "") {	
				 $errorMessges[] = "Please select State";		
				}
				
				if(sizeof($errorMessges)>0) {
				  $this->flashMessenger()->addMessage($errorMessges);
				  } else {
				  
				   $leadsTable->saveLead($data);
				  }
				  
				  return $this->redirect()->toUrl('/webtoleadsuccess');
				/* else {
				
					
				}	*/
				
				//print_r($errorMessges); exit;
				
			  
			  
			  
			
			}
			
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	/**
	 * Creates after login landing page
	 */	
	public function webtoleadsuccessAction()
    {
		try{
			// Write your code here
		
			return array('flashMessages' => $this->flashMessenger()->getMessages());
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
    
    public function ajaxgetleadsAction(){
    	try{
			// Write your code here
			
    		$config = $this->getServiceLocator()->get('Config');
    		
    		//$router = $this->getEvent()->getRouteMatch();
			$keyword = '';
    		$params = $this->getRequest()->getQuery()->toArray();
			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
			$keyword = $params['keyword'];
			$sortdatafield = $params['sortdatafield'];
			$sortorder = $params['sortorder'];
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			$leadsTable = $this->getServiceLocator()->get('Customer\Model\LeadsTable');
			if($keyword != ''){
				$offset = 0;
			}
			$leadsArr = $leadsTable->fetchAll($limit, $offset, $keyword, $sortdatafield, $sortorder, $params);
			
			foreach($leadsArr['Rows'] as $key => $value){
				foreach($value as $field => $fieldValue){
					if($field == 'created_date')
						$leadsArr['Rows'][$key][$field] = date($config['phpDateFormat'], strtotime($leadsArr['Rows'][$key][$field]));
				}
			}
			
			echo json_encode($leadsArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
    
	public function ajaxcustomerslookupAction(){
    	try{
			// Write your code here
			
    		$params = $this->getRequest()->getQuery()->toArray();
			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			
			$keyword = $params['keyword'];
			
			$sortdatafield = $params['sortdatafield'];
			$sortorder = $params['sortorder'];
			
			$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');
			$customersArr = $customersTable->customerLookup($limit, $offset, $keyword, $sortdatafield, $sortorder);
			echo json_encode($customersArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
	
	public function ajaxoppcustomerslookupAction(){
    	try{
			// Write your code here
			
    		$params = $this->getRequest()->getQuery()->toArray();
			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			
			$keyword = $params['keyword'];
			
			$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');
			$customersArr = $customersTable->customerLookup($limit, $offset, $keyword);
			echo json_encode($customersArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
    
	public function ajaxcheckmobileAction(){
    	try{
			// Write your code here
			
    		$mobile = $this->getRequest()->getPost('mobile');
			
			$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');
			echo $customersTable->checkValueExists('mobile', $mobile);
			
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
    
	public function ajaxcheckemailAction(){
    	try{
			// Write your code here
			
    		$params = $this->getRequest()->getPosts();
			$email = $params['email'];
			
			$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');
			echo $customersTable->checkValueExists('email', $email);
			
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
    
    public function ajaxcustomerfromleadAction(){
    	try {
    		$leadId = $this->getRequest()->getPost('lead_id');

    		$leadsTable = $this->getServiceLocator()->get('Customer\Model\LeadsTable');
    		$lead = (array)$leadsTable->fetchLeadDetails($leadId);
    		if (is_array($lead) && $lead['lead_id']) {
				$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');
	    		$customer = $customersTable->fetchCustomerdetailsByEmail($lead['email']);
	    		
	    		if (!is_array($customer) || !$customer['id']) {
	    			/* Customer wasn't found - create it */
	    			$cols = array('title', 'first_name', 'last_name', 'email', 'mobile', 'postcode');
	    			$to_save = array_intersect_key($lead, array_flip($cols));
	    			/* TODO: state column name is different between lead & customer */
	    			$to_save['state_id'] = $lead['state'];
	    			/* TODO: Don't hardcode this */
	    			$to_save['country_id'] = 'Australia';
	    			
	    			$to_save['created_date'] = date('Y-m-d H:i:s');
	    			$sm = $this->getServiceLocator();
	    			$identity = $sm->get('AuthService')->getIdentity();
	    			$to_save['created_by'] = $identity['user_id'];
	    			
	    			$customerId = $customersTable->saveCustomer($to_save);
	    			$customer = $customersTable->fetchCustomerdetails($customerId);
	    		}
	    		
	    		/* Only return partial record */
	    		echo json_encode(array_intersect_key($customer, array_flip(array('id', 'first_name', 'last_name'))));
    		}
    		exit;
	    }catch(Exception $e){
	    	\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
	    }
    }
    
    public function leaddetailsAction(){
	    try{	    	
	    	$id = $this->params('id');
			$leadsTable = $this->getServiceLocator()->get('Customer\Model\LeadsTable');
			$leadData = (array)$leadsTable->fetchLeadDetails($id);
			$leadData['referred_by_name'] = $leadData['cust_first_name'] . ' ' . $leadData['cust_last_name'];
			
			$leadForm = $this->getServiceLocator()->get('Customer\Form\LeadForm');
			$leadForm->setData($leadData);
			
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $this->getServiceLocator()->get('Config');
			
			$newCustomerForm = $this->getServiceLocator()->get('Customer\Form\NewCustomerForm');
			$newCustomerForm->get('save')->setAttribute('onclick', 'createNewCustomer($(\'#frm_new_customer\'), \'jqxCustomersLookup\');');
			
			$userTable = $sm->get('Customer\Model\UsersTable');
			$ownerOptions = $userTable->fetchUsersForTasks();
			
			return array('form' => $leadForm, 'recordsPerPage' => $config['recordsPerPage'],
						 'identity' => $identity, 'leadData' => $leadData,
						 'usersList' => $usersList,
						 'newCustomerForm' => $newCustomerForm, 'ownerOptions' => $ownerOptions);
			
	    }catch(Exception $e){
	    	\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
	    }
    }
        
	public function convertleadformAction(){
		try{
			$sm = $this->getServiceLocator();
			 	
	    	$request = $this->getRequest();
	    	$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');
	    	$leadsTable = $this->getServiceLocator()->get('Customer\Model\LeadsTable');
	    	
	    	$viewModel = new ViewModel();
	    	
	    	$config = $this->getServiceLocator()->get('Config');
	    	
    		if($request->isPost()){
				$posts = $request->getPost()->toArray();
				
				$leadData = $leadsTable->fetchLeadDetails($posts['lead_id']);
				
				$keywords = array('mobile' => $leadData['mobile'], 'email' => $leadData['email']);
				
				$convertForm = $this->getServiceLocator()->get('LeadToCustomerForm');
				$convertForm->get('lead_id')->setValue($posts['lead_id']);
				$convertForm->get('lead_owner')->setValue($leadData['lead_owner']);
				
				$customerData = $customersTable->fetchMatchedCustomer($keywords);
				
				if($customerData)
					$convertForm->get('matched_customer_id')->setValue($customerData['id']);					
					
				$usersTable = $this->getServiceLocator()->get('Customer\Model\UsersTable');
				$usersList = $usersTable->fetchUsersForTasks();
				$tasksCategoryTable = $this->getServiceLocator()->get('Task\Model\TasksCategoryTable');
				$CategoryList = $tasksCategoryTable->fetchAll();
				$tasksSubjectTable = $this->getServiceLocator()->get('Task\Model\TasksSubjectTable');
				$subjectList = $tasksSubjectTable->fetchAll();
				$tasksPriorityTable = $this->getServiceLocator()->get('Task\Model\TasksPriorityTable');
				$priorityList = $tasksPriorityTable->fetchAll();
				
				$userTable = $sm->get('Customer\Model\UsersTable');
				$ownerOptions = $userTable->fetchUsersForTasks();
				
				$viewModel->setVariables(array('leadData' => $leadData, 'matchedCustomer' => $customerData, 'convertForm' => $convertForm,
											   'usersList' => $usersList, 'CategoryList' => $CategoryList, 'subjectList' => $subjectList,
											   'priorityList' => $priorityList, 'ownerOptions' => $ownerOptions));
			}			
			
			$viewModel->setVariable('recordsPerPage', $config['recordsPerPage']);
			$viewModel->setTerminal(true);	
			return $viewModel;	
	    }catch(Exception $e){
	    	\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
	    }
    }
    
	public function newleadformAction(){
		try{
    		$params = $this->getRequest()->getQuery()->toArray();
    		$customerId = $params['customerId'];

    		$sm = $this->getServiceLocator();

    		$form = $sm->get('Customer\Form\LeadForm');
    		$customersTable = $sm->get('Customer\Model\CustomersTable');
    		$customerData = $customersTable->fetchCustomerdetails($customerId);
    		$form->setData($customerData);

    		$usersTable = $sm->get('Customer\Model\UsersTable');
    		$ownerOptions = $usersTable->fetchSelectOptions();
			
	    	$viewModel = new ViewModel();
	    	$config = $this->getServiceLocator()->get('Config');
			$viewModel->setVariables(array(
					'recordsPerPage' => $config['recordsPerPage'],
					'form' => $form,
					'ownerOptions' => $ownerOptions,
					));
			$viewModel->setTerminal(true);	
			return $viewModel;	
	    }catch(Exception $e){
	    	\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
	    }
    }
    
    public function convertleadAction(){
		try{	    	
	    	$request = $this->getRequest();
	    	$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');
	    	$leadsTable = $this->getServiceLocator()->get('Customer\Model\LeadsTable');
	    	
	    	$config = $this->getServiceLocator()->get('Config');
	    	
    		if($request->isPost()){
				$posts = $request->getPost()->toArray();
				
				$sm = $this->getServiceLocator();
				$identity = $sm->get('AuthService')->getIdentity();
				
				$leadData = $leadsTable->fetchLeadDetails($posts['lead_id']);
				
				$user_id = empty($posts['customer_id']) ? $posts['matched_customer_id'] : $posts['customer_id'];
					
				if(empty($user_id)){
					
					$count = $customersTable->checkDuplicate("email = '".$leadData['email']."' OR mobile = '".$leadData['mobile']."'");
					
					if($count == 0){
						$customerData = array(
							'title' => $leadData['title'],
							'first_name' => $leadData['first_name'],
							'last_name' => $leadData['last_name'],
							'email' => $leadData['email'],
							'mobile' => $leadData['mobile'],
							'state_id' => $leadData['state'],
							'created_date' => date('Y-m-d H:i:s'),
							'created_by' => $identity['user_id']
						);
						$user_id = $customersTable->saveCustomer($customerData);
					}else{
						echo 'Customer already exists';
						exit;
					}
				}				
				
				$opportunityData = array(
					'user_id' => $user_id,
					'lead_id' => $posts['lead_id'],
					'opportunity_name' => $posts['opportunity_name'],
					'lead_source' => $leadData['lead_source'],
					'referred_by_customer' => $leadData['referred_by_customer'],
					'product' => $leadData['product'],
					'reference_product' => $leadData['reference_product'],
					'looking_for' => $leadData['looking_for'],
					'preferred_contact' => $leadData['preferred_contact'],
					'budget' => $leadData['budget'],
					'special_instructions' => $posts['special_instructions'],
					'record_owner_id' => $posts['lead_owner'],
					'rating' => '',
					'opportunity_status' => 'Open',
					'created_date' => date('Y-m-d H:i:s'),
					'created_by' => $identity['user_id']
				);
				
				$opportunitiesTable = $this->getServiceLocator()->get('Opportunities\Model\OpportunitiesTable');
				$opportunityId = $opportunitiesTable->saveOpportunities($opportunityData);	
				
				if($opportunityId){
					$task_id = 0;
					$comment_id = 0;
					if(!empty($posts['convert_task_title'])){
						/** Creating task for newly reated Customer **/
									
						$due_date = empty($posts['convert_due_date']) ? null : \De\Lib::dbDateFormat($posts['convert_due_date']);
						$taskData = array('task_title' => $posts['convert_task_title'],
									  'task_category' => $posts['convert_task_category'], 'task_subject' => $posts['convert_task_subject'],
									  'task_priority' => $posts['convert_task_priority'], 'assigned_to' => $posts['convert_assigned_to'],
									  'due_date' => $due_date, 'created_by' => $identity['user_id'], 'created_date' => date('Y-m-d H:i:s'),
									  'opportunity_id' => $opportunityId,
						);
									  
						$tasksTable = $this->getServiceLocator()->get('Task\Model\TasksTable');			
						$task_id =  $tasksTable->saveTask($taskData);
						if(!empty($task_id) && !empty($posts['convert_task_comment'])){
							$taskHistoryTable = $this->getServiceLocator()->get('Task\Model\TasksHistoryTable');
					
							$data = array('metadata' => 'comment', 'data' => $posts['convert_task_comment'], 'task_id' => $task_id,
										  'created_by' => $identity['user_id'], 'created_date' => date('Y-m-d H:i:s'));
							$comment_id = $taskHistoryTable->saveTaskHistory($data);
						}
						
						/** Creating task for newly reated Customer **/
					}
					
					$saveLeadData['lead_status'] = 'To Opportunity';
					$saveLeadData['lead_id'] = $posts['lead_id'];
					$leadsTable->saveLead($saveLeadData, $posts['lead_id']);
					
					/*
					 * Create alert if this is an assigned user and not the current user.
					 */
					
					if ($opportunityData['record_owner_id'] != 0 &&
						$opportunityData['record_owner_id'] != $identity['user_id']) {
						$alertTable = $this->getServiceLocator()->get('Alert\Model\AlertTable');
							
						$opportunity_description = $opportunityId . ' (' . $opportunityData['opportunity_name'] . ')';
						$message = sprintf(
								'%s assigned opportunity <a href="%s">%s</a> to you.',
								$identity['first_name'],
								'/opportunitydetails/' . $opportunityId,
								$opportunity_description
								);
							
						$alertTable->createAlert($identity['user_id'], $opportunityData['record_owner_id'], $message);
					}
				}
				
				$response = array('customer_id' => $user_id, 'task_id' => $task_id, 'comment_id' => $comment_id);
				echo json_encode($response);
			}		
			
			exit;	
	    }catch(Exception $e){
	    	\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
	    }
    }
    
	public function leadopportunitylookupAction(){
    	try{
			// Write your code here
			
    		$params = $this->getRequest()->getQuery()->toArray();
			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			
			$keyword = $params['keyword'];
			
			$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');
			$customersArr = $customersTable->customerLookup($limit, $offset, $keyword);
			echo json_encode($customersArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
	
	public function matchedcustomerAction(){
		try{
			// Write your code here
			$request = $this->getRequest();
	    	if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');				
				$keywords = array($posts['field'] => $posts['value']);
				$customerData = $customersTable->fetchMatchedCustomer($keywords);
				
				echo json_encode($customerData);
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function sendmailtoleadownerAction(){
		try{	    	
	    	$request = $this->getRequest();
	    	
    		if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$usersTable = $this->getServiceLocator()->get('Customer\Model\UsersTable');
				$leadsTable = $this->getServiceLocator()->get('Customer\Model\LeadsTable');
				$ownerData = $usersTable->fetchUsersDetails($posts['owner_id']);
				$leadData = $leadsTable->getLeadDetails($posts['lead_id']);
				$emailParams['toEmail'] = $ownerData['email'];
				$emailParams['toName'] = $ownerData['fullname'];
				$emailParams['subject'] = 'Convert Lead';
				$emailParams['message'] = $leadData;
				$emailParams['template'] = 'convert-lead-emailto-owner.phtml';
				$mailConfig = $this->getServiceLocator()->get('Config');
				\De\Service\EmailService::sendEmail($mailConfig['smtp_details'], $emailParams);
				exit;
			}
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function updateleadstatusAction(){
		try{
			$request = $this->getRequest();
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			if($request->isPost()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				$leadsTable = $this->getServiceLocator()->get('Customer\Model\LeadsTable');
				$finalData['lead_id'] = $data['lead_statusId'];
				$finalData['lead_status'] = $data['lead_status'];
				$finalData['lead_reason'] = $data['lead_reason'];
				if($data['lead_close_date'] != ''){
					$dateOldFormat = explode("/", $data['lead_close_date']);
					$dateNewFormat = $dateOldFormat[2].'-'.$dateOldFormat[1].'-'.$dateOldFormat[0].' 00:00:00';
					$finalData['lead_close_date'] = $dateNewFormat;
				} else {
					$finalData['lead_close_date'] = null;
				}
				$finalData['updated_by'] = $identity['user_id'];
				$finalData['updated_date'] = date('Y-m-d H:i:s');
				$LeadsData = $leadsTable->saveLead($finalData, $data['lead_statusId']);
				echo $LeadsData; exit;
			}
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}
