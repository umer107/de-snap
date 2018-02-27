<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
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
				$finalData = array();
				$supplierTable = $this->getServiceLocator()->get('Suppliers\Model\SuppliersTable');
				
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
			
			$suppliersForm = $this->getServiceLocator()->get('Suppliers\Form\SuppliersForm');
			$config = $this->getServiceLocator()->get('Config');
			$leadsTable = $this->getServiceLocator()->get('Customer\Model\LeadsTable');
			$columnList = $leadsTable->listColumns('suppliers');
			$gridViewTable = $this->getServiceLocator()->get('Customer\Model\GridViewTable');
			$gridViewOptions = $gridViewTable->getGridViews($identity['user_id'], 'suppliers');
			return array('form' => $suppliersForm, 'recordsPerPage' => $config['recordsPerPage'],
						 'columnList' => $columnList, 'gridViewOptions' => $gridViewOptions,
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
			$suppliersArr = $suppliersTable->fetchAll($limit, $offset, $keyword, $oppCustomerId, $sortdatafield, $sortorder);
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
			$config = $this->getServiceLocator()->get('Config');
			$id = $this->params('id');
			$SuppliersTable = $this->getServiceLocator()->get('Suppliers\Model\SuppliersTable');
			$SuppliersData = (array)$SuppliersTable->fetchSupplierDetails($id);
			$objUsersTable = $this->getServiceLocator()->get('Customer\Model\UsersTable');
			
			$partnerData = array();
			$sm = $this->getServiceLocator();
			
			$tasksTable = $sm->get('Task\Model\TasksTable');
			$tasks = $tasksTable->fetchAll($id, 'supplier', 1, 100);			
			$closedTasks = $tasksTable->fetchAll($id, 'supplier', 2, 100);
			
			$usersTable = $this->getServiceLocator()->get('Customer\Model\UsersTable');
			$usersList = $usersTable->fetchUsersForTasks();
			$tasksCategoryTable = $this->getServiceLocator()->get('Task\Model\TasksCategoryTable');
			$CategoryList = $tasksCategoryTable->fetchAll();
			$tasksSubjectTable = $this->getServiceLocator()->get('Task\Model\TasksSubjectTable');
			$subjectList = $tasksSubjectTable->fetchAll();
			$tasksPriorityTable = $this->getServiceLocator()->get('Task\Model\TasksPriorityTable');
			$priorityList = $tasksPriorityTable->fetchAll();
			
			return array('suppliersData' => $SuppliersData, 'supplierTypesLookup' => $SuppliersTable->getSupplierTypesLookup(), 'serviceTypesLookup' => $SuppliersTable->getServicesTypesLookup(), 'servicesString' => $SuppliersTable->getSupplierServicesList($id, true));
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
}
