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

class WeddingringController extends AbstractActionController
{
    public function indexAction()
    {
		try{
			// Write your code here
			
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $this->getServiceLocator()->get('Config');
			$objWeddingringTable = $this->getServiceLocator()->get('Inventory\Model\WeddingringTable');
			$columnList = $objWeddingringTable->listColumns('weddingring');
			$gridViewTable = $this->getServiceLocator()->get('Customer\Model\GridViewTable');
			$gridViewOptions = $gridViewTable->getGridViews($identity['user_id'], 'weddingring');
			$additionalLookup = array();
			$inventoryForm = $this->getServiceLocator()->get('Inventory\Form\WeddingringInventoryForm');
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
	
    public function saveweddingringAction()
    {
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				unset($posts['supplier_name']);
				$objWeddingringTable = $sm->get('Inventory\Model\WeddingringTable');
				if(isset($posts['weddingRingId'])){
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
				
				echo $objWeddingringTable->saveWeddingring($posts);
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	public function ajaxgetweddingringsAction(){
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
			$weddingringTable = $this->getServiceLocator()->get('Inventory\Model\WeddingringTable');
			if($keyword != ''){
				$offset = 0;
			}
			$weddingringsArr = $weddingringTable->fetchAll($limit, $offset, $keyword, $sortdatafield, $sortorder);
			
			foreach($weddingringsArr['Rows'] as $key => $value){
				foreach($value as $field => $fieldValue){
					if($field == 'created_date'){
						$weddingringsArr['Rows'][$key][$field] = date($config['phpDateFormat'], strtotime($weddingringsArr['Rows'][$key]['created_date']));
					}elseif($field == 'reserve_time' && !empty($weddingringsArr['Rows'][$key]['reserve_time'])){
						$weddingringsArr['Rows'][$key][$field] = date($config['phpDateTimeFormat'], strtotime($weddingringsArr['Rows'][$key]['reserve_time']));
					}
					if(empty($fieldValue)){
						$weddingringsArr['Rows'][$key][$field] = '-';
					}
				}
			}
			echo json_encode($weddingringsArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
	
	public function weddingringdetailsAction(){
		try{
			// Write your code here
			$config = $this->getServiceLocator()->get('Config');
			$id = $this->params('id');
			$additionalLookup = array();
			$WeddingringTable = $this->getServiceLocator()->get('Inventory\Model\WeddingringTable');
			$WeddingringData = (array)$WeddingringTable->fetchWeddingringDetails($id);
			$objLookupTable = $this->getServiceLocator()->get('Inventory\Model\LookupTable');
			$additionalLookup['setting_style_options'] = $objLookupTable->fetchSettingStyleOptions(true);
			$additionalLookup['gem_type_options'] = $objLookupTable->fetchGemTypeOptions(true);
			$additionalLookup['shape_options'] = $objLookupTable->fetchShapeOptions(true);
			$addtionalData = (array)$WeddingringTable->getAdditionalData($id, 'weddingring');
			$ConsignTable = $this->getServiceLocator()->get('Inventory\Model\ConsignTable');
			$ConsignData = (array)$ConsignTable->fetchConsignData($id, 'weddingring');
			$ConsignData['reserve_time'] = (isset($ConsignData['reserve_time']) && !empty($ConsignData['reserve_time'])) ? date($config['phpDateTimeFormat'], strtotime($ConsignData['reserve_time'])) : null;
			
			return array('recordsPerPage' => $config['recordsPerPage'], 'viewData' => $WeddingringData, 'ringTypesLookup' => $objLookupTable->fetchRingTypeOptions(), 'metalTypesLookup' => $objLookupTable->fetchMetalTypeOptions(), 'profileTypesLookup' => $objLookupTable->fetchProfileOptions(), 'finishTypesLookup' => $objLookupTable->fetchFinishOptions(), 'fitoptionsTypesLookup' => $objLookupTable->fetchFitOptions(), 'additionalLookup' => $additionalLookup, 'addtionalData' => $addtionalData, 'ConsignData' => $ConsignData);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deleteadditionalAction(){
		try{
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$id = $posts['id'];
				$type = $posts['type'];
				if($type == "weddingring"){
					$WeddingringTable = $this->getServiceLocator()->get('Inventory\Model\WeddingringTable');
					$rowData = $WeddingringTable->deleteAdditionalRow($id, $type);
					echo $rowData; exit;
				} else if($type == "engagementring"){
					$EngagementringTable = $this->getServiceLocator()->get('Inventory\Model\EngagementringTable');
					$rowData = $EngagementringTable->deleteAdditionalRow($id, $type);
					echo $rowData; exit;
				} else if($type == "earring"){
					$EarringTable = $this->getServiceLocator()->get('Inventory\Model\EarringTable');
					$rowData = $EarringTable->deleteAdditionalRow($id, $type);
					echo $rowData; exit;
				} else if($type == "pendant"){
					$PendantTable = $this->getServiceLocator()->get('Inventory\Model\PendantTable');
					$rowData = $PendantTable->deleteAdditionalRow($id, $type);
					echo $rowData; exit;
				}
			}
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function getadditionallistAction(){
		try{
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				if($posts['type'] == "weddingring"){
					$WeddingringTable = $this->getServiceLocator()->get('Inventory\Model\WeddingringTable');
					$addtionalData = (array)$WeddingringTable->getAdditionalData($posts['id'], $posts['type']);
					echo json_encode($addtionalData); exit;
				} else if($posts['type'] == "engagementring"){
					$EngagementringTable = $this->getServiceLocator()->get('Inventory\Model\EngagementringTable');
					$addtionalData = (array)$EngagementringTable->getAdditionalData($posts['id'], $posts['type']);
					echo json_encode($addtionalData); exit;
				} else if($posts['type'] == "earring"){
					$EarringTable = $this->getServiceLocator()->get('Inventory\Model\EarringTable');
					$addtionalData = (array)$EarringTable->getAdditionalData($posts['id'], $posts['type']);
					echo json_encode($addtionalData); exit;
				} else if($posts['type'] == "pendant"){
					$PendantTable = $this->getServiceLocator()->get('Inventory\Model\PendantTable');
					$addtionalData = (array)$PendantTable->getAdditionalData($posts['id'], $posts['type']);
					echo json_encode($addtionalData); exit;
				}
			}
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deleteweddingringAction(){
		try{
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$WeddingringTable = $this->getServiceLocator()->get('Inventory\Model\WeddingringTable');
				$data = $WeddingringTable->deleteWeddingring($posts['id']);
				echo $data; exit;
			}
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}
