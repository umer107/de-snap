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

class ChainController extends AbstractActionController
{
    public function indexAction()
    {
		try{
			// Write your code here
			
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $this->getServiceLocator()->get('Config');
			$objChainTable = $this->getServiceLocator()->get('Inventory\Model\ChainTable');
			$columnList = $objChainTable->listColumns('chain');
			$gridViewTable = $this->getServiceLocator()->get('Customer\Model\GridViewTable');
			$gridViewOptions = $gridViewTable->getGridViews($identity['user_id'], 'chain');
			$inventoryForm = $this->getServiceLocator()->get('Inventory\Form\ChainInventoryForm');
			$consignForm = $this->getServiceLocator()->get('Inventory\Form\ConsignInventoryForm');
			return array('recordsPerPage' => $config['recordsPerPage'], 'columnList' => $columnList,
						 'inventoryForm' => $inventoryForm, 'consignForm' => $consignForm,
						 'gridViewOptions' => $gridViewOptions, 'loginUserId' => $identity['user_id'], 'identity' => $identity);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
    public function savechainAction()
    {
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				unset($posts['supplier_name']);
				$objChainTable = $sm->get('Inventory\Model\ChainTable');
				if(isset($posts['chainRingId'])){
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
				
				echo $objChainTable->saveChain($posts);
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	public function ajaxgetchainAction(){
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
			$chainTable = $this->getServiceLocator()->get('Inventory\Model\ChainTable');
			if($keyword != ''){
				$offset = 0;
			}
			$chainArr = $chainTable->fetchAll($limit, $offset, $keyword, $sortdatafield, $sortorder);
			foreach($chainArr['Rows'] as $key => $value){
				foreach($value as $field => $fieldValue){
					if($field == 'created_date'){
						$chainArr['Rows'][$key][$field] = date($config['phpDateFormat'], strtotime($chainArr['Rows'][$key]['created_date']));
					}elseif($field == 'reserve_time' && !empty($chainArr['Rows'][$key]['reserve_time'])){
						$chainArr['Rows'][$key][$field] = date($config['phpDateTimeFormat'], strtotime($chainArr['Rows'][$key]['reserve_time']));
					}
					if(empty($fieldValue)){
						$chainArr['Rows'][$key][$field] = '-';
					}
				}
			}
			echo json_encode($chainArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
	
	public function chaindetailsAction(){
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$canDelete = $sm->get('ControllerPluginManager')->get('AuthPlugin')->checkResource($sm->get('AuthService'), 'Inventory\Controller\Chain::deletechain');

			$config = $sm->get('Config');
			$id = $this->params('id');
			$additionalLookup = array();
			$ChainTable = $sm->get('Inventory\Model\ChainTable');
			$ChainData = (array)$ChainTable->fetchChainDetails($id);
			$objLookupTable = $sm->get('Inventory\Model\LookupTable');
			$ConsignTable = $sm->get('Inventory\Model\ConsignTable');
			$ConsignData = (array)$ConsignTable->fetchConsignData($id, 'chain');
			$ConsignData['reserve_time'] = (isset($ConsignData['reserve_time']) && !empty($ConsignData['reserve_time'])) ? date($config['phpDateTimeFormat'], strtotime($ConsignData['reserve_time'])) : null;
			return array(
					'recordsPerPage' => $config['recordsPerPage'], 'viewData' => $ChainData, 'fetchChainStyleOptions' => $objLookupTable->fetchChainStyleOptions(), 'metalTypesLookup' => $objLookupTable->fetchMetalTypeOptions(),'ConsignData' => $ConsignData,
					'canDelete' => $canDelete
			);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deletechainAction(){
		try{
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$ChainTable = $this->getServiceLocator()->get('Inventory\Model\ChainTable');
				$data = $ChainTable->deleteChain($posts['id']);
				echo $data; exit;
			}
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}
