<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class MiscellaneousController extends AbstractActionController
{
    public function indexAction()
    {
		try{
			// Write your code here
			
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $this->getServiceLocator()->get('Config');
			$objMiscellaneousTable = $this->getServiceLocator()->get('Inventory\Model\MiscellaneousTable');
			$columnList = $objMiscellaneousTable->listColumns('miscellaneous');
			$gridViewTable = $this->getServiceLocator()->get('Customer\Model\GridViewTable');
			$gridViewOptions = $gridViewTable->getGridViews($identity['user_id'], 'miscellaneous');
			$inventoryForm = $this->getServiceLocator()->get('Inventory\Form\MiscellaneousInventoryForm');
			$consignForm = $this->getServiceLocator()->get('Inventory\Form\ConsignInventoryForm');
			return array('recordsPerPage' => $config['recordsPerPage'], 'columnList' => $columnList,
						 'inventoryForm' => $inventoryForm, 'consignForm' => $consignForm,
						 'gridViewOptions' => $gridViewOptions, 'loginUserId' => $identity['user_id'], 'identity' => $identity);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
    public function savemiscellaneousAction()
    {
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				unset($posts['supplier_name']);
				$objMiscellaneousTable = $sm->get('Inventory\Model\MiscellaneousTable');
				if(isset($posts['miscellaneousRingId'])){
					$posts['updated_date'] = date('Y-m-d H:i:s');
					$posts['updated_by'] = $identity['user_id'];
				} else {
					$posts['created_date'] = date('Y-m-d H:i:s');
					$posts['created_by'] = $identity['user_id'];
				}
				foreach($posts as $key => $value){
					if(empty($value))
						unset($posts[$key]);
				}
				
				echo $objMiscellaneousTable->saveMiscellaneous($posts);
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	public function ajaxgetmiscellaneousAction(){
    	try{
			$config = $this->getServiceLocator()->get('Config');
			$keyword = '';
			$oppCustomerId = '';
    		$params = $this->getRequest()->getQuery()->toArray();
			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
			$keyword = $params['keyword'];
			
			$sortdatafield = $params['sortdatafield'];
			$sortorder = $params['sortorder'];
			
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			$miscellaneousTable = $this->getServiceLocator()->get('Inventory\Model\MiscellaneousTable');
			if($keyword != ''){
				$offset = 0;
			}
			$miscellaneousArr = $miscellaneousTable->fetchAll($limit, $offset, $keyword, $sortdatafield, $sortorder);
			foreach($miscellaneousArr['Rows'] as $key => $value){
				foreach($value as $field => $fieldValue){
					if($field == 'created_date'){
						$miscellaneousArr['Rows'][$key][$field] = date($config['phpDateFormat'], strtotime($miscellaneousArr['Rows'][$key]['created_date']));
					}elseif($field == 'reserve_time' && !empty($miscellaneousArr['Rows'][$key]['reserve_time'])){
						$miscellaneousArr['Rows'][$key][$field] = date($config['phpDateTimeFormat'], strtotime($miscellaneousArr['Rows'][$key]['reserve_time']));
					}
					if(empty($fieldValue)){
						$miscellaneousArr['Rows'][$key][$field] = '-';
					}
				}
			}
			echo json_encode($miscellaneousArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
	
	public function miscellaneousdetailsAction(){
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$canDelete = $sm->get('ControllerPluginManager')->get('AuthPlugin')->checkResource($sm->get('AuthService'), 'Inventory\Controller\Miscellaneous::deletemiscellaneous');
				
			$config = $sm->get('Config');
			$id = $this->params('id');
			$additionalLookup = array();
			$MiscellaneousTable = $sm->get('Inventory\Model\MiscellaneousTable');
			$MiscellaneousData = (array)$MiscellaneousTable->fetchMiscellaneousDetails($id);
			$ConsignTable = $sm->get('Inventory\Model\ConsignTable');
			$ConsignData = (array)$ConsignTable->fetchConsignData($id, 'miscellaneous');
			$ConsignData['reserve_time'] = (isset($ConsignData['reserve_time']) && !empty($ConsignData['reserve_time'])) ? date($config['phpDateTimeFormat'], strtotime($ConsignData['reserve_time'])) : null;
			return array(
					'recordsPerPage' => $config['recordsPerPage'], 'viewData' => $MiscellaneousData, 'ConsignData' => $ConsignData,
					'canDelete' => $canDelete
			);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deletemiscellaneousAction(){
		try{
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$MiscellaneousTable = $this->getServiceLocator()->get('Inventory\Model\MiscellaneousTable');
				$data = $MiscellaneousTable->deleteMiscellaneous($posts['id']);
				echo $data; exit;
			}
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}
