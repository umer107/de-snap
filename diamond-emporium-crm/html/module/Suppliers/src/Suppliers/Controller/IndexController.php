<?php
/**
 * Supplier Controller
 */

namespace Suppliers\Controller;

use Suppliers\Form\SuppliersForm;

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
				$data = $posts->toArray();
				$supplierTable = $this->getServiceLocator()->get('Suppliers\Model\SuppliersTable');
				
				/* Add HTTP:// protocol to URL if missing */
				
				if (preg_match("#https?://#", $data['website']) === 0) {
					$data['website'] = 'http://' . $data['website'];
				}
				
				if(empty($data['supplierId'])){
					$data['created_by'] = $identity['user_id'];
					echo $supplierTable->saveSuppliers($data);
				} else {
					$data['updated_by'] = $identity['user_id'];
					$data['updated_date'] = date('Y-m-d H:i:s');
					echo $supplierTable->saveSuppliers($data, $data['supplierId']);
				}
				exit;
			}
			
			$suppliersForm = $sm->get('Suppliers\Form\SuppliersForm');
			$config = $sm->get('Config');
			$leadsTable = $sm->get('Customer\Model\LeadsTable');
			$columnList = $leadsTable->listColumns('suppliers');
			$gridViewTable = $sm->get('Customer\Model\GridViewTable');
			$gridViewOptions = $gridViewTable->getGridViews($identity['user_id'], 'suppliers');
			
			$suppliersTable = $sm->get('Suppliers\Model\SuppliersTable');
			$serviceTypes  = $suppliersTable->getServicesTypesLookup();
			
			return array('form' => $suppliersForm, 'recordsPerPage' => $config['recordsPerPage'],
						 'columnList' => $columnList, 'gridViewOptions' => $gridViewOptions,
						 'serviceTypes' => $serviceTypes,
						 'loginUserId' => $identity['user_id'], 'config' => $config, 'tasks' => $tasks);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	public function ajaxgetsuppliersAction(){
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
			$suppliersTable = $this->getServiceLocator()->get('Suppliers\Model\SuppliersTable');
			if($keyword != ''){
				$offset = 0;
			}
			$suppliersArr = $suppliersTable->fetchAll($limit, $offset, $keyword, $oppCustomerId, $sortdatafield, $sortorder, $params);
			foreach($suppliersArr['Rows'] as $key => $value){
				foreach($value as $field => $fieldValue){
					if($field == 'created_date'){
						$suppliersArr['Rows'][$key][$field] = date($config['phpDateFormat'], strtotime($suppliersArr['Rows'][$key]['created_date']));
					}
				}
			}
			echo json_encode($suppliersArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
	
	public function supplierdetailsAction(){
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$canDelete = $sm->get('ControllerPluginManager')->get('AuthPlugin')->checkResource($sm->get('AuthService'), 'Suppliers\Controller\Index::deletesupplier');
			
			$config = $sm->get('Config');
			$id = $this->params('id');
			$StatesTable = $sm->get('Customer\Model\StatesTable');
			$CountryTable = $sm->get('Customer\Model\CountryTable');
			$SuppliersTable = $sm->get('Suppliers\Model\SuppliersTable');
			$SuppliersData = (array)$SuppliersTable->fetchSupplierDetails($id);

			$objUsersTable = $sm->get('Customer\Model\UsersTable');
			
			$partnerData = array();
			
			$tasksTable = $sm->get('Task\Model\TasksTable');
			$tasks = $tasksTable->fetchAll($id, 'supplier', 1, 100);			
			$closedTasks = $tasksTable->fetchAll($id, 'supplier', 2, 100);
			
			$usersTable = $sm->get('Customer\Model\UsersTable');
			$usersList = $usersTable->fetchUsersForTasks();
			$tasksCategoryTable = $sm->get('Task\Model\TasksCategoryTable');
			$CategoryList = $tasksCategoryTable->fetchAll();
			$tasksSubjectTable = $sm->get('Task\Model\TasksSubjectTable');
			$subjectList = $tasksSubjectTable->fetchAll();
			$tasksPriorityTable = $sm->get('Task\Model\TasksPriorityTable');
			$priorityList = $tasksPriorityTable->fetchAll();
			
			$emailCount = $SuppliersTable->fetchEmailCount($id);
			
			return array(
					'suppliersData' => $SuppliersData,
					'supplierTypesLookup' => $SuppliersTable->getSupplierTypesLookup(),
					'serviceTypesLookup' => $SuppliersTable->getServicesTypesLookup(),
					'servicesString' => $SuppliersTable->getSupplierServicesList($id, true),
					'statesLookup' => $StatesTable->fetchSelectOptions(),
					'countryLookup' => $CountryTable->fetchSelectOptions(),
					'recordsPerPage' => $config['recordsPerPage'], 'emailCount' => $emailCount,
					'canDelete' => $canDelete
			);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deletesupplierAction(){
		try{
			$id = $this->params('id');
			$SuppliersTable = $this->getServiceLocator()->get('Suppliers\Model\SuppliersTable');
			$SuppliersData = $SuppliersTable->deleteSupplier($id);
			echo $SuppliersData; exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	
	public function newsuppliersformAction(){
		try{
			$sm = $this->getServiceLocator();
			$form = $sm->get('Suppliers\Form\SuppliersForm');
			$config = $sm->get('Config');
				
			$viewModel = new ViewModel();
			$viewModel->setVariables(array('recordsPerPage' => $config['recordsPerPage'], 'form' => $form));
			$viewModel->setTerminal(true);
			return $viewModel;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}
