<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Customer\Controller;

use Zend\Db\Sql\Ddl\Column\Decimal;

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
				}else{
					$data['created_by'] = $identity['user_id'];
					$data['created_date'] = date('Y-m-d H:i:s');					
				}
				
				foreach($data as $key => $value){
					if(empty($value)){
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
				
				echo $leadsTable->saveLead($data);
				
				exit;
			}
			$leadForm = $this->getServiceLocator()->get('Customer\Form\LeadForm');
			$config = $this->getServiceLocator()->get('Config');
			$leadsTable = $this->getServiceLocator()->get('Customer\Model\LeadsTable');
			$columnList = $leadsTable->listColumns('leads');
			$gridViewTable = $this->getServiceLocator()->get('Customer\Model\GridViewTable');
			$gridViewOptions = $gridViewTable->getGridViews($identity['user_id'], 'leads');
			
			return array('form' => $leadForm, 'recordsPerPage' => $config['recordsPerPage'], 'identity' => $identity, 'columnList' => $columnList, 'gridViewOptions' => $gridViewOptions, 'loginUserId' => $identity['user_id']);
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
			$leadsArr = $leadsTable->fetchAll($limit, $offset, $keyword, $sortdatafield, $sortorder);
			
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
			
			$tasksTable = $sm->get('Task\Model\TasksTable');
			$tasks = $tasksTable->fetchAll($id, 'lead', 1, 100);			
			$closedTasks = $tasksTable->fetchAll($id, 'lead', 2, 100);
			
			$usersTable = $this->getServiceLocator()->get('Customer\Model\UsersTable');
			$usersList = $usersTable->fetchUsersForTasks();
			$tasksCategoryTable = $this->getServiceLocator()->get('Task\Model\TasksCategoryTable');
			$CategoryList = $tasksCategoryTable->fetchAll();
			$tasksSubjectTable = $this->getServiceLocator()->get('Task\Model\TasksSubjectTable');
			$subjectList = $tasksSubjectTable->fetchAll();
			$tasksPriorityTable = $this->getServiceLocator()->get('Task\Model\TasksPriorityTable');
			$priorityList = $tasksPriorityTable->fetchAll();
			
			$newCustomerForm = $this->getServiceLocator()->get('Customer\Form\NewCustomerForm');
			$newCustomerForm->get('save')->setAttribute('onclick', 'createNewCustomer($(\'#frm_new_customer\'), \'jqxCustomersLookup\');');
			
			foreach($tasks as $key => $value){
				$tasks[$key]['is_overdue'] = \De\Lib::isTaskOverDue($value);
			}
			
			return array('form' => $leadForm, 'recordsPerPage' => $config['recordsPerPage'],
						 'identity' => $identity, 'leadData' => $leadData,
						 'tasks' => $tasks, 'closedTasks' => $closedTasks, 'config' => $config,
						 'usersList' => $usersList, 'CategoryList' => $CategoryList, 'subjectList' => $subjectList,
						 'priorityList' => $priorityList, 'newCustomerForm' => $newCustomerForm);
			
	    }catch(Exception $e){
	    	\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
	    }
    }
    
    public function deleteleadAction(){
		try{	    	
	    	$request = $this->getRequest();
    		if($request->isPost()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				$lead_id = $data['lead_id'];
				
				$leadsTable = $this->getServiceLocator()->get('Customer\Model\LeadsTable');		
				echo $leadsTable->deleteLead($lead_id);
			}
			
			exit;
	    }catch(Exception $e){
	    	\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
	    }
    }
    
	public function convertleadformAction(){
		try{	    	
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
				
				$viewModel->setVariables(array('leadData' => $leadData, 'matchedCustomer' => $customerData, 'convertForm' => $convertForm,
											   'usersList' => $usersList, 'CategoryList' => $CategoryList, 'subjectList' => $subjectList, 'priorityList' => $priorityList));
			}			
			
			$viewModel->setVariable('recordsPerPage', $config['recordsPerPage']);
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
					//'opportunity_type' => null,
					'opportunity_name' => $posts['opportunity_name'],
					'lead_source' => $leadData['lead_source'],
					'referred_by_customer' => $leadData['referred_by_customer'],
					'product' => $leadData['product'],
					'reference_product' => $leadData['reference_product'],
					'looking_for' => $leadData['looking_for'],
					'preferred_contact' => $leadData['preferred_contact'],
					'budget' => $leadData['budget'],
					'progress_of_opportunity' => $posts['progress_of_opportunity'],
					'record_owner_id' => $posts['lead_owner'],
					'urgency' => '',
					'rating' => '',
					'opportunity_status' => 'Pending',
					'probability' => 0,
					'created_date' => date('Y-m-d H:i:s'),
					'created_by' => $identity['user_id']
				);
				
				$opportunitiesTable = $this->getServiceLocator()->get('Opportunities\Model\OpportunitiesTable');
				$opportunityId = $opportunitiesTable->saveOpportunities($opportunityData);	
				
				$tasksTable = $this->getServiceLocator()->get('Task\Model\TasksTable');			
				
				if($opportunityId){
				
					$tasksTable->convertTasks($posts['lead_id'], $user_id);
					
					$task_id = 0;
					$comment_id = 0;
					if(!empty($posts['convert_task_title'])){
						/** Creating task for newly reated Customer **/
									
						$due_date = empty($posts['convert_due_date']) ? null : \De\Lib::dbDateFormat($posts['convert_due_date']);
						$taskData = array('task_title' => $posts['convert_task_title'], 'customer_id' => $user_id,
									  'task_category' => $posts['convert_task_category'], 'task_subject' => $posts['convert_task_subject'],
									  'task_priority' => $posts['convert_task_priority'], 'assigned_to' => $posts['convert_assigned_to'],
									  'due_date' => $due_date, 'created_by' => $identity['user_id'], 'created_date' => date('Y-m-d H:i:s'));
									  
						$task_id =  $tasksTable->saveTask($taskData);
						if(!empty($task_id) && !empty($posts['convert_task_comment'])){
							$taskHistoryTable = $this->getServiceLocator()->get('Task\Model\TasksHistoryTable');
					
							$data = array('metadata' => 'comment', 'data' => $posts['convert_task_comment'], 'task_id' => $task_id,
										  'created_by' => $identity['user_id'], 'created_date' => date('Y-m-d H:i:s'));
							$comment_id = $taskHistoryTable->saveTaskHistory($data);
						}
						
						/** Creating task for newly reated Customer **/
					}
					
					$notesTable = $this->getServiceLocator()->get('Opportunities\Model\NotesTable');
					
					$noteData = array('grid_type' => 'opportunity', 'grid_type_id' => $opportunityId);
					$where = array('grid_type_id' => $posts['lead_id']);
					$notesTable->convertNotes($noteData, $where);
					
					$leadsTable->deleteLead($posts['lead_id']);
					
					if(isset($posts['email_notification']) && $posts['email_notification'] == 1){
						
						$usersTable = $this->getServiceLocator()->get('Customer\Model\UsersTable');
						$ownerData = $usersTable->fetchUsersDetails($posts['lead_owner']);
						
						$emailParams['toEmail'] = $ownerData['email'];
						$emailParams['toName'] = $ownerData['fullname'];
						$emailParams['subject'] = 'Convert Lead';
						$emailParams['message'] = $leadData;
						$emailParams['template'] = 'convert-lead-emailto-owner.phtml';
						
						\De\Service\EmailService::sendEmail($config['smtp_details'], $emailParams);
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
}
