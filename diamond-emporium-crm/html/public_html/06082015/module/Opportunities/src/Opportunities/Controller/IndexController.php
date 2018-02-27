<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
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
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost();
				
				$leadsTable = $this->getServiceLocator()->get('Opportunities\Model\OpportunitiesTable');
				$data = $posts->toArray();
				$finalData = array();
				$opportunitiesTable = $this->getServiceLocator()->get('Opportunities\Model\OpportunitiesTable');
				$finalData['opportunity_type'] = $data['opportunity_type'];
				$finalData['lead_source'] = $data['lead_source'];
				$finalData['referred_by_customer'] = $data['referred_by_customer'];
				$finalData['product'] = $data['product'];
				$finalData['reference_product'] = $data['reference_product'];
				$finalData['looking_for'] = $data['looking_for'];
				$finalData['preferred_contact'] = $data['preferred_contact'];
				$finalData['budget'] = $data['budget'];
				$finalData['progress_of_opportunity'] = $data['progress_of_opportunity'];
				$finalData['urgency'] = $data['urgency'];
				$finalData['rating'] = $data['rating'];
				$finalData['probability'] = $data['probability'];
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
			return array('form' => $opportunitiesForm, 'recordsPerPage' => $config['recordsPerPage'],
						 'columnList' => $columnList, 'gridViewOptions' => $gridViewOptions,
						 'loginUserId' => $identity['user_id'], 'config' => $config, 'tasks' => $tasks);
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
			$opportunitiesArr = $opportunitiesTable->fetchAll($limit, $offset, $keyword, $oppCustomerId, $sortdatafield, $sortorder);
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
			$config = $this->getServiceLocator()->get('Config');
			
    		//$router = $this->getEvent()->getRouteMatch();
			$id = $this->params('id');
			$OpportunitiesTable = $this->getServiceLocator()->get('Opportunities\Model\OpportunitiesTable');
			$OpportunitiesData = (array)$OpportunitiesTable->fetchOpportunityDetails($id);
			
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
						 'priorityList' => $priorityList);
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
	
	public function ajaxgetnotesAction(){
    	try{
			$config = $this->getServiceLocator()->get('Config');
    		$params = $this->getRequest()->getQuery()->toArray();
			$typeName = $this->params('type');
			$typeId = $this->params('id');
			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			$notesTable = $this->getServiceLocator()->get('Opportunities\Model\OpportunitiesTable');
			$notesArr = $notesTable->fetchNotes($limit, $offset, $typeName, $typeId);
			foreach($notesArr as $key => $value){
				foreach($value as $field => $fieldValue){
					if($field == 'created_date'){
						$notesArr[$key]['created_date'] = date($config['phpDateFormat'], strtotime($notesArr[$key]['created_date']));
					}
					if($field == 'follow_up_date'){
						$notesArr[$key]['follow_up_date'] = date($config['phpDateFormat'], strtotime($notesArr[$key]['follow_up_date']));
					}
				}
			}
			echo json_encode($notesArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
	
	public function notesAction()
    {
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			$notesTable = $this->getServiceLocator()->get('Opportunities\Model\NotesTable');
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				$finalData = array();
				if(!empty($data['noteId'])){
					$noteDetails = $notesTable->getNoteDetails($data['noteId']);
					echo json_encode($noteDetails);
					exit;
				} else if(!empty($data['noteUpdateId'])){
					if($data['follow_up_date'] != ''){
						$dateOldFormat = explode("/", $data['follow_up_date']);
						$dateNewFormat = $dateOldFormat[2].'-'.$dateOldFormat[1].'-'.$dateOldFormat[0].' 00:00:00';
						$finalData['follow_up_date'] = $dateNewFormat;
					}
					if($data['note_type'] != ''){
						$finalData['note_type'] = $data['note_type'];
					}
					if($data['note_description'] != ''){
						$finalData['note_description'] = $data['note_description'];
					}
					if($data['type'] != ''){
						$finalData['grid_type'] = $data['type'];
					}
					if($data['typeId'] != ''){
						$finalData['grid_type_id'] = $data['typeId'];
					}
					$finalData['modified_by'] = $identity['user_id'];
					$finalData['modified_date'] = date('Y-m-d H:i:s');
					echo $notesTable->saveNotes($finalData, $data['noteUpdateId']);
					exit;
				} else if(!empty($data['deleteNote'])){
					echo $notesTable->deleteNotes($data['deleteNote']);
					exit;
				} else {
					if($data['follow_up_date'] != ''){
						$dateOldFormat = explode("/", $data['follow_up_date']);
						$dateNewFormat = $dateOldFormat[2].'-'.$dateOldFormat[1].'-'.$dateOldFormat[0].' 00:00:00';
						$finalData['follow_up_date'] = $dateNewFormat;
					}
					$finalData['note_type'] = $data['note_type'];
					$finalData['note_description'] = $data['note_description'];
					$finalData['grid_type'] = $data['type'];
					$finalData['grid_type_id'] = $data['typeId'];
					$finalData['created_by'] = $identity['user_id'];
					echo $notesTable->saveNotes($finalData);
					exit;
				}
				
			}
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
}
