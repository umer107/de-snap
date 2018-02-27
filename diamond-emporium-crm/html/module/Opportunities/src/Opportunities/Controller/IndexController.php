<?php
/**
 * Opportunity Controller
 */

namespace Opportunities\Controller;

use Opportunities\Form\OpportunitiesForm;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();				
			$opportunitiesTable = $this->getServiceLocator()->get('Opportunities\Model\OpportunitiesTable');
				
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				
				$finalData = array();
				
				/*$customersTable = $this->getServiceLocator()->get('Customer\Model\CustomersTable');				
				$customeDetails = $customersTable->fetchCustomerdetails($posts['user_id']);
				$finalData['opportunity_name'] = $customeDetails['fullname'];*/
				
				$leadsTable = $this->getServiceLocator()->get('Opportunities\Model\OpportunitiesTable');
				
				$finalData['opportunity_name'] = $data['opportunity_name'];
				$finalData['opportunity_type'] = $data['opportunity_type'];
				$finalData['lead_source'] = $data['lead_source'];
				$finalData['referred_by_customer'] = $data['referred_by_customer'];
				$finalData['product'] = $data['product'];
				$finalData['reference_product'] = $data['reference_product'];
				$finalData['looking_for'] = $data['looking_for'];
				$finalData['preferred_contact'] = $data['preferred_contact'];
				$finalData['budget'] = $data['budget'];
				$finalData['rating'] = $data['rating'];
				if($data['est_close_date'] != ''){
					$dateOldFormat = explode("/", $data['est_close_date']);
					$dateNewFormat = $dateOldFormat[2].'-'.$dateOldFormat[1].'-'.$dateOldFormat[0].' 00:00:00';
					$finalData['est_close_date'] = $dateNewFormat;
				}
				
				foreach($finalData as $key => $value){
					if(empty($value))
						unset($finalData[$key]);
				}
				
				if(empty($data['opportunityId'])){
					$finalData['user_id'] = $data['user_id'];
					$finalData['created_by'] = $identity['user_id'];
					$finalData['opportunity_status'] = 'Open'; //Pending
					echo $opportunitiesTable->saveOpportunities($finalData);
				} else {
					$finalData['record_owner_id'] = $data['record_owner_id'];
					$finalData['special_instructions'] = $data['special_instructions'];
					$finalData['updated_by'] = $identity['user_id'];
					$finalData['updated_date'] = date('Y-m-d H:i:s');

					/*
					 * We're updating an existing record, get the old one and
					 * see if the owner has changed. If so, create an alert to
					 * notify the new owner.
					 * Don't do this for the unassigned user (zero) or if the
					 * new owner is the current user.
					 */
					$oldData = $opportunitiesTable->fetchOpportunityDetails($data['opportunityId']);
					if ($oldData['record_owner_id'] != $finalData['record_owner_id'] &&
						$finalData['record_owner_id'] != 0 &&
						$finalData['record_owner_id'] != $identity['user_id']) {
						$alertTable = $this->getServiceLocator()->get('Alert\Model\AlertTable');
					
						$opportunity_description = $data['opportunityId'] . ' (' . $finalData['opportunity_name'] . ')';
						$message = sprintf(
								'%s assigned opportunity <a href="%s">%s</a> to you.',
								$identity['first_name'],
								'/opportunitydetails/' . $data['opportunityId'],
								$opportunity_description
								);
							
						$alertTable->createAlert($identity['user_id'], $finalData['record_owner_id'], $message);
					}
					
					echo $opportunitiesTable->saveOpportunities($finalData, $data['opportunityId']);
				}
				exit;
			}
			
			$opportunitiesForm = $this->getServiceLocator()->get('Opportunities\Form\OpportunitiesForm');
			$config = $this->getServiceLocator()->get('Config');
			$leadsTable = $this->getServiceLocator()->get('Customer\Model\LeadsTable');
			$columnList = $leadsTable->listColumns('opportunities');
			$gridViewTable = $this->getServiceLocator()->get('Customer\Model\GridViewTable');
			$gridViewOptions = $gridViewTable->getGridViews($identity['user_id'], 'opportunities');

			$statusOptions  = $opportunitiesTable->fetchOpportunityTypesOptions();
			$statusOptions = array_merge(array('' => 'Select'), $statusOptions);
			
			$userTable = $sm->get('Customer\Model\UsersTable');
			$ownerOptions = $userTable->fetchSelectOptions();
				
			return array('form' => $opportunitiesForm, 'recordsPerPage' => $config['recordsPerPage'],
						 'columnList' => $columnList, 'gridViewOptions' => $gridViewOptions,
						 'loginUserId' => $identity['user_id'], 'config' => $config, 'tasks' => $tasks,
						 'statusOptions' => $statusOptions, 'ownerOptions' => $ownerOptions);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	public function ajaxgetopportunitiesAction(){
    	try{
			$config = $this->getServiceLocator()->get('Config');
			$keyword = '';
			$oppCustomerId = '';
    		$params = $this->getRequest()->getQuery()->toArray();
			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
			$keyword = $params['keyword'];
			$oppCustomerId = $params['oppCustomerId'];
			
			$sortdatafield = $params['sortdatafield'];
			$sortorder = $params['sortorder'];
			
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			$opportunitiesTable = $this->getServiceLocator()->get('Opportunities\Model\OpportunitiesTable');
			if($keyword != ''){
				$offset = 0;
			}
			$opportunitiesArr = $opportunitiesTable->fetchAll($limit, $offset, $keyword, $oppCustomerId, $sortdatafield, $sortorder, $params);
			foreach($opportunitiesArr['Rows'] as $key => $value){
				foreach($value as $field => $fieldValue){
					if($field == 'created_date'){
						$opportunitiesArr['Rows'][$key][$field] = date($config['phpDateFormat'], strtotime($opportunitiesArr['Rows'][$key]['created_date']));
					} else if($field == 'est_close_date'){
						if($opportunitiesArr['Rows'][$key]['est_close_date'] != null){
							$opportunitiesArr['Rows'][$key][$field] = date($config['phpDateFormat'], strtotime($opportunitiesArr['Rows'][$key]['est_close_date']));
						}
					}
				}
			}
			echo json_encode($opportunitiesArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
	
	public function opportunitydetailsAction(){
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$authService = $sm->get('AuthService');
			$identity = $authService->getIdentity();
			/* TODO: not sure this is the correct way to check access */
			$canDelete = $sm->get('ControllerPluginManager')->get('AuthPlugin')->checkResource($authService, 'Opportunities\Controller\Index::deleteopportunity');
			
			$config = $this->getServiceLocator()->get('Config');
			
    		//$router = $this->getEvent()->getRouteMatch();
			$id = $this->params('id');
			$OpportunitiesTable = $this->getServiceLocator()->get('Opportunities\Model\OpportunitiesTable');
			$OpportunitiesData = (array)$OpportunitiesTable->fetchOpportunityDetails($id);
			if (count($OpportunitiesData) == 0) {
				return $this->notFoundAction();				
			}
			
			$OpportunitiesData[0]['edit_est_close_date'] = empty($OpportunitiesData[0]['est_close_date']) ? '' : date($config['formDateFormat'], strtotime($OpportunitiesData[0]['est_close_date']));
			$OpportunitiesData[0]['est_close_date'] = empty($OpportunitiesData[0]['est_close_date']) ? '' : date($config['phpDateFormat'], strtotime($OpportunitiesData[0]['est_close_date']));						
			
			$ProductsTable = $this->getServiceLocator()->get('Customer\Model\ProductsTable');
			$objUsersTable = $this->getServiceLocator()->get('Customer\Model\UsersTable');
			$listProducts = $ProductsTable->fetchSelectOptions();
			
			$partnerData = array();
			if(count($OpportunitiesData) > 0){
				$partnerData = $OpportunitiesTable->getPartnerDetails($OpportunitiesData[0]['partner_data_id']);
			}
			
			$sm = $this->getServiceLocator();
			
			$tasksTable = $sm->get('Task\Model\TasksTable');
			$tasks = $tasksTable->fetchAll($id, 'opportunity', 1, 100);			
			$closedTasks = $tasksTable->fetchAll($id, 'opportunity', 2, 100);
			
			$usersTable = $this->getServiceLocator()->get('Customer\Model\UsersTable');
			$usersList = $usersTable->fetchUsersForTasks();
			$tasksCategoryTable = $this->getServiceLocator()->get('Task\Model\TasksCategoryTable');
			$CategoryList = $tasksCategoryTable->fetchAll();
			$tasksSubjectTable = $this->getServiceLocator()->get('Task\Model\TasksSubjectTable');
			$subjectList = $tasksSubjectTable->fetchAll();
			$tasksPriorityTable = $this->getServiceLocator()->get('Task\Model\TasksPriorityTable');
			$priorityList = $tasksPriorityTable->fetchAll();
			
			foreach($tasks as $key => $value){
				$tasks[$key]['is_overdue'] = \De\Lib::isTaskOverDue($value);
			}
			
			return array('opportunitiesData' => $OpportunitiesData, 'listProducts' => $listProducts, 'recordsPerPage' => $config['recordsPerPage'],
						 'partnerData' => $partnerData, 'topLevelUsers' => $objUsersTable->fetchSelectOptions(),
						 'tasks' => $tasks, 'closedTasks' => $closedTasks, 'config' => $config,
						 'usersList' => $usersList, 'CategoryList' => $CategoryList, 'subjectList' => $subjectList,
						 'priorityList' => $priorityList, 'canDelete' => $canDelete
			);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deleteopportunityAction(){
		try{
			$id = $this->params('id');
			$OpportunitiesTable = $this->getServiceLocator()->get('Opportunities\Model\OpportunitiesTable');
			$OpportunitiesData = $OpportunitiesTable->deleteOpportunity($id);
			echo $OpportunitiesData; exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function updateopportunitystatusAction(){
		try{
			$request = $this->getRequest();
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			if($request->isPost()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				$OpportunitiesTable = $this->getServiceLocator()->get('Opportunities\Model\OpportunitiesTable');
				$finalData['opportunity_status'] = $data['opportunity_status'];
				$finalData['opportunity_reason'] = $data['opportunity_reason'];
				if($data['opportunity_close_date'] != ''){
					$dateOldFormat = explode("/", $data['opportunity_close_date']);
					$dateNewFormat = $dateOldFormat[2].'-'.$dateOldFormat[1].'-'.$dateOldFormat[0].' 00:00:00';
					$finalData['opportunity_close_date'] = $dateNewFormat;
				} else {
					$finalData['opportunity_close_date'] = null;
				}
					$finalData['updated_by'] = $identity['user_id'];
					$finalData['updated_date'] = date('Y-m-d H:i:s');
					//echo "<pre>"; print_r($finalData); exit;
				$OpportunitiesData = $OpportunitiesTable->saveOpportunities($finalData, $data['opportunity_statusId']);
				echo $OpportunitiesData; exit;
			}
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}
