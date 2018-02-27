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

class EngagementringController extends AbstractActionController
{
    public function indexAction()
    {
		try{
			// Write your code here
			
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $this->getServiceLocator()->get('Config');
			$objEngagementringTable = $this->getServiceLocator()->get('Inventory\Model\EngagementringTable');
			$columnList = $objEngagementringTable->listColumns('engagementring');
			$gridViewTable = $this->getServiceLocator()->get('Customer\Model\GridViewTable');
			$gridViewOptions = $gridViewTable->getGridViews($identity['user_id'], 'engagementring');
			$additionalLookup = array();
			$inventoryForm = $this->getServiceLocator()->get('Inventory\Form\EngagementringInventoryForm');
			$consignForm = $this->getServiceLocator()->get('Inventory\Form\ConsignInventoryForm');
			$objLookupTable = $this->getServiceLocator()->get('Inventory\Model\LookupTable');
			$additionalLookup['setting_style_options'] = $objLookupTable->fetchSettingStyleOptions(true);
			$additionalLookup['gem_type_options'] = $objLookupTable->fetchGemTypeOptions(true);
			$additionalLookup['shape_options'] = $objLookupTable->fetchShapeOptions(true);
			return array('recordsPerPage' => $config['recordsPerPage'], 'columnList' => $columnList,
						 'inventoryForm' => $inventoryForm, 'consignForm' => $consignForm,
						 'gridViewOptions' => $gridViewOptions, 'loginUserId' => $identity['user_id'], 'identity' => $identity, 'additionalLookup' => $additionalLookup);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
    public function saveengagementringAction()
    {
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				unset($posts['supplier_name']);
				$objEngagementringTable = $sm->get('Inventory\Model\EngagementringTable');
				if(isset($posts['engagementRingId'])){
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
				if($posts['ring_type'] != '3'){
					$posts['halo_width'] = null;
					$posts['halo_thickness'] = null;
				}
				
				echo $objEngagementringTable->saveEngagementring($posts);
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	public function ajaxgetengagementringsAction(){
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
			$engagementringTable = $this->getServiceLocator()->get('Inventory\Model\EngagementringTable');
			if($keyword != ''){
				$offset = 0;
			}
			$engagementringsArr = $engagementringTable->fetchAll($limit, $offset, $keyword, $sortdatafield, $sortorder);
			foreach($engagementringsArr['Rows'] as $key => $value){
				foreach($value as $field => $fieldValue){
					if($field == 'created_date'){
						$engagementringsArr['Rows'][$key][$field] = date($config['phpDateFormat'], strtotime($engagementringsArr['Rows'][$key]['created_date']));
					}elseif($field == 'reserve_time' && !empty($engagementringsArr['Rows'][$key]['reserve_time'])){
						$engagementringsArr['Rows'][$key][$field] = date($config['phpDateTimeFormat'], strtotime($engagementringsArr['Rows'][$key]['reserve_time']));
					}
					if(empty($fieldValue)){
						$engagementringsArr['Rows'][$key][$field] = '-';
					}
				}
			}
			echo json_encode($engagementringsArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
	
	public function engagementringdetailsAction(){
		try{
			// Write your code here
			$config = $this->getServiceLocator()->get('Config');
			$id = $this->params('id');
			$additionalLookup = array();
			$EngagementringTable = $this->getServiceLocator()->get('Inventory\Model\EngagementringTable');
			$EngagementringData = (array)$EngagementringTable->fetchEngagementringDetails($id);
			$objLookupTable = $this->getServiceLocator()->get('Inventory\Model\LookupTable');
			$additionalLookup['setting_style_options'] = $objLookupTable->fetchSettingStyleOptions(true);
			$additionalLookup['gem_type_options'] = $objLookupTable->fetchGemTypeOptions(true);
			$additionalLookup['shape_options'] = $objLookupTable->fetchShapeOptions(true);
			$addtionalData = (array)$EngagementringTable->getAdditionalData($id, 'engagementring');
			$ConsignTable = $this->getServiceLocator()->get('Inventory\Model\ConsignTable');
			$ConsignData = (array)$ConsignTable->fetchConsignData($id, 'engagementring');
			$ConsignData['reserve_time'] = (isset($ConsignData['reserve_time']) && !empty($ConsignData['reserve_time'])) ? date($config['phpDateTimeFormat'], strtotime($ConsignData['reserve_time'])) : null;
			return array('recordsPerPage' => $config['recordsPerPage'], 'viewData' => $EngagementringData, 'ringTypesLookup' => $objLookupTable->fetchEngagementRingTypeOptions(), 'metalTypesLookup' => $objLookupTable->fetchMetalTypeOptions(), 'profileTypesLookup' => $objLookupTable->fetchProfileOptions(), 'additionalLookup' => $additionalLookup, 'addtionalData' => $addtionalData, 'ConsignData' => $ConsignData, 'fetchHeadSettingsOptions' => $objLookupTable->fetchHeadSettingsOptions(), 'fetchClawTerminationOptions' => $objLookupTable->fetchClawTerminationOptions());
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deleteengagementringAction(){
		try{
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$EngagementringTable = $this->getServiceLocator()->get('Inventory\Model\EngagementringTable');
				$data = $EngagementringTable->deleteEngagementring($posts['id']);
				echo $data; exit;
			}
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}
