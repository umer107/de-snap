<?php

/**
 * This controller manage all order related functionalities
 */

namespace Order\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
	/**
	 * List all the orders, landing action or order module
	 */
    public function indexAction()
    {
		try{
			// Write your code here
			
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$newOrderForm = $sm->get('Order\Form\OrderForm');
			
			return array('recordsPerPage' => $config['recordsPerPage'], 'newOrderForm' => $newOrderForm, 'identity' => $identity, 'config' => $config);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	/**
	 * Store order data in database
	 */
    public function createorderAction()
    {
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$objOrderTable = $sm->get('Order\Model\OrderTable');
				
				foreach($posts as $key => $value){
					if(empty($value))
						unset($posts[$key]);
				}
				
				list($d, $m, $y) = explode('/', $posts['exp_delivery_date']);
				$posts['exp_delivery_date'] = "$y-$m-$d";
				
				if(empty($posts['id'])){
					$posts['created_date'] = date('Y-m-d H:i:s');
					$posts['created_by'] = $identity['user_id'];
				}else{
					$posts['updated_date'] = date('Y-m-d H:i:s');
					$posts['updated_by'] = $identity['user_id'];
				}
				unset($posts['opp_name']);
				
				echo $objOrderTable->createOrder($posts);
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	/**
	 * ajaxinvoicelookupAction() - fetch invoices and return data in json format
	 */
	public function ajaxinvoicelookupAction()
	{
    	try{
			// Write your code here
						
			$sm = $this->getServiceLocator();
			$config = $sm->get('Config');
			
    		$params = $this->getRequest()->getQuery()->toArray();
			
			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
			$keyword = $params['keyword'];
			$sortdatafield = $params['sortdatafield'];
			$sortorder = $params['sortorder'];
			
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			if(!empty($keyword)){
				$offset = 0;
			}
						
			$xero = new \Invoice\Model\Xero($sm);
			$invoices = $xero->fetchAllInvoices($limit, $offset, $keyword, $sortdatafield, $sortorder);
			
			foreach($invoices['Rows'] as $key => $invoice){
				$invoices['Rows'][$key]['created_date'] = date($config['phpDateFormat'], strtotime($invoice['created_date']));
			}
			
    		echo json_encode($invoices);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
	
	/**
	 * Fetch orders and echo json data. This actio use for ajax call.
	 */
	public function ajaxorderlistAction(){
		try{
			$sm = $this->getServiceLocator();
			$config = $sm->get('Config');
			
    		$params = $this->getRequest()->getQuery()->toArray();
			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
			$keyword = $params['keyword'];
			$cust_id = $params['cust_id'];
			
			$sortdatafield = $params['sortdatafield'];
			$sortorder = $params['sortorder'];
			
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			$orderTable = $this->getServiceLocator()->get('Order\Model\OrderTable');
			if(!empty($keyword)){
				$offset = 0;
			}
			
			$request = $this->getRequest();
			$posts = $request->getPost()->toArray();
			
			$xero = new \Invoice\Model\Xero($sm);
			$object = $xero->getAllInvoicesFromWebSerice();
			
			$ordersArr = $orderTable->fetchAll($limit, $offset, $keyword, $cust_id, $sortdatafield, $sortorder);
			foreach($ordersArr['Rows'] as $key => $value){
				foreach($value as $field => $fieldValue){
					if($field == 'created_date'){
						$ordersArr['Rows'][$key][$field] = date($config['phpDateFormat'], strtotime($ordersArr['Rows'][$key]['created_date']));
					}
					if(empty($fieldValue)){
						$ordersArr['Rows'][$key][$field] = '-';
					}
					
					if($field == 'invoice_number'){
						foreach($object->Invoices->Invoice as $invoice){
							$invoice = (array)$invoice;
							if($invoice['InvoiceNumber'] == $fieldValue){
								$ordersArr['Rows'][$key]['value'] = $invoice['Total'];
								$ordersArr['Rows'][$key]['payment_made'] = $invoice['AmountCredited'] * 100 / $invoice['Total'].'%';
							}
						}
					}
				}
			}
			echo json_encode($ordersArr);
			exit;		
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		} 
	}
	
	/**
	 * This action fetch order details, Jobs created for the order and pass it to the view
	 * Order id need to be passed in url
	 */
	public function orderdetailsAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$id = $this->params('id');
			
			$order_id = \De\Service\CommonService::generateStockCode($id, 'order');
			
			$newJobForm = $sm->get('Order\Form\JobForm');
			
			$orderTable = $this->getServiceLocator()->get('Order\Model\OrderTable');
			$order = (array)$orderTable->fetchOrderDetails($id);
			
			$order['exp_delivery_date'] = (isset($order['exp_delivery_date']) && !empty($order['exp_delivery_date'])) ? date($config['phpDateFormat'], strtotime($order['exp_delivery_date'])) : null;
			$order['created_date'] = (isset($order['created_date']) && !empty($order['created_date'])) ? date($config['phpDateFormat'], strtotime($order['created_date'])) : null;
			
			$objInvoice = $sm->get('Invoice\Model\InvoiceTable');
			$invoiceItems = $objInvoice->fetchInvoiceItems($order['invoice_number']);
			
			$partnerData = array('fullname' => $order['partner_name'], 'email' => $order['part_email'], 'mobile' => $order['part_mobile']);
			
			return array('recordsPerPage' => $config['recordsPerPage'], 'newJobForm' => $newJobForm, 'identity' => $identity,
						 'order_id' => $order_id, 'order' => $order, 'invoice_items' => $invoiceItems, 'partnerData' => $partnerData,
						 'config' => $config);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Action to store job packet data in db
	 */
	public function createjobpacketAction(){
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$objJobPacketTable = $sm->get('Order\Model\JobPacketTable');
				
				foreach($posts as $key => $value){
					if($key == 'milestones'){
						$posts['milestones'] = implode(',', $value);
					}elseif($key == 'items'){
						$posts['items'] = implode(',', $value);
					}elseif($key == 'exp_delivery_date'){
						list($d, $m, $y) = explode('/', $value);
						$posts['exp_delivery_date'] = "$y-$m-$d";
					}
				}
				$posts['created_date'] = date('Y-m-d H:i:s');
				$posts['created_by'] = $identity['user_id'];
				
				echo $objJobPacketTable->createJobPacket($posts);
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch orders and echo json data. This actio use for ajax call.
	 */
	public function ajaxjoblistAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
    		$params = $this->getRequest()->getQuery()->toArray();
			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
			$keyword = $params['keyword'];
			$order_id = $params['order_id'];
			$cust_id = $params['cust_id'];
			
			$sortdatafield = $params['sortdatafield'];
			$sortorder = $params['sortorder'];
			
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			$objJobPacketTable = $sm->get('Order\Model\JobPacketTable');
						
			$request = $this->getRequest();
			$posts = $request->getPost()->toArray();
						
			$jobsArr = $objJobPacketTable->fetchAll($limit, $offset, $sortdatafield, $sortorder, $order_id, $cust_id);
			
			foreach($jobsArr['Rows'] as $key => $value){
				foreach($value as $k => $v){
					if($k == 'exp_delivery_date'){
						$jobsArr['Rows'][$key][$k] = date($config['phpDateFormat'], strtotime($v));
					}
					
					if(empty($v)){
						$jobsArr['Rows'][$key][$k] = '-';
					}
				}
				$milestones = explode(',', $value['milestones']);
				$milestones_completed = explode(',', $value['milestones_completed']);
				$jobsArr['Rows'][$key]['milestone_progress'] = count($milestones_completed) . ' of ' . count($milestones);
				
				$arr = array();
				foreach($milestones as $milestone){
					$arr[] = $config['milestones'][$milestone];
				}
				$jobsArr['Rows'][$key]['milestones_str'] = implode(', ', $arr);
				
				$arr = array();
				foreach($milestones_completed as $milestone){
					$arr[] = $config['milestones'][$milestone];
				}
				$jobsArr['Rows'][$key]['milestones_completed_str'] = implode(', ', $arr);
			}
			
			echo json_encode($jobsArr);
			exit;		
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		} 
	}
	
	/**
	 * Edit order (until a job is created)
	 */
	 
	public function editorderformAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			
			$viewRender = $sm->get('ViewRenderer');
			$htmlViewPart = new ViewModel();
			
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$order_id = $posts['order_id'];
				
				$orderTable = $sm->get('Order\Model\OrderTable');
				$order = (array)$orderTable->fetchOrderDetails($order_id);
				$order['exp_delivery_date'] = date($config['formDateFormat'], strtotime($order['exp_delivery_date']));
				$orderAttachments = (array)$orderTable->fetchOrderAttachments($order_id);
				if(count($orderAttachments) > 0){
					$order['order_attachment'] = json_encode($orderAttachments);
				}
				$orderFullAttachments = (array)$orderTable->fetchOrderAttachments($order_id, true);
				$newOrderForm = $sm->get('Order\Form\OrderForm');				
				$newOrderForm->setData($order);
				
				$htmlViewPart->setTemplate('order/index/neworder')
							 ->setTerminal(true)
							 ->setVariables(array('newOrderForm' => $newOrderForm, 'orderFullAttachments' => $orderFullAttachments));
			}
		
			$html = $viewRender->render($htmlViewPart);	
			
			return $this->getResponse()->setContent($html);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Delete order action
	 */
	 public function deleteorderAction(){
	 	try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$order_id = $posts['order_id'];
				
				$orderTable = $sm->get('Order\Model\OrderTable');
				if($orderTable->deleteOrder($order_id)){
					echo 1;
				}
			}
		
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	
	/**
	 * Show job details / milestone management
	 */
	public function jobdetailsAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$job_id = $this->params('id');
			$actual_job_id = $this->params('id');
			
			$orderTable = $sm->get('Order\Model\OrderTable');
			$order = (array)$orderTable->fetchOrderDetailsByJobId($job_id);
			$order['job_count'] = 1;
			
			$jobDetails = (array)$orderTable->fetchJobDetails($job_id);
			
			$order['exp_delivery_date'] = (isset($order['exp_delivery_date']) && !empty($order['exp_delivery_date'])) ? date($config['phpDateFormat'], strtotime($order['exp_delivery_date'])) : null;
			$order['created_date'] = (isset($order['created_date']) && !empty($order['created_date'])) ? date($config['phpDateFormat'], strtotime($order['created_date'])) : null;
			
			// Setting job count to 1 to hide edit and delete job button 
			
			$order_id = \De\Service\CommonService::generateStockCode($order['id'], 'order');
			$job_id = \De\Service\CommonService::generateStockCode($job_id, 'order');
			
			$partnerData = array('fullname' => $order['partner_name'], 'email' => $order['part_email'], 'mobile' => $order['part_mobile']);
			
			$objInvoice = $sm->get('Invoice\Model\InvoiceTable');
			$invoiceItems = $objInvoice->fetchInvoiceItems($order['invoice_number'], $order['items']);
			
			// Cad Milestone data
			$objCaddesign = $sm->get('Order\Model\CaddesignTable');
			$cadMilestoneData = $objCaddesign->getCaddesignDataByJobId($actual_job_id);
			$cadMilestoneMediaFiles = $objCaddesign->fetchCADMediaFiles($actual_job_id);
			
			// Prototype Milestone data
			$prototypeTable = $sm->get('Order\Model\PrototypeTable');
			$prototype = (array)$prototypeTable->fetchPrototype($jobDetails['job_id']);
			$prototype['date_delivered'] = empty($prototype['date_delivered']) ? '' : date($config['formDateFormat'], strtotime($prototype['date_delivered']));
			$prototype['exp_delivery_date'] = empty($prototype['exp_delivery_date']) ? '' : date($config['formDateFormat'], strtotime($prototype['exp_delivery_date']));
			
			$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
			$prototypeAttachments = $jobPacketTable->fetchMilestoneImages($prototype['id'], 2);
			
			// Cast Milestone data
			$castTable = $sm->get('Order\Model\CastTable');
			$cast = (array)$castTable->fetchCast($jobDetails['job_id']);
			$cast['date_delivered'] = empty($cast['date_delivered']) ? '' : date($config['formDateFormat'], strtotime($cast['date_delivered']));
			$cast['exp_delivery_date'] = empty($cast['exp_delivery_date']) ? '' : date($config['formDateFormat'], strtotime($cast['exp_delivery_date']));
			
			$castAttachments = $jobPacketTable->fetchMilestoneImages($cast['id'], 3);
			
			$lookupTable = $this->getServiceLocator()->get('Inventory\Model\LookupTable');
			$metalTypes = $lookupTable->fetchMetalTypeOptions();
			
			$jobTypes = $lookupTable->fetchWorkshopJobTypeOptions();
			
			$reviewedByUser = $lookupTable->fetchConsignOwnerOptions();
			
			// Workshop Milestone data
			$workshopTable = $sm->get('Order\Model\WorkshopTable');
			$workshop = (array)$workshopTable->fetchWorkshop($jobDetails['job_id'], 1)->current();
			
			$suppliersToWorkshop = $workshopTable->fetchWorkshopSuppliers($jobDetails['job_id'], $workshop['id']);
			foreach($suppliersToWorkshop as $key => $value){
				$suppliersToWorkshop[$key]['tasks'] = unserialize($suppliersToWorkshop[$key]['tasks']);
			}
			
			$ConsignTable = $this->getServiceLocator()->get('Inventory\Model\ConsignTable');
			$ConsignData = (array)$ConsignTable->fetchConsignData($actual_job_id, 'job');
			
			return array('recordsPerPage' => $config['recordsPerPage'], 'identity' => $identity, 'order_id' => $order_id, 'job_id' => $job_id,
						 'order' => $order, 'jobDetails' => $jobDetails, 'invoice_items' => $invoiceItems, 'partnerData' => $partnerData, 'config' => $config,
						 'cadMilestoneData' => $cadMilestoneData, 'prototype' => $prototype, 'cast' => $cast, 'metalTypes' => $metalTypes, 'jobTypes' => $jobTypes,
						 'cadMilestoneMediaFiles' => $cadMilestoneMediaFiles, 'reviewedByUser' => $reviewedByUser, 'workshop' => $workshop,
						 'suppliersToWorkshop' => $suppliersToWorkshop, 'prototypeAttachments' => $prototypeAttachments, 'castAttachments' => $castAttachments,
						 'ConsignData' => $ConsignData, 'jobPacketTable' => $jobPacketTable);
		
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Change status in job packet table to 1, to mark as started
	 */
	public function startjobAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			if($request->isPost()){
			
				$posts = $request->getPost()->toArray();
			
				/***********/
				if(empty($posts['invoice_number'])){
					$data = array('status' => 1);
					$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
					echo $response = $jobPacketTable->startJob($posts['start_job_id'], $data);
				}else{
					$xero = new \Invoice\Model\Xero($sm);
					$invoice = $xero->getInvoiceById($posts['invoice_number']);
					/*echo '<pre>';print_r($invoice->Invoices->Invoice->Total);exit;
					echo '<pre>';print_r($invoice->Invoices->Invoice->AmountDue);exit;
					echo '<pre>';print_r($invoice->Invoices->Invoice->AmountPaid);exit;*/
					
					$paymentMade = ($invoice->Invoices->Invoice->AmountPaid * 100) / $invoice->Invoices->Invoice->Total;
					
					if($paymentMade >= 40){
						$data = array('status' => 1);
					
						$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
						echo $response = $jobPacketTable->startJob($posts['start_job_id'], $data);
					}else{
						echo 2;
					}
				}
				exit;
				/***********/
			
				
				/*$data = array('status' => 1, 'comment' => $posts['start_comment']);
				
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
				echo $response = $jobPacketTable->startJob($posts['start_job_id'], $data);*/
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Store cad design data againest job in database
	 */
    public function savecaddesignAction()
    {
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$objCaddesignTable = $sm->get('Order\Model\CaddesignTable');
				
				foreach($posts as $key => $value){
					if(empty($value))
						unset($posts[$key]);
				}
				if($posts['exp_delivery_date'] != ''){
					list($d, $m, $y) = explode('/', $posts['exp_delivery_date']);
					$posts['exp_delivery_date'] = "$y-$m-$d";
				}
				if($posts['stp2_delivery_date'] != ''){
					list($d, $m, $y) = explode('/', $posts['stp2_delivery_date']);
					$posts['stp2_delivery_date'] = "$y-$m-$d";
				}
				
				if($posts['caddesign_stage'] == 1){
					if($posts['priority'] == "on"){
						$posts['priority'] = 1;
					} else {
						$posts['priority'] = 0;
					}
					$posts['steps_completed'] = 1;
					$posts['created_date'] = date('Y-m-d H:i:s');
					$posts['created_by'] = $identity['user_id'];
				} else if($posts['caddesign_stage'] == 2){
					$posts['steps_completed'] = 2;
					$posts['modified_date'] = date('Y-m-d H:i:s');
					$posts['modified_by'] = $identity['user_id'];
				} else if($posts['caddesign_stage'] == 3){
					$posts['steps_completed'] = 3;
					$posts['modified_date'] = date('Y-m-d H:i:s');
					$posts['modified_by'] = $identity['user_id'];
				}
				echo $objCaddesignTable->saveCADdesign($posts);
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	/**
	 * Stores 1st step of prototype milestone
	 */
	public function prototypestep1Action(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				
				$orderTable = $sm->get('Order\Model\OrderTable');
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
				
				if($jobPacketTable->checkMilestoneCanBeCompleted($posts['job_id'], 2)){
				
					$order = (array)$orderTable->fetchJobDetails($posts['job_id']);
					
					unset($posts['metal_type_opt']);
					
					$posts['metal_types'] = implode(',', $posts['metal_types']);
					
					$posts['steps_completed'] = $posts['step'];
					unset($posts['step']);
					unset($posts['supplier_name']);
					
					$attachments = empty($posts['multipleattachmentsHidden']) ? null : json_decode($posts['multipleattachmentsHidden']);
					unset($posts['multipleattachmentsHidden']);
					
					list($d, $m, $y) = explode('/', $posts['exp_delivery_date']);
					$posts['exp_delivery_date'] = "$y-$m-$d";
					
					list($d, $m, $y) = explode('/', $posts['date_delivered']);
					$posts['date_delivered'] = "$y-$m-$d";
					
					$prototypetTable = $sm->get('Order\Model\PrototypeTable');
					if($response = $prototypetTable->savePrototypeStep($posts)){
						
						$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
						
						foreach($attachments as $value){
							$data = array('milestone_type' => 2, 'milestones_ref_id' => $response, 'step' => 1, 'image' => $value);
							$jobPacketTable->saveMilestoneImages($data);
						}
						
						echo $response;
					}else{
						echo 0;
					}					
				}else{
					echo 0;
				}
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Stores 2nd step of prototype milestone
	 */
	public function prototypestep2Action(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				
				$prototypetTable = $sm->get('Order\Model\PrototypeTable');
				$prototype = (array)$prototypetTable->fetchPrototype($posts['job_id']);
				
				if(empty($prototype)){
					echo 0;
					exit;
				}
				$data = array('id' => $prototype['id'], 'steps_completed' => 2, 'stp2_client_reviewed' => 1);
				
				if($prototypetTable->savePrototypeStep($data)){
					$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
					echo $jobPacketTable->completeMilestone($posts['job_id'], 2);
				}
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * get CAD Stage
	 */
	public function getcaddesignstageAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$objCaddesign = $sm->get('Order\Model\CaddesignTable');
				$cadMilestoneData = $objCaddesign->getCaddesignDataByJobId($posts['job_id']);
				if($cadMilestoneData->steps_completed){
					echo $cadMilestoneData->steps_completed;
				} else {
					echo 0;
				}
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Stores 1st step of cast milestone
	 */
	public function caststep1Action(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
				
				if($jobPacketTable->checkMilestoneCanBeCompleted($posts['job_id'], 3)){
				
					unset($posts['metal_type_opt']);
					
					$posts['metal_types'] = implode(',', $posts['metal_types']);
					
					$posts['steps_completed'] = $posts['step'];
					unset($posts['step']);
					unset($posts['supplier_name']);
					
					$attachments = empty($posts['multipleattachmentsHidden']) ? null : json_decode($posts['multipleattachmentsHidden']);
					unset($posts['multipleattachmentsHidden']);
					
					list($d, $m, $y) = explode('/', $posts['exp_delivery_date']);
					$posts['exp_delivery_date'] = "$y-$m-$d";
					
					list($d, $m, $y) = explode('/', $posts['date_delivered']);
					$posts['date_delivered'] = "$y-$m-$d";
					
					$castTable = $sm->get('Order\Model\CastTable');
					if($response = $castTable->saveCastStep($posts)){
						
						$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
						
						foreach($attachments as $value){
							$data = array('milestone_type' => 3, 'milestones_ref_id' => $response, 'step' => 1, 'image' => $value);
							$jobPacketTable->saveMilestoneImages($data);
						}
						
						echo $response;
					}else{
						echo 0;
					}
					
				}else{
					echo 0;
				}
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Stores 2nd step of cast milestone
	 */
	public function caststep2Action(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				
				$castTable = $sm->get('Order\Model\CastTable');
				$cast = (array)$castTable->fetchCast($posts['job_id']);
				if(empty($cast)){
					echo 0;
					exit;
				}
								
				$data = array('id' => $cast['id'], 'steps_completed' => 2, 'stp2_client_reviewed' => 1);
				
				if($castTable->saveCastStep($data)){
					$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
					echo $jobPacketTable->completeMilestone($posts['job_id'], 3);
				}
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

	/**
	 * Stores 1st step of workshop milestone
	 */	
	public function workshopstep1Action(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
				
				if($jobPacketTable->checkMilestoneCanBeCompleted($posts['job_id'], 4)){
				
					$workshopTable = $this->getServiceLocator()->get('Order\Model\WorkshopTable');
					$workshopData = array('job_id' => $posts['job_id'], 'steps_completed' => 1);
					$id = $workshopTable->saveWorkshopStep($workshopData);
					
					if($id){
						$data = array('milestone_type' => 4, 'step' => 1, 'milestones_ref_id' => $id, 'image' => $posts['workshop_production_line_image_data']);
						echo $jobPacketTable->saveMilestoneImages($data);					
					}
				}else{
					echo 0;
				}
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Stores suppliers step of workshop milestone
	 */
	public function savesuppliertaskAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				
				foreach($posts['job_type_id'] as $value){
					$serializedData[$value] = array(
						'cost' => $posts['cost_'.$value],
						'taskinfo' => $posts['taskinfo_'.$value],
					);
				}
				list($d, $m, $y) = explode('/', $posts['exp_delivery_date']);
				$exp_delivery_date = "$y-$m-$d";
				
				$workshopTable = $sm->get('Order\Model\WorkshopTable');
				
				$workshop = $workshopTable->fetchWorkshop($posts['job_id'], 1)->current();
								
				if(empty($workshop)){
					echo 0;
					exit;
				}
				
				$data = array(
					'job_id' => $posts['job_id'],
					'milestone_id' => $workshop->id,
					'supplier_id' => $posts['supplier_id'],
					'exp_delivery_date' => $exp_delivery_date,
					'tasks' => serialize($serializedData)
				);
				
				if($workshopTable->saveSupplierData($data)){;
					echo $workshopTable->increaseWorkshopStepCount($posts['job_id']);
				}				
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}	
	}
	
	/**
	 * Save quality control step
	 */
	 
	 public function workshopqualitycontrolAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				
				$workshopTable = $sm->get('Order\Model\WorkshopTable');
				$workshop = $workshopTable->fetchWorkshop($posts['job_id'], 1)->current();
				
				if(empty($workshop) || !empty($workshop->qa_reviewed_by)){
					echo 0;
					exit;
				}
				
				$data = array('id' => $workshop->id, 'qa_reviewed_by' => $posts['qa_reviewed_by'], 'steps_completed' => new \Zend\Db\Sql\Expression('steps_completed + 1'));
				echo $workshopTable->saveWorkshopStep($data);
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	/**
	 * Save quality control step
	 */
	 
	 public function workshopfinalstepAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				
				$workshopTable = $sm->get('Order\Model\WorkshopTable');
				$workshop = $workshopTable->fetchWorkshop($posts['job_id'], 1)->current();
				
				if(empty($workshop) || empty($workshop->qa_reviewed_by) || !empty($workshop->client_reviewed)){
					echo 0;
					exit;
				}
				
				$data = array('id' => $workshop->id, 'client_reviewed' => 1, 'steps_completed' => new \Zend\Db\Sql\Expression('steps_completed + 1'));
								
				if($workshopTable->saveWorkshopStep($data)){
					$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
					echo $jobPacketTable->completeMilestone($posts['job_id'], 4);
				}
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

	/**
	 * Send request email to the admin to start the job
	 */
	public function startjobrequestAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
				$approval_code = time().rand(99999, 99999999);
				if($response = $jobPacketTable->startJob($posts['start_job_id'], array('approval_code' => $approval_code, 'status' => 3))){
				
					$orderTable = $sm->get('Order\Model\OrderTable');
				
					$jobDetails = (array)$orderTable->fetchJobDetails($posts['start_job_id']);
					
					$jobDetails['approval_code'] = $approval_code;
					$jobDetails['display_job_id'] = \De\Service\CommonService::generateStockCode($jobDetails['job_id'], 'order');
					
					$milestones = explode(',', $jobDetails['milestones']);
					$milestonesNames = array();
					foreach($milestones as $value){
						$milestonesNames[] = $config['milestones'][$value];
					}
					$jobDetails['milestones'] = implode(', ', $milestonesNames);
					
					$emailParams['toEmail'] = 'admin@openseed.com.au';
					$emailParams['toName'] = 'Michael';
					$emailParams['subject'] = 'Approval needed to start job';
					$emailParams['message'] = $jobDetails;
					$emailParams['template'] = 'approve-job-to-start.phtml';
					
					\De\Service\EmailService::sendEmail($config['smtp_details'], $emailParams);
				
					echo 3;
				}
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Approve a job
	 */
	public function approvejobAction(){
		try{
			$sm = $this->getServiceLocator();
			$config = $sm->get('Config');
			$request = $this->getRequest();
			
			$job_id = $this->params('job_id');
			$approval_code = $this->params('approval_code');
			
			$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
			$count = $jobPacketTable->validateApprovalCode($job_id, $approval_code);
			
			if($count == 1){
				$data = array('status' => 1);
				$response = $jobPacketTable->startJob($job_id, $data);
				header('location: /jobdetails/'.$job_id);
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Delete order action
	 */
	 public function deletejobAction(){
	 	try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			
			if($request->isPost()){
			
				$posts = $request->getPost()->toArray();
				$job_id = $posts['job_id'];
				
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
				echo $jobPacketTable->deleteJobPacket($job_id);
			}
		
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
}
