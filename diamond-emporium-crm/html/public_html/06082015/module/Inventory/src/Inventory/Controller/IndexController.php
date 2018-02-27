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

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
		try{
			// Write your code here
			
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			$objDiamondTable = $sm->get('Inventory\Model\DiamondTable');
			$columnList = $objDiamondTable->listColumns('diamond');
			
			$gridViewTable = $sm->get('Customer\Model\GridViewTable');
			$gridViewOptions = $gridViewTable->getGridViews($identity['user_id'], 'diamond');
			
			$inventoryForm = $sm->get('Inventory\Form\DiamondInventoryForm');
			
			$objLookupTable = $sm->get('Inventory\Model\LookupTable');
			$shapeOptions = $objLookupTable->fetchShapeOptions();
			$colorOptions = $objLookupTable->fetchColorOptions();
			$inventoryStatusOptions = $objLookupTable->fetchInventoryStatusOptions();
			$cutOptions = array('Select', 1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
			$clarityOptions = $objLookupTable->fetchClarityOptions();
			
			return array('recordsPerPage' => $config['recordsPerPage'], 'columnList' => $columnList, 'inventoryForm' => $inventoryForm,
						 'gridViewOptions' => $gridViewOptions, 'loginUserId' => $identity['user_id'], 'identity' => $identity,
						 'shapeOptions' => $shapeOptions, 'inventoryStatusOptions' => $inventoryStatusOptions,
						 'colorOptions' => $colorOptions, 'cutOptions' => $cutOptions, 'clarityOptions' => $clarityOptions);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
    public function savediamondAction()
    {
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				unset($posts['supplier_name']);
				$objDiamondTable = $sm->get('Inventory\Model\DiamondTable');
				if(isset($posts['DiamondId'])){
					$posts['updated_date'] = date('Y-m-d H:i:s');
					$posts['updated_by'] = $identity['user_id'];
				} else {
					$posts['created_date'] = date('Y-m-d H:i:s');
					$posts['created_by'] = $identity['user_id'];
				}
				if(!isset($posts['intensity'])){
					$posts['intensity'] = 0;
				}
				if(!isset($posts['overtone'])){
					$posts['overtone'] = 0;
				}
				
				foreach($posts as $key => $value){
					if(empty($value))
						unset($posts[$key]);
				}
				
				if($posts['diamond_type'] == 1){
					$posts['color'] = 0;
				}elseif($posts['diamond_type'] == 2){
					$posts['white_type'] = null;
				}
				
				echo $objDiamondTable->saveDiamond($posts);
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	public function ajaxsupplierslookupAction(){
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
			
			$objSuppliersTable = $this->getServiceLocator()->get('Suppliers\Model\SuppliersTable');
			$suppliersArr = $objSuppliersTable->supplierLookup($limit, $offset, $keyword, $sortdatafield, $sortorder);
			echo json_encode($suppliersArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
	
	public function ajaxgetdiamondsAction(){
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
			$diamondTable = $this->getServiceLocator()->get('Inventory\Model\DiamondTable');
			if($keyword != ''){
				$offset = 0;
			}
			
			$request = $this->getRequest();
			$posts = $request->getPost()->toArray();
				
			$diamondsArr = $diamondTable->fetchAll($limit, $offset, $keyword, $sortdatafield, $sortorder, $params);
			foreach($diamondsArr['Rows'] as $key => $value){
				foreach($value as $field => $fieldValue){
					if($field == 'created_date'){
						$diamondsArr['Rows'][$key][$field] = date($config['phpDateFormat'], strtotime($diamondsArr['Rows'][$key]['created_date']));
					}elseif($field == 'reserve_time' && !empty($diamondsArr['Rows'][$key]['reserve_time'])){
						$diamondsArr['Rows'][$key][$field] = date($config['phpDateTimeFormat'], strtotime($diamondsArr['Rows'][$key]['reserve_time']));
					}
					if(empty($fieldValue)){
						$diamondsArr['Rows'][$key][$field] = '-';
					}
				}
			}
			echo json_encode($diamondsArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
	
	public function uploadfileAction(){
		try{
			// Write your code here
			$config = $this->getServiceLocator()->get('Config');
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				
				if(is_uploaded_file($_FILES['item_image']['tmp_name'])){
					echo \De\Service\CommonService::uploadFile($_FILES['item_image'], 'inventory_images', $config, array('gif', 'png', 'jpeg', 'jpg'));
				}elseif(is_uploaded_file($_FILES['item_invoice']['tmp_name'])){
					echo \De\Service\CommonService::uploadFile($_FILES['item_invoice'], 'invoice', $config, null);
				}elseif(is_uploaded_file($_FILES['order_file']['tmp_name'])){					
					echo \De\Service\CommonService::uploadFile($_FILES['order_file'], 'order_attachment', $config, null);
				}elseif(is_uploaded_file($_FILES['cad_image']['tmp_name'])){					
					echo \De\Service\CommonService::uploadFile($_FILES['cad_image'], 'milestone_attachments', $config, array('gif', 'png', 'jpeg', 'jpg'));
				}elseif(is_uploaded_file($_FILES['workshop_production_line_image']['tmp_name'])){					
					echo \De\Service\CommonService::uploadFile($_FILES['workshop_production_line_image'], 'milestone_attachments', $config, array('gif', 'png', 'jpeg', 'jpg'));
				}
			}
			
			exit;
			
		}catch(Exception $e){echo $e->getMessage ();
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function consignformAction(){
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$config = $sm->get('Config');
			
			$identity = $sm->get('AuthService')->getIdentity();
			
			$viewRender = $sm->get('ViewRenderer');
    		
			$request = $this->getRequest();
			
			$htmlViewPart = new ViewModel();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				
				$objLookupTable = $sm->get('Inventory\Model\LookupTable');
				$inventoryStatusReasons = $objLookupTable->fetchInventoryStatusReasonOptions();
				$inventoryTrackingReasons = $objLookupTable->fetchInventoryTrackingReasonOptions();
				
				$objConsignTable = $sm->get('Inventory\Model\ConsignTable');
				$consignData = $objConsignTable->fetchConsignData($posts['item_id'], $posts['jewel_type']);
				$consignData['inventory_type'] = $consignData['inventory_type_id'];
				$consignData['inventory_tracking_status'] = $consignData['inventory_tracking_status_id'];			
				
				$consignData['db_reserve_time'] = $consignData['reserve_time'];
				$consignData['reserve_time'] = (isset($consignData['reserve_time']) && !empty($consignData['reserve_time'])) ? date($config['formDateTimeFormat'], strtotime($consignData['reserve_time'])) : null;
				
				$consignForm = $sm->get('Inventory\Form\ConsignInventoryForm');
				$consignForm->setData($consignData);				
				
				$consignForm->get('item_id')->setAttribute('value', $posts['item_id']);
				$consignForm->get('jewel_type')->setAttribute('value', $posts['jewel_type']);
				$consignForm->get('edit_mode')->setAttribute('value', $posts['mode']);
				$htmlViewPart->setTemplate('inventory/index/consignform')
							 ->setTerminal(true)
							 ->setVariables(array('recordsPerPage' => $config['recordsPerPage'], 'consignForm' => $consignForm, 'consignData' => $consignData,
							 					  'inventoryStatusReasons' => $inventoryStatusReasons, 'inventoryTrackingReasons' => $inventoryTrackingReasons,
												  'identity' => $identity, 'config' => $config, 'consign_owner_name' => $consignData['owner_name']));
			}
			
			$html = $viewRender->render($htmlViewPart);	
			
			return $this->getResponse()->setContent($html);
			
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}	
	}
	
	public function saveconsignAction(){
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$objConsignTable = $sm->get('Inventory\Model\ConsignTable');
				
				if(!empty($posts['id'])){
					$posts['updated_date'] = date('Y-m-d H:i:s');
					$posts['updated_by'] = $identity['user_id'];
				}else{
					$posts['created_date'] = date('Y-m-d H:i:s');
					$posts['created_by'] = $identity['user_id'];
				}
				$jewel_type = $posts['jewel_type'];
				if($jewel_type == "diamond"){
					$posts['diamond_id'] = $posts['item_id'];
				} else if($jewel_type == "weddingring"){
					$posts['weddingring_id'] = $posts['item_id'];
				} else if($jewel_type == "engagementring"){
					$posts['engagementring_id'] = $posts['item_id'];
				} else if($jewel_type == "earring"){
					$posts['earring_id'] = $posts['item_id'];
				} else if($jewel_type == "pendant"){
					$posts['pendant_id'] = $posts['item_id'];
				} else if($jewel_type == "miscellaneous"){
					$posts['miscellaneous_id'] = $posts['item_id'];
				} else if($jewel_type == "chain"){
					$posts['chain_id'] = $posts['item_id'];
				} else if($jewel_type == "job"){
					$posts['job_id'] = $posts['item_id'];
				}
						
				unset($posts['item_id']);
				unset($posts['jewel_type']);
				unset($posts['edit_mode']);
				
				$posts['inventory_status_reason_id'] = $posts['inventory_status_reason'];
				unset($posts['inventory_status_reason']);
				
				$posts['inventory_type_id'] = $posts['inventory_type'];
				unset($posts['inventory_type']);
				
				$posts['inventory_tracking_status_id'] = $posts['inventory_tracking_status'];
				unset($posts['inventory_tracking_status']);
				
				$posts['inventory_tracking_reason_id'] = $posts['inventory_tracking_reason'];
				unset($posts['inventory_tracking_reason']);
				
				unset($posts['accept']);
				unset($posts['password']);
				if($posts['reserve_time'] != ''){
					list($date, $time) = explode(' ', $posts['reserve_time']);
					list($d, $m, $y) = explode('/', $date);
					list($hr, $min) = explode(':', $time);
					$posts['reserve_time'] = date('Y-m-d H:i:s', mktime($hr, $min, 0, $m, $d, $y));
					
				}
				foreach($posts as $key => $value){
					if(empty($value))
						unset($posts[$key]);
				}
				
				echo $objConsignTable->saveConsign($posts, $jewel_type);
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function validateownerAction(){
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$objConsignTable = $sm->get('Inventory\Model\ConsignTable');
				
				echo $objConsignTable->validateOwner($posts['user_id'], $posts['password']);
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}	
	}
	
	public function getconsigndetailsAction(){
		try{
			$sm = $this->getServiceLocator();
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$objConsignTable = $this->getServiceLocator()->get('Inventory\Model\ConsignTable');
				$ConsignData = (array)$objConsignTable->fetchConsignData($posts['id'], $posts['type']);
				
				$ConsignData['reserve_time'] = (isset($ConsignData['reserve_time']) && !empty($ConsignData['reserve_time'])) ? date($config['phpDateTimeFormat'], strtotime($ConsignData['reserve_time'])) : null;
				
				echo json_encode($ConsignData); exit;
			}
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function diamonddetailsAction(){
		try{
			// Write your code here
			$config = $this->getServiceLocator()->get('Config');
			$id = $this->params('id');
			$additionalLookup = array();
			$DiamondTable = $this->getServiceLocator()->get('Inventory\Model\DiamondTable');
			$DiamondData = (array)$DiamondTable->fetchDiamondDetails($id);
			
			$objLookupTable = $this->getServiceLocator()->get('Inventory\Model\LookupTable');
			$ConsignTable = $this->getServiceLocator()->get('Inventory\Model\ConsignTable');
			$ConsignData = (array)$ConsignTable->fetchConsignData($id, 'diamond');
			$ConsignData['reserve_time'] = (isset($ConsignData['reserve_time']) && !empty($ConsignData['reserve_time'])) ? date($config['phpDateTimeFormat'], strtotime($ConsignData['reserve_time'])) : null;
			
			return array('recordsPerPage' => $config['recordsPerPage'], 'viewData' => $DiamondData, 'ConsignData' => $ConsignData, 'fetchDiamonTypesOptions' => $objLookupTable->fetchDiamonTypesOptions(), 'fetchColorOptions' => $objLookupTable->fetchColorOptions(), 'fetchShapeOptions' => $objLookupTable->fetchShapeOptions(), 'fetchPolishOptions' => $objLookupTable->fetchPolishOptions(), 'fetchSymmetryOptions' => $objLookupTable->fetchSymmetryOptions(), 'fetchLabOptions' => $objLookupTable->fetchLabOptions(), 'fetchIntensityOptions' => $objLookupTable->fetchIntensityOptions(), 'fetchOvertoneOptions' => $objLookupTable->fetchOvertoneOptions(), 'fetchClarityOptions' => $objLookupTable->fetchClarityOptions());
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deletediamondAction(){
		try{
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$DiamondTable = $this->getServiceLocator()->get('Inventory\Model\DiamondTable');
				$data = $DiamondTable->deleteDiamond($posts['id']);
				echo $data; exit;
			}
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function uploadmultiplefileAction(){
		try{
			// Write your code here
			$config = $this->getServiceLocator()->get('Config');
			
			$request = $this->getRequest();
			if($request->isPost()){
				$imageFileName = array();
				if(count($_FILES['multipleimages']['tmp_name']) > 0){
					foreach($_FILES['multipleimages']['tmp_name'] as $imgKey => $imgValue){
						if(is_uploaded_file($imgValue)){
							$imageFileName[] = \De\Service\CommonService::uploadFile(array('tmp_name' => $imgValue, 'name' => $_FILES['multipleimages']['name'][$imgKey]), 'milestone_attachments', $config, array('gif', 'png', 'jpeg', 'jpg'));
						}
					}
				} else if(count($_FILES['multipleattachments']['tmp_name']) > 0){
					foreach($_FILES['multipleattachments']['tmp_name'] as $fileKey => $fileValue){
						if(is_uploaded_file($fileValue)){
							$imageFileName[] = \De\Service\CommonService::uploadFile(array('tmp_name' => $fileValue, 'name' => $_FILES['multipleattachments']['name'][$fileKey]), 'milestone_attachments', $config, array('gif', 'png', 'jpeg', 'jpg', 'doc', 'docx'));
						}
					}
				}
				echo json_encode($imageFileName);
			}
			
			exit;
			
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function unlinkfileAction(){
		try{
			// Write your code here
			$config = $this->getServiceLocator()->get('Config');
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$file = $config['documentRoot'].$posts['fileFullData'];
				if (!unlink($file)){
				  echo 0;
				} else {
					if(isset($posts['imgId'])){
						$CADdesignTable = $this->getServiceLocator()->get('Order\Model\CaddesignTable');
						$CADdesignTable->removeCADMediaFile($posts['imgId']);
					}					
				  echo 1;
				}
				
			}
			
			exit;
			
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}
