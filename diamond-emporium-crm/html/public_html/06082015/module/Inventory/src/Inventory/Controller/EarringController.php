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

class EarringController extends AbstractActionController
{
    public function indexAction()
    {
		try{
			// Write your code here
			
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $this->getServiceLocator()->get('Config');
			$objEarringTable = $this->getServiceLocator()->get('Inventory\Model\EarringTable');
			$columnList = $objEarringTable->listColumns('earring');
			$gridViewTable = $this->getServiceLocator()->get('Customer\Model\GridViewTable');
			$gridViewOptions = $gridViewTable->getGridViews($identity['user_id'], 'earring');
			$additionalLookup = array();
			$inventoryForm = $this->getServiceLocator()->get('Inventory\Form\EarringInventoryForm');
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
	
    public function saveearringAction()
    {
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				unset($posts['supplier_name']);
				$objEarringTable = $sm->get('Inventory\Model\EarringTable');
				if(isset($posts['earringRingId'])){
					$posts['updated_date'] = date('Y-m-d H:i:s');
					$posts['updated_by'] = $identity['user_id'];
				} else {
					$posts['created_date'] = date('Y-m-d H:i:s');
					$posts['created_by'] = $identity['user_id'];
				}
				$objLookupTable = $this->getServiceLocator()->get('Inventory\Model\LookupTable');
				$earRingStyle = $objLookupTable->fetchEarringStyleOptions();
				if($earRingStyle[$posts['ring_style']] != 'Other'){
					$posts['other_ring_style'] = ' ';
				}
				foreach($posts as $key => $value){
					if(empty($value))
						unset($posts[$key]);
				}
				
				echo $objEarringTable->saveEarring($posts);
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	public function ajaxgetearringsAction(){
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
			$earringTable = $this->getServiceLocator()->get('Inventory\Model\EarringTable');
			if($keyword != ''){
				$offset = 0;
			}
			$earringsArr = $earringTable->fetchAll($limit, $offset, $keyword, $sortdatafield, $sortorder);
			foreach($earringsArr['Rows'] as $key => $value){
				foreach($value as $field => $fieldValue){
					if($field == 'created_date'){
						$earringsArr['Rows'][$key][$field] = date($config['phpDateFormat'], strtotime($earringsArr['Rows'][$key]['created_date']));
					}elseif($field == 'reserve_time' && !empty($earringsArr['Rows'][$key]['reserve_time'])){
						$earringsArr['Rows'][$key][$field] = date($config['phpDateTimeFormat'], strtotime($earringsArr['Rows'][$key]['reserve_time']));
					}
					if(empty($fieldValue)){
						$earringsArr['Rows'][$key][$field] = '-';
					}
				}
			}
			echo json_encode($earringsArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
	
	public function earringdetailsAction(){
		try{
			// Write your code here
			$config = $this->getServiceLocator()->get('Config');
			$id = $this->params('id');
			$additionalLookup = array();
			$EarringTable = $this->getServiceLocator()->get('Inventory\Model\EarringTable');
			$EarringData = (array)$EarringTable->fetchEarringDetails($id);
			$objLookupTable = $this->getServiceLocator()->get('Inventory\Model\LookupTable');
			$additionalLookup['setting_style_options'] = $objLookupTable->fetchSettingStyleOptions(true);
			$additionalLookup['gem_type_options'] = $objLookupTable->fetchGemTypeOptions(true);
			$additionalLookup['shape_options'] = $objLookupTable->fetchShapeOptions(true);
			$addtionalData = (array)$EarringTable->getAdditionalData($id, 'earring');
			$ConsignTable = $this->getServiceLocator()->get('Inventory\Model\ConsignTable');
			$ConsignData = (array)$ConsignTable->fetchConsignData($id, 'earring');
			$ConsignData['reserve_time'] = (isset($ConsignData['reserve_time']) && !empty($ConsignData['reserve_time'])) ? date($config['phpDateTimeFormat'], strtotime($ConsignData['reserve_time'])) : null;
			return array('recordsPerPage' => $config['recordsPerPage'], 'viewData' => $EarringData, 'ringTypesLookup' => $objLookupTable->fetchEarringStyleOptions(), 'metalTypesLookup' => $objLookupTable->fetchMetalTypeOptions(), 'profileTypesLookup' => $objLookupTable->fetchProfileOptions(), 'additionalLookup' => $additionalLookup, 'addtionalData' => $addtionalData, 'ConsignData' => $ConsignData);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deleteearringAction(){
		try{
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$EarringTable = $this->getServiceLocator()->get('Inventory\Model\EarringTable');
				$data = $EarringTable->deleteEarring($posts['id']);
				echo $data; exit;
			}
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}
