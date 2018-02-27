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

class PendantController extends AbstractActionController
{
    public function indexAction()
    {
		try{
			// Write your code here
			
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $this->getServiceLocator()->get('Config');
			$objPendantTable = $this->getServiceLocator()->get('Inventory\Model\PendantTable');
			$columnList = $objPendantTable->listColumns('pendant');
			$gridViewTable = $this->getServiceLocator()->get('Customer\Model\GridViewTable');
			$gridViewOptions = $gridViewTable->getGridViews($identity['user_id'], 'pendant');
			$additionalLookup = array();
			$inventoryForm = $this->getServiceLocator()->get('Inventory\Form\PendantInventoryForm');
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
	
    public function savependantAction()
    {
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				unset($posts['supplier_name']);
				$objPendantTable = $sm->get('Inventory\Model\PendantTable');
				if(isset($posts['pendantRingId'])){
					$posts['updated_date'] = date('Y-m-d H:i:s');
					$posts['updated_by'] = $identity['user_id'];
				} else {
					$posts['created_date'] = date('Y-m-d H:i:s');
					$posts['created_by'] = $identity['user_id'];
				}
				$objLookupTable = $this->getServiceLocator()->get('Inventory\Model\LookupTable');
				$earRingStyle = $objLookupTable->fetchPendantStyleOptions();
				if($earRingStyle[$posts['ring_style']] != 'Other'){
					$posts['other_ring_style'] = ' ';
				}
				
				foreach($posts as $key => $value){
					if(empty($value))
						unset($posts[$key]);
				}
				
				if($earRingStyle[$posts['ring_style']] != 'Halo'){
					$posts['halo_width'] = null;
					$posts['halo_thickness'] = null;
				}
				
				echo $objPendantTable->savePendant($posts);
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	public function ajaxgetpendantsAction(){
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
			$pendantTable = $this->getServiceLocator()->get('Inventory\Model\PendantTable');
			if($keyword != ''){
				$offset = 0;
			}
			$pendantsArr = $pendantTable->fetchAll($limit, $offset, $keyword, $sortdatafield, $sortorder);
			foreach($pendantsArr['Rows'] as $key => $value){
				foreach($value as $field => $fieldValue){
					if($field == 'created_date'){
						$pendantsArr['Rows'][$key][$field] = date($config['phpDateFormat'], strtotime($pendantsArr['Rows'][$key]['created_date']));
					}elseif($field == 'reserve_time' && !empty($pendantsArr['Rows'][$key]['reserve_time'])){
						$pendantsArr['Rows'][$key][$field] = date($config['phpDateTimeFormat'], strtotime($pendantsArr['Rows'][$key]['reserve_time']));
					}
					if(empty($fieldValue)){
						$pendantsArr['Rows'][$key][$field] = '-';
					}
				}
			}
			echo json_encode($pendantsArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
	
	public function pendantdetailsAction(){
		try{
			// Write your code here
			$config = $this->getServiceLocator()->get('Config');
			$id = $this->params('id');
			$additionalLookup = array();
			$PendantTable = $this->getServiceLocator()->get('Inventory\Model\PendantTable');
			$PendantData = (array)$PendantTable->fetchPendantDetails($id);
			$objLookupTable = $this->getServiceLocator()->get('Inventory\Model\LookupTable');
			$additionalLookup['setting_style_options'] = $objLookupTable->fetchSettingStyleOptions(true);
			$additionalLookup['gem_type_options'] = $objLookupTable->fetchGemTypeOptions(true);
			$additionalLookup['shape_options'] = $objLookupTable->fetchShapeOptions(true);
			$addtionalData = (array)$PendantTable->getAdditionalData($id, 'pendant');
			$ConsignTable = $this->getServiceLocator()->get('Inventory\Model\ConsignTable');
			$ConsignData = (array)$ConsignTable->fetchConsignData($id, 'pendant');
			$ConsignData['reserve_time'] = (isset($ConsignData['reserve_time']) && !empty($ConsignData['reserve_time'])) ? date($config['phpDateTimeFormat'], strtotime($ConsignData['reserve_time'])) : null;
			return array('recordsPerPage' => $config['recordsPerPage'], 'viewData' => $PendantData, 'ringTypesLookup' => $objLookupTable->fetchPendantStyleOptions(), 'fetchHeadSettingsOptions' => $objLookupTable->fetchHeadSettingsOptions(), 'metalTypesLookup' => $objLookupTable->fetchMetalTypeOptions(), 'profileTypesLookup' => $objLookupTable->fetchProfileOptions(), 'additionalLookup' => $additionalLookup, 'addtionalData' => $addtionalData, 'ConsignData' => $ConsignData);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deletependantAction(){
		try{
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$PendantTable = $this->getServiceLocator()->get('Inventory\Model\PendantTable');
				$data = $PendantTable->deletePendant($posts['id']);
				echo $data; exit;
			}
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}
