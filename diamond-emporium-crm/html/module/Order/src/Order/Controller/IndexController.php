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
			
			$invoice_id = $this->params('invoice_id');
			
			$newOrderForm = $sm->get('Order\Form\OrderForm');
			
			if($invoice_id){
				$invoiceTable = $sm->get('Invoice\Model\InvoiceTable');
				$invoice = $invoiceTable->getInvoiceDetailsById($invoice_id);
				
				$newOrderForm->get('cust_id')->setValue($invoice['cust_id']);
				$newOrderForm->get('opp_id')->setValue($invoice['opp_id']);
				$newOrderForm->get('opp_name')->setValue($invoice['opp_name']);
				$newOrderForm->get('invoice_number')->setValue($invoice['invoice_number']);
			}
			
			return array('recordsPerPage' => $config['recordsPerPage'], 'newOrderForm' => $newOrderForm, 'identity' => $identity, 'config' => $config, 'invoice' => $invoice);
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
				
				$posts['order_attachment'] = $posts['multipleimagesHidden'];
				unset($posts['multipleimagesHidden']);
				
				/*foreach($posts as $key => $value){
					if(empty($value))
						unset($posts[$key]);
				}*/
				
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
			$opp_id = $params['opp_id'];
			$sortdatafield = $params['sortdatafield'];
			$sortorder = $params['sortorder'];
			
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			if(!empty($keyword)){
				$offset = 0;
			}
						
			$xero = new \Invoice\Model\Xero($sm);
			$invoices = $xero->fetchAllInvoices($limit, $offset, $keyword, $sortdatafield, $sortorder, null, $opp_id);
			
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
			
			//$xero = new \Invoice\Model\Xero($sm);
			//$object = $xero->getAllInvoicesFromWebSerice();
			
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
						$ordersArr['Rows'][$key]['invoice_number'] = $value['invoice_number'];
						$ordersArr['Rows'][$key]['xero_tax_rate'] = $value['xero_tax_rate'];
						
						if($value['xero_date_due']!='0000-00-00 00:00:00')
							$ordersArr['Rows'][$key]['xero_date_due'] = date($config['phpDateFormat'], strtotime($value['xero_date_due']));
						else 
							$ordersArr['Rows'][$key]['xero_date_due'] ='';
							
						$ordersArr['Rows'][$key]['payment_made'] = $value['xero_payment_made'];
						
						$ordersArr['Rows'][$key]['value'] = $value['xero_total'];
						
						
					/*
						foreach($object->Invoices->Invoice as $invoice){
							$invoice = (array)$invoice;
							
							if($invoice['InvoiceNumber'] == $fieldValue){
								$ordersArr['Rows'][$key]['value'] = $invoice['Total'];
								$payment_made = !empty($invoice['AmountPaid']) ? number_format($invoice['AmountPaid'] * 100 / $invoice['Total'], 2) : $invoice['AmountPaid'];
								$ordersArr['Rows'][$key]['payment_made'] = $payment_made.'%';
							}
						}
					*/}
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
			$authService = $sm->get('AuthService');
			$identity = $authService->getIdentity();
			$canDelete = $sm->get('ControllerPluginManager')->get('AuthPlugin')->checkResource($authService, 'Order\Controller\Index::deleteorder');
			
			$config = $sm->get('Config');
			
			$id = $this->params('id');
			
			$order_id = \De\Service\CommonService::generateStockCode($id, 'order');
			
			$newJobForm = $sm->get('Order\Form\JobForm');
			$newJobForm->get('order_id')->setValue($id);
			
			/* TODO: read this from a variable or something */
			$defaultOwnerId = 25;
			$newJobForm->get('owner_id')->setValue($defaultOwnerId);
						
			$orderTable = $sm->get('Order\Model\OrderTable');
			$order = (array)$orderTable->fetchOrderDetails($id);
			$order['orderAttachments'] = $orderTable->fetchOrderAttachments($id);
			
			$order['exp_delivery_date'] = (isset($order['exp_delivery_date']) && !empty($order['exp_delivery_date'])) ? date($config['phpDateFormat'], strtotime($order['exp_delivery_date'])) : null;
			$order['created_date'] = (isset($order['created_date']) && !empty($order['created_date'])) ? date($config['phpDateFormat'], strtotime($order['created_date'])) : null;
			
			$objInvoice = $sm->get('Invoice\Model\InvoiceTable');
			$invoiceItems = $objInvoice->fetchInvoiceItems($order['invoice_number']);
			
			// Delete Invoice - starts
			if(!empty($order['invoice_number'])){
				$xero = new \Invoice\Model\Xero($sm);
				$invoice = $xero->getInvoiceById($order['invoice_number']);
				
				if($invoice->Invoices->Invoice->Status == 'DELETED'){
					$objInvoice = $sm->get('Invoice\Model\InvoiceTable');
					$objInvoice->detachOrderInvoice($order['invoice_number']);
					$order['invoice_number'] = null;
				}
			}			
			// Delete Invoice - ends
			
			$partnerData = array('fullname' => $order['partner_name'], 'email' => $order['part_email'], 'mobile' => $order['part_mobile']);
			
			$userTable = $sm->get('Customer\Model\UsersTable');
			$ownerOptions = $userTable->fetchUsersForTasks();
			
			return array('recordsPerPage' => $config['recordsPerPage'], 'newJobForm' => $newJobForm, 'identity' => $identity,
						 'order_id' => $order_id, 'order' => $order, 'invoice_items' => $invoiceItems, 'partnerData' => $partnerData,
						 'config' => $config, 'ownerOptions' => $ownerOptions,
						 'canDelete' => $canDelete
			);
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
					/*if($key == 'milestones'){
						$posts['milestones'] = implode(',', $value);
					}else*/if($key == 'items'){
						$posts['items'] = implode(',', $value);
					}elseif($key == 'exp_delivery_date'){
						list($d, $m, $y) = explode('/', $value);
						$posts['exp_delivery_date'] = "$y-$m-$d";
					}
				}
				
				if(empty($posts['id'])){
					$posts['created_date'] = date('Y-m-d H:i:s');
					$posts['created_by'] = $identity['user_id'];
				}else{
					$posts['updated_date'] = date('Y-m-d H:i:s');
					$posts['updated_by'] = $identity['user_id'];
				}
				
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
			$keyword = '';
			$order_id = '';
			$cust_id = '';
    		$params = $this->getRequest()->getQuery()->toArray();
			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
			if($params['keyword'] != 'undefined'){
				$keyword = $params['keyword'];
			}
			if($params['order_id'] != 'undefined'){
				$order_id = $params['order_id'];
			}
			if($params['cust_id'] != 'undefined'){
				$cust_id = $params['cust_id'];
			}
			
			$sortdatafield = $params['sortdatafield'];
			$sortorder = $params['sortorder'];
			
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			$objJobPacketTable = $sm->get('Order\Model\JobPacketTable');
			if($keyword != ''){
				$offset = 0;
			}		
			$request = $this->getRequest();
			$posts = $request->getPost()->toArray();
						
			$jobsArr = $objJobPacketTable->fetchAll($limit, $offset, $sortdatafield, $sortorder, $order_id, $cust_id, $keyword);
			
			foreach($jobsArr['Rows'] as $key => $value){
				foreach($value as $k => $v){
					if($k == 'exp_delivery_date'){
						$jobsArr['Rows'][$key][$k] = date($config['phpDateFormat'], strtotime($v));
					}
					
					if(empty($v)){
						$jobsArr['Rows'][$key][$k] = '-';
					}
				}
				
				$milestoneList = $objJobPacketTable->fetchMilestones($value['job_id']);
				
				$array_milestones = array();
				foreach($milestoneList as $milesData){
					$array_milestones[] = $milesData['milestone_type_id'];
				}
				$value['milestones'] = implode(",",$array_milestones);
				$milestones = explode(',', $value['milestones']);
				$milestones_completed = 1;
				$completedMilestones = $objJobPacketTable->getMilestonesByJobId($value['job_id']);
				/*if($completedMilestones > 0){
					$milestones_completed = $completedMilestones;
				}
				$jobsArr['Rows'][$key]['milestone_progress'] = $milestones_completed . ' of ' . count($milestones);*/

				$jobsArr['Rows'][$key]['milestone_progress'] = $completedMilestones . ' of ' . count($milestones);
				
				$arr = array();
				foreach($milestones as $milestone){
					$arr[] = $config['milestones'][$milestone];
				}
				$jobsArr['Rows'][$key]['milestones_str'] = implode(', ', $arr);
				
				$arr = array();
				foreach($milestones_completed as $milestone){
					$arr[] = $config['milestones'][$milestone];
				}
				$jobsArr['Rows'][$key]['milestones_current_status'] = '';
				$jobsArr['Rows'][$key]['milestones_completed_str'] = '';
				$jobsArr['Rows'][$key]['milestones_supplier'] = '';
				if($value['current_milestone_id'] > 0 && $value['current_milestone_step_id'] > 0){
					$jobsArr['Rows'][$key]['milestones_current_status'] = $config['milestones_steps'][$value['current_milestone_id']][$value['current_milestone_step_id']];
				}
				if($value['current_milestone_id'] > 0){
					$jobsArr['Rows'][$key]['milestones_completed_str'] = $config['milestones'][$value['current_milestone_id']];
				}
				if($value['job_packet_status'] == 0){
					$jobsArr['Rows'][$key]['milestones_activity'] = '<span class="activeBall" style=" background-color:#11d307"></span>';//Green
				} else if($value['job_packet_status'] == 1){
					$jobsArr['Rows'][$key]['milestones_activity'] = '<span class="activeBall" style=" background-color:orange"></span>';//Orange
				} else if($value['job_packet_status'] == 2){
					$jobsArr['Rows'][$key]['milestones_activity'] = '<span class="activeBall" style=" background-color:blue"></span>';//Blue
				} else if($value['job_packet_status'] == 3){
					$jobsArr['Rows'][$key]['milestones_activity'] = '<span class="activeBall" style=" background-color:#cecece"></span>';//Gray
				}
				if($value['milestone_id'] > 0 && $value['current_milestone_id'] > 0){
					$supplier = $objJobPacketTable->getSupplierNameByMilestoneId($value['milestone_id'], $value['current_milestone_id']);
					if(count($supplier) > 0){
						foreach($supplier as $dataSup){
							$jobsArr['Rows'][$key]['milestones_supplier'] = $dataSup['supplier_name'];
						}
					}
					//$jobsArr['Rows'][$key]['milestones_supplier'];
				}
				
				// Population data for costs updated - starts
				
				$workshopList = array();
				foreach($milestoneList as $milestone){
					if($milestone['milestone_type_id'] == 4)
						$workshopList[]['workshop_id'] = $milestone['id'];
				}
				
				if(empty($workshopList)){ // No Workshop milestone added
					$jobsArr['Rows'][$key]['costs_updated'] = '<span style="color:cecece; font-weight:bolder;">NA</span>';
				}else{					
					$objWorkshopTable = $sm->get('Order\Model\WorkshopTable');
					foreach($workshopList as $workshop){					
						$supplierData = $objWorkshopTable->fetchWorkshopSuppliers($workshop['workshop_id']);
						if(empty($supplierData)){ // No suppliers are added to the Workshop milestone
							$jobsArr['Rows'][$key]['costs_updated'] = '<span style="color:cecece; font-weight:bolder;">NA</span>';
						}else{
							foreach($supplierData as $data){
								if(empty($data['tasks'])){ // No Tasks are created for the suppliers in the Workshop milestone
									$jobsArr['Rows'][$key]['costs_updated'] = '<span style="color:cecece; font-weight:bolder;">NA</span>';
								}else{
									$tasks = unserialize($data['tasks']);
									foreach($tasks as $task){
										if($task['cost'] > 0){
											$jobsArr['Rows'][$key]['costs_updated'] = '<span style="color:#11d307; font-weight:bolder;">Yes</span>';
											break;
										}else{
											$jobsArr['Rows'][$key]['costs_updated'] = '<span style="color:red; font-weight:bolder;">No</span>';
										}
									}
								}
							}
						}
					}
				}
				// Population data for costs updated - ends
				
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
				$newOrderForm->get('order_save')->setLabel("Update");
				$newOrderForm->setData($order);
				
				$htmlViewPart->setTemplate('order/index/neworder')
							 ->setTerminal(true)
							 ->setVariables(array('newOrderForm' => $newOrderForm, 'orderFullAttachments' => $orderFullAttachments, 'config' => $config));
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
			$authService = $sm->get('AuthService');
			$identity = $authService->getIdentity();
			$canDelete = $sm->get('ControllerPluginManager')->get('AuthPlugin')->checkResource($authService, 'Order\Controller\Index::deletejob');
			
			$config = $sm->get('Config');
			
			$job_id = $this->params('id');
			$actual_job_id = $this->params('id');
			
			$orderTable = $sm->get('Order\Model\OrderTable');
			$order = (array)$orderTable->fetchOrderDetailsByJobId($job_id);
			// Setting job count to 1 to hide edit and delete job button 
			$order['job_count'] = 1;
			
			$order['exp_delivery_date'] = (isset($order['exp_delivery_date']) && !empty($order['exp_delivery_date'])) ? date($config['phpDateFormat'], strtotime($order['exp_delivery_date'])) : null;
			$order['created_date'] = (isset($order['created_date']) && !empty($order['created_date'])) ? date($config['phpDateFormat'], strtotime($order['created_date'])) : null;
			$order['orderAttachments'] = $orderTable->fetchOrderAttachments($order['id']);
			
			$jobDetails = (array)$orderTable->fetchJobDetails($job_id);
			
			$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
			$milestones = $jobPacketTable->fetchMilestones($job_id);
			
			$order_id = \De\Service\CommonService::generateStockCode($order['id'], 'order');
			$job_id = \De\Service\CommonService::generateStockCode($job_id, 'order');
			
			$partnerData = array('fullname' => $order['partner_name'], 'email' => $order['part_email'], 'mobile' => $order['part_mobile']);
			
			$objInvoice = $sm->get('Invoice\Model\InvoiceTable');
			$invoiceItems = $objInvoice->fetchInvoiceItems($order['invoice_number'], $order['items']);
			
			$lookupTable = $sm->get('Inventory\Model\LookupTable');
			$metalTypes = $lookupTable->fetchMetalTypeOptions();
			
			$jobTypes = $lookupTable->fetchWorkshopJobTypeOptions();
			
			$reviewedByUser = $lookupTable->fetchConsignOwnerOptions();
			
			// Workshop Milestone data
			
			$workshopTable = $sm->get('Order\Model\WorkshopTable');
			
			$ConsignTable = $sm->get('Inventory\Model\ConsignTable');
			$ConsignData = (array)$ConsignTable->fetchConsignData($actual_job_id, 'job');
			
			/*$newJobForm = $sm->get('Order\Form\JobForm');
			$newJobForm->get('id')->setValue($jobDetails['job_id']);
			$newJobForm->get('owner_id')->setValue($jobDetails['owner_id']);
			$newJobForm->get('exp_delivery_date')->setValue(date($config['formDateFormat'], strtotime($jobDetails['exp_delivery_date'])));
			$newJobForm->get('job_save')->setLabel('Update');*/
			
			return array('recordsPerPage' => $config['recordsPerPage'], 'identity' => $identity, 'order_id' => $order_id, 'job_id' => $job_id,
						 'order' => $order, 'jobDetails' => $jobDetails, 'invoice_items' => $invoiceItems, 'partnerData' => $partnerData, 'config' => $config,
						 'milestones' => $milestones, 'metalTypes' => $metalTypes, 'jobTypes' => $jobTypes, 'reviewedByUser' => $reviewedByUser,
						 'ConsignData' => $ConsignData, 'jobPacketTable' => $jobPacketTable, 'workshopTable' => $workshopTable,
						 'canDelete' => $canDelete
			);
		
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
				$paymentMade = 0;
				
				if(!empty($posts['invoice_number'])){ // If no invoice is attached then start the job
					$xero = new \Invoice\Model\Xero($sm);
					$invoice = $xero->getInvoiceById($posts['invoice_number']);
					
					$paymentMade = ($invoice->Invoices->Invoice->AmountPaid * 100) / $invoice->Invoices->Invoice->Total;
				}
				
				if(empty($posts['invoice_number']) || $paymentMade < 40){ // If payment made more than 40% then start else wait for approval					
					echo 2;
				}else{
					$data = array('status' => 1);
					
					$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
					echo $response = $jobPacketTable->startJob($posts['start_job_id'], $data);
				}
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
				
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
				
				if($jobPacketTable->checkMilestoneCanBeCompleted($posts['milestone_id'], $posts['steps_completed'])){
					
					$objCaddesignTable = $sm->get('Order\Model\CaddesignTable');
					
					foreach($posts as $key => $value){
						if(empty($value))
							unset($posts[$key]);
					}
					
					$milestones_ref_id = $posts['milestone_id'];
					$milestone_type_id = $posts['milestone_type_id'];
					$attachments = isset($posts['multipleimagesHidden'])&& !empty($posts['multipleimagesHidden']) ? json_decode($posts['multipleimagesHidden']) : null;
					
					unset($posts['milestone_type_id']);
					unset($posts['multipleimagesHidden']);
					unset($posts['addms']);
					
					if(isset($posts['exp_delivery_date']) && !empty($posts['exp_delivery_date'])){
						list($d, $m, $y) = explode('/', $posts['exp_delivery_date']);
						$posts['exp_delivery_date'] = "$y-$m-$d";
					}
					
					if(isset($posts['stp2_delivery_date']) && !empty($posts['stp2_delivery_date'])){
						list($d, $m, $y) = explode('/', $posts['stp2_delivery_date']);
						$posts['stp2_delivery_date'] = "$y-$m-$d";
					}
					
					if($posts['steps_completed'] == 1){
						unset($posts['supplier_name']);
						$posts['created_date'] = date('Y-m-d H:i:s');
						$posts['created_by'] = $identity['user_id'];
					} else{
						$posts['modified_date'] = date('Y-m-d H:i:s');
						$posts['modified_by'] = $identity['user_id'];
					}
					
					if(!empty($attachments)){
						$jobPacketTable->saveAttachedFiles($milestone_type_id, $milestones_ref_id, $posts['steps_completed'], $attachments);
					}
					
					echo $objCaddesignTable->saveCADdesign($posts);
				}
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
				
				//$orderTable = $sm->get('Order\Model\OrderTable');
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
				
				if($jobPacketTable->checkMilestoneCanBeCompleted($posts['milestone_id'], $posts['steps_completed'])){
				
					//$order = (array)$orderTable->fetchJobDetails($posts['job_id']);
					
					$milestones_ref_id = $posts['milestone_id'];
					$milestone_type_id = $posts['milestone_type_id'];
					$attachments = isset($posts['multipleimagesHidden'])&& !empty($posts['multipleimagesHidden']) ? json_decode($posts['multipleimagesHidden']) : null;
					
					unset($posts['multipleimagesHidden']);
					unset($posts['milestone_type_id']);
					unset($posts['metal_type_opt']);
					unset($posts['supplier_name']);
					
					$posts['metal_types'] = implode(',', $posts['metal_types']);
					
					list($d, $m, $y) = explode('/', $posts['exp_delivery_date']);
					$posts['exp_delivery_date'] = "$y-$m-$d";
					
					//list($d, $m, $y) = explode('/', $posts['date_delivered']);
					//$posts['date_delivered'] = "$y-$m-$d";
					
					$prototypetTable = $sm->get('Order\Model\PrototypeTable');
					if($prototypetTable->savePrototypeStep($posts)){
						
						if(!empty($attachments))
							$jobPacketTable->saveAttachedFiles($milestone_type_id, $milestones_ref_id, $posts['steps_completed'], $attachments);
						
						echo 1;
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
				
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
				
				if($jobPacketTable->checkMilestoneCanBeCompleted($posts['milestone_id'], $posts['steps_completed'])){
				
					$data = array('milestone_id' => $posts['milestone_id'], 'steps_completed' => $posts['steps_completed'], 'stp2_client_reviewed' => 2, 'job_id' => $posts['job_id']);
					
					list($d, $m, $y) = explode('/', $posts['date_delivered']);
					$data['date_delivered'] = "$y-$m-$d";
					
					$prototypetTable = $sm->get('Order\Model\PrototypeTable');
					
					echo $prototypetTable->savePrototypeStep($data);
					exit;
				}
				
				echo 0;
				exit;
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
				
				//$orderTable = $sm->get('Order\Model\OrderTable');
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
				
				if($jobPacketTable->checkMilestoneCanBeCompleted($posts['milestone_id'], $posts['steps_completed'])){
				
					//$order = (array)$orderTable->fetchJobDetails($posts['job_id']);
					
					$milestones_ref_id = $posts['milestone_id'];
					$milestone_type_id = $posts['milestone_type_id'];
					$attachments = isset($posts['multipleimagesHidden'])&& !empty($posts['multipleimagesHidden']) ? json_decode($posts['multipleimagesHidden']) : null;
					
					unset($posts['multipleimagesHidden']);
					unset($posts['milestone_type_id']);
					unset($posts['metal_type_opt']);
					unset($posts['supplier_name']);
					
					$posts['metal_types'] = implode(',', $posts['metal_types']);
					
					list($d, $m, $y) = explode('/', $posts['exp_delivery_date']);
					$posts['exp_delivery_date'] = "$y-$m-$d";
					
					list($d, $m, $y) = explode('/', $posts['date_delivered']);
					$posts['date_delivered'] = "$y-$m-$d";
					
					$castTable = $sm->get('Order\Model\CastTable');
					if($castTable->saveCastStep($posts)){
						
						if(!empty($attachments))
							$jobPacketTable->saveAttachedFiles($milestone_type_id, $milestones_ref_id, $posts['steps_completed'], $attachments);
						
						echo 1;
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
				
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
				
				if($jobPacketTable->checkMilestoneCanBeCompleted($posts['milestone_id'], $posts['steps_completed'])){
				
					$data = array('milestone_id' => $posts['milestone_id'], 'steps_completed' => $posts['steps_completed'], 'stp2_client_reviewed' => 2, 'job_id' => $posts['job_id']);
					
					$castTable = $sm->get('Order\Model\CastTable');
					
					echo $castTable->saveCastStep($data);
					exit;
				}
				
				echo 0;
				exit;
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
				
				if($jobPacketTable->checkMilestoneCanBeCompleted($posts['milestone_id'], $posts['steps_completed'])){
					
					$attachments = isset($posts['multipleimagesHidden'])&& !empty($posts['multipleimagesHidden']) ? json_decode($posts['multipleimagesHidden']) : null;					
				
					$workshopTable = $this->getServiceLocator()->get('Order\Model\WorkshopTable');
					$workshopData = array('milestone_id' => $posts['milestone_id'], 'job_id' => $posts['job_id'], 'steps_completed' => 1);					
					
					if(!empty($attachments))
							$jobPacketTable->saveAttachedFiles($posts['milestone_type_id'], $posts['milestone_id'], $posts['steps_completed'], $attachments);
					
					echo $workshopTable->saveWorkshopStep($workshopData);
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
				$tasks = array();
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
				
				if($jobPacketTable->checkMilestoneCanBeCompleted($posts['milestone_id'], 2)){
					foreach($posts['job_type_id'] as $value){
						$serializedData[$value] = array(
							'cost' => $posts['cost_'.$value],
							'taskinfo' => $posts['taskinfo_'.$value],
						);
						$tasks[] = $value;
					}
					list($d, $m, $y) = explode('/', $posts['exp_delivery_date']);
					$exp_delivery_date = "$y-$m-$d";
					
					$workshopTable = $sm->get('Order\Model\WorkshopTable');
										
					$data = array(
						'job_id' => $posts['job_id'],
						'milestone_id' => $posts['milestone_id'],
						'supplier_id' => $posts['supplier_id'],
						'exp_delivery_date' => $exp_delivery_date,
						'tasks' => empty($serializedData) ? null : serialize($serializedData)
					);
					$id = $workshopTable->saveSupplierData($data);
					if($id){
						$json_response = array(
							'id' => $id,
							'milestone_id' => $posts['milestone_id'],
							'tasks' => $tasks
						);
						echo json_encode($json_response);
					}else{
						echo 0;
					}
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
				
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
				
				if($jobPacketTable->checkMilestoneCanBeCompleted($posts['milestone_id'], $posts['steps_completed'])){
					$data = array('milestone_id' => $posts['milestone_id'], 'qa_reviewed_by' => $posts['qa_reviewed_by'], 'steps_completed' => $posts['steps_completed'], 'job_id' => $posts['job_id']);
					
					$workshopTable = $sm->get('Order\Model\WorkshopTable');
					echo $workshopTable->saveWorkshopStep($data);
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
	 
	 public function workshopfinalstepAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
				
				if($jobPacketTable->checkMilestoneCanBeCompleted($posts['milestone_id'], $posts['steps_completed'])){
					$data = array('milestone_id' => $posts['milestone_id'], 'client_reviewed' => 1, 'steps_completed' => $posts['steps_completed'], 'job_id' => $posts['job_id']);
					
					$workshopTable = $sm->get('Order\Model\WorkshopTable');
					echo $workshopTable->saveWorkshopStep($data);
				}
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

	/**
	 * Send alert to the admin to start the job
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

					$alertTable = $sm->get('Alert\Model\AlertTable');
					$viewUrl = sprintf('/jobdetails/%s', $jobDetails['job_id']);
					$approvalUrl = sprintf('/approvejob/%s/%s', $jobDetails['job_id'], $jobDetails['approval_code']);

					$reason = $posts['start_comment'];
					if ($reason != '') {
						$reason = ' (' . $reason . ')';
					}
					
					$message = sprintf(
							'Job <a href="%s">%s</a> needs approval%s. <a href="%s">Click to approve</a>.',
							$viewUrl,
							$jobDetails['display_job_id'],
							$reason,
							$approvalUrl
							);
					/* TODO: don't hardcode role ID */
					$alertTable->createRoleAlert($identity['user_id'], 1, $message);
					
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
	 
	 /**
	  * Add milestone in the specific order
	  */
	 public function addmilestoneAction(){
	 	try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			$request = $this->getRequest();
			
			if($request->isPost()){
			
				$posts = $request->getPost()->toArray();
				$current_milestone_id = $posts['current_milestone_id'];
				$job_id = $posts['job_id'];
				$milestone_type_id = $posts['milestone_type_id'];
				
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
				
				if($jobPacketTable->checkMilestoneCanBeAdded($current_milestone_id)){
					echo $jobPacketTable->addMilestone($current_milestone_id, $job_id, $milestone_type_id);
					exit;
				}
				echo 0;				
			}
			
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	 public function deletemilestoneAction(){
	 	try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			$request = $this->getRequest();
			
			if($request->isPost()){
			
				$posts = $request->getPost()->toArray();
				$milestone_id = $posts['milestone_id'];
				$milestone_type_id = $posts['milestone_type_id'];
				
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
				
				$previousMilestoneData = $jobPacketTable->getPreviousMilestone($milestone_id);
				
				if($jobPacketTable->checkMilestoneCanBeDeleted($milestone_id)){
				
					if($milestone_type_id == 1)
						$milestoneTable = $sm->get('Order\Model\CaddesignTable');
					elseif($milestone_type_id == 2)
						$milestoneTable = $sm->get('Order\Model\PrototypeTable');
					elseif($milestone_type_id == 3)
						$milestoneTable = $sm->get('Order\Model\castTable');
					elseif($milestone_type_id == 4)
						$milestoneTable = $sm->get('Order\Model\WorkshopTable');
						
					$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
					$jobPacketTable->deleteMilestoneFiles($milestone_id);	
					
					if($milestoneTable->deleteMilestone($milestone_id, $sm)){
						$job_id = $previousMilestoneData['job_id'];
						$data = array('milestone_id' => $previousMilestoneData['current_milestone_id'],
									  'current_milestone_id' => $previousMilestoneData['current_milestone_type_id'],
									  'current_milestone_step_id' => $previousMilestoneData['current_milestone_step_id']);
						echo $jobPacketTable->updateJobPacket($job_id, $data);
					}
				}
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	 /**
	  * Change job status to paused or start
	  */
	 public function changejobstatusAction(){
	 	try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			$request = $this->getRequest();
			
			if($request->isPost()){
			
				$posts = $request->getPost()->toArray();
				$job_id = $posts['job_id'];
				$status = $posts['status'];
				
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
				
				echo $jobPacketTable->changeJobStatus($job_id, $status);
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	 /**
	  * Change job status to paused or start
	  */
	 public function emailmilestoneAction(){
	 	try{
			$sm = $this->getServiceLocator();
			
			$config = $sm->get('Config');
			
			$identity = $sm->get('AuthService')->getIdentity();
			$request = $this->getRequest();
			
			if($request->isPost()){
			
				$posts = $request->getPost()->toArray();
				$milestone_id = $posts['milestone_id'];
				$milestone_type_id = $posts['milestone_type_id'];
				$step = $posts['step'];
				$emailParams = array();
				$attachments = array();
				$currDate = date('Y-m-d H:i:s');
				$attachments = array();
				
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
					
				$milestoneData = $jobPacketTable->fetchMilestoneData($milestone_id, $milestone_type_id);
				
				if(empty($milestoneData) || $milestoneData->steps_completed < $step){
					echo 0;
					exit;
				}
				
				$templatePath = $config['documentRoot'].'templates/email/';
				
				$view = new \Zend\View\Renderer\PhpRenderer();
				$resolver = new \Zend\View\Resolver\TemplateMapResolver();
								
				if($milestone_type_id == 1){
					$resolver->setMap(array(
						'mailTemplate' => $templatePath.'cad_email_template.phtml'
					));
								 
					$emailParams['template'] = 'cad_email_template.phtml';
					
					$milestoneTable = $sm->get('Order\Model\CaddesignTable');
					
					if($step == 1){
						$data = array('step1_emailed_on' => $currDate);
					}elseif($step == 3){
						$files = $jobPacketTable->fetchMilestoneFiles($milestone_id, $step);
						foreach($files as $file){
							$content = file_get_contents($config['documentRoot'] . 'milestone_attachments/' . $file['image']);
							$attachments[] = array('filename' => $file['image'], 'content' => $content);
						}
						
						$data = array('step3_emailed_on' => $currDate);
					}
					
					$tmplParams = array('message' => $posts['message'], 'milestoneData' => $milestoneData, 'config' => $config);
					
				}elseif($milestone_type_id == 2){
					$lookupTable = $this->getServiceLocator()->get('Inventory\Model\LookupTable');
					$metalTypes = $lookupTable->fetchMetalTypeOptions();
					
					$resolver->setMap(array(
						'mailTemplate' => $templatePath.'prototype_email_template.phtml'
					));
								 
					$emailParams['template'] = 'prototype_email_template.phtml';
					
					$milestoneTable = $sm->get('Order\Model\PrototypeTable');
					
					$data = array('step1_emailed_on' => $currDate);
					
					$tmplParams = array('message' => $posts['message'], 'milestoneData' => $milestoneData, 'config' => $config, 'metalTypes' => $metalTypes);
					
				}elseif($milestone_type_id == 3){
					$lookupTable = $this->getServiceLocator()->get('Inventory\Model\LookupTable');
					$metalTypes = $lookupTable->fetchMetalTypeOptions();
					
					$resolver->setMap(array(
						'mailTemplate' => $templatePath.'prototype_email_template.phtml'
					));
								 
					$emailParams['template'] = 'prototype_email_template.phtml';
					
					$milestoneTable = $sm->get('Order\Model\CastTable');
					
					$data = array('step1_emailed_on' => $currDate);
					
					$tmplParams = array('message' => $posts['message'], 'milestoneData' => $milestoneData, 'config' => $config, 'metalTypes' => $metalTypes);
					
				}
				
				$emailParams['toEmail'] = $milestoneData['supplier_email'];
				$emailParams['toName'] = $milestoneData['supplier_name'];
				$emailParams['subject'] = $posts['subject'];
				$emailParams['message'] = $tmplParams;
				
				$emailParams['additionalEmails']['cc'][] = array('email' => $config['smtp_details']['ccEmail'], 'name' =>  $config['smtp_details']['ccName']);				
				
				if(isset($posts['copy_email']) && $posts['copy_email'] == 1){
					$emailParams['additionalEmails']['cc'][] = array('email' => $identity['email'], 'name' =>  $identity['first_name'] .' '. $identity['last_name']);
				}
				
				$view->setResolver($resolver);				 	
				$viewModel = new \Zend\View\Model\ViewModel();
				$viewModel->setTemplate('mailTemplate')->setVariables(array('msgholdename' => $emailParams ['toName'], 'data' => $tmplParams));
				$html = $view->render($viewModel);
				
				if(isset($posts['attachment_check']) && $posts['attachment_check'] == 1){		
					$emailAttachments = json_decode($posts['email_attachment_list']);
					foreach($emailAttachments as $filename){
						$content = file_get_contents($config['documentRoot'] . 'email_attachments/' . $filename);
						$attachments[] = array('filename' => $filename, 'content' => $content);
					}
				}
				
				$response = \De\Service\EmailService::sendEmailWithAttachments($config['smtp_details'], $emailParams, $attachments);
								
				$insetData = array('milestone_id' => $milestone_id, 'milestone_type_id' => $milestone_type_id, 'step' => $step,
								   'supplier_id' => $milestoneData->supplier_id, 'subject' => $posts['subject'], 'message' => $posts['message'],
								   'attachments' => $posts['email_attachment_list'], 'email_body' => $html,
								   'created_by' => $identity['user_id'], 'created_date' => date('Y-m-d H:i:s'));
								   
				$insetData['copy_email'] = $config['smtp_details']['ccName'].'<'.$config['smtp_details']['ccEmail'].'>';
								   
				if(isset($posts['copy_email']) && $posts['copy_email'] == 1){
					$insetData['copy_email'] .= ','.$identity['email'].'<'.$identity['first_name'] .' '. $identity['last_name'].'>';
				}
				if(isset($posts['attachment_check']) && $posts['attachment_check'] == 1){
					$insetData['file_attached'] = 1;
				}
				
				$where = array('milestone_id' => $milestone_id);
				if($milestoneTable->updateMilestone($data, $where)){					
					if($jobPacketTable->saveMilestoneEmail($insetData))
						echo date('d/m/Y g:i a', strtotime($currDate));
				}			
			}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	 public function composemilestoneemailAction(){
	 	try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			
			$viewRender = $sm->get('ViewRenderer');
			$htmlViewPart = new ViewModel();
			
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$milestone_id = $posts['milestone_id'];
				$milestone_type_id = $posts['milestone_type_id'];
				$step = $posts['step'];
				
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
					
				$milestoneData = $jobPacketTable->fetchMilestoneData($milestone_id, $milestone_type_id);
				
				if(empty($milestoneData) || $milestoneData->steps_completed < $step){
					echo 0;
					exit;
				}
				
				$composeEmailForm = $sm->get('Order/Form/ComposeEmailForm');
				$composeEmailForm->setData($milestoneData);
				$composeEmailForm->get('milestone_type_id')->setValue($milestone_type_id);
				$composeEmailForm->get('step')->setValue($step);
				
				$htmlViewPart->setTemplate('order/index/composeemail')
							 ->setTerminal(true)
							 ->setVariables(array('composeEmailForm' => $composeEmailForm, 'milestoneData' => $milestoneData, 'config' => $config));
			}
		
			$html = $viewRender->render($htmlViewPart);	
			
			return $this->getResponse()->setContent($html);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Email grid
	 */
	public function ajaxgetmilestoneemailAction() {
        try {
			$sm = $this->getServiceLocator();
			$config = $sm->get('Config');
			$params = $this->getRequest()->getQuery()->toArray();
			
			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
			$keyword = $params['keyword'];			
			$sortdatafield = $params['sortdatafield'];
			$sortorder = $params['sortorder'];
			$supplier_id = $params['supplier_id'];
			
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			if(!empty($keyword)){
				$offset = 0;
			}
			
			$jobPacketTable = $sm->get('Order/Model/JobPacketTable');
			
			$emails = $jobPacketTable->fetchAllEmail($limit, $offset, $sortdatafield, $sortorder, $supplier_id, $keyword);
			
			foreach ($emails['Rows'] as $key => $value) {
				$emails['Rows'][$key]['created_date'] = date($config['phpDateFormat'], strtotime($value['created_date']));
				$emails['Rows'][$key]['created_time'] = date('g:i a', strtotime($value['created_date']));
            }
			echo json_encode($emails);
			exit;
        } catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
    }
	
	/**
	 * View email details
	 */
	public function viewmilestoneemailAction(){
		try {
			$sm = $this->getServiceLocator();
			$config = $sm->get('Config');
			$params = $this->getRequest()->getQuery()->toArray();
			
			$id = $this->params('id');
						
			$jobPacketTable = $sm->get('Order/Model/JobPacketTable');
			
			$emailData = $jobPacketTable->fetchEmail($id);			
			$emailData['to_email'] = $emailData['supplier_email'];
			$emailData['to_name'] = $emailData['supplier_name'];
			
			// open connection
			$imap = new \De\Service\Imap($config['imap_details']['imapserver'], $config['imap_details']['username'], $config['imap_details']['password'], $config['imap_details']['encryption']);
			
			// select folder Inbox
			$imap->selectFolder('Inbox');
			$emailsSent = $imap->searchEmails('Subject "'.$emailData['subject'].'" To "'.$emailData['supplier_email'].'"');
			
			$imap->selectFolder('Inbox');
			$emailsFrom = $imap->searchEmails('Subject "'.$emailData['subject'].'" From "'.$emailData['supplier_email'].'"');
			
			if(is_array($emailsFrom)){
				$emails = array_merge($emailsSent, $emailsFrom);
				sort($emails);
			}else{
				sort($emailsSent);
				$emails = $emailsSent;
			}	
			
			$replyEmailForm = $sm->get('Invoice\Form\ReplyEmailForm');
			$replyEmailForm->get('to_email')->setValue($emailData['supplier_email']);
			$replyEmailForm->get('to_name')->setValue($emailData['supplier_name']);
			$replyEmailForm->get('subject')->setValue($emailData['subject']);
			
			return array('imap' => $imap, 'emails' => $emails, 'emailData' => $emailData, 'replyEmailForm' => $replyEmailForm);
        } catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
	}
	
	/**
	 * Open update job form
	 */
	public function updatejobformAction(){
		try {
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$job_id = $posts['job_id'];
				
				$orderTable = $sm->get('Order\Model\OrderTable');				
				$jobDetails = (array)$orderTable->fetchJobDetails($job_id);
				
				$objInvoice = $sm->get('Invoice\Model\InvoiceTable');
				$invoiceItems = $objInvoice->fetchInvoiceItems($jobDetails['invoice_number']);
								
				$jobForm = $sm->get('Order\Form\JobForm');
				$jobForm->get('id')->setValue($jobDetails['job_id']);
				$jobForm->get('order_id')->setValue($jobDetails['order_id']);
				$jobForm->get('owner_id')->setValue($jobDetails['owner_id']);
				$jobForm->get('exp_delivery_date')->setValue(date($config['formDateFormat'], strtotime($jobDetails['exp_delivery_date'])));
				$jobForm->get('job_save')->setLabel('Update');
				
				$viewRender = $sm->get('ViewRenderer');
				$htmlViewPart = new ViewModel();
				
				$userTable = $sm->get('Customer\Model\UsersTable');
				$ownerOptions = $userTable->fetchUsersForTasks();
				
				$htmlViewPart->setTemplate('order/index/jobform')
							 ->setTerminal(true)
							 ->setVariables(array('newJobForm' => $jobForm, 'jobDetails' => $jobDetails, 'invoice_items' => $invoiceItems, 'config' => $config, 'ownerOptions' => $ownerOptions));
			}
		
			$html = $viewRender->render($htmlViewPart);	
			
			return $this->getResponse()->setContent($html);
        } catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
	}
	
	/**
	 * Download email attachment
	 */
	public function downloademailattachmentAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
		
			$id = base64_decode($this->params('id'));
			$index = base64_decode($this->params('index'));
						
			// open connection
			$imap = new \De\Service\Imap($config['imap_details']['imapserver'], $config['imap_details']['username'], $config['imap_details']['password'], $config['imap_details']['encryption']);
			
			// stop on error
			if($imap->isConnected()===false)
				die($imap->getError());
				
			$attachment = $imap->getAttachment($id, $index);
			
			$mimeType = \De\Service\CommonService::getMimeType($attachment['name']);
			
			$fileUrl = 'http:/'.$_SERVER['HTTP_HOST'].'/'.$attachment['name'];
			header('Content-Type: '.$mimeType);
			header("Content-Transfer-Encoding: Binary"); 
			header("Content-disposition: attachment; filename=\"" . basename($fileUrl) . "\""); 
			readfile($attachment['content']);
			
			exit;
		} catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
	}
	
	/**
	 *
	 */
	public function printjobAction(){
		try{
			$sm = $this->getServiceLocator();
			
			$request = $this->getRequest();
			
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$jobPacketTable = $sm->get('Order\Model\JobPacketTable');
				$workshop = $jobPacketTable->fetchMilestoneData($posts['milestone_id'], 4);
				
				if(empty($workshop)){
					echo 0;
					exit;
				}
				$orderTable = $sm->get('Order\Model\OrderTable');
				$order = $orderTable->fetchOrderDetailsByJobId($workshop->job_id);				
				
				$workshopTable = $sm->get('Order\Model\WorkshopTable');
				$suppliersToWorkshop = $workshopTable->fetchWorkshopSuppliers($posts['milestone_id']);
				
				$order_id = \De\Service\CommonService::generateStockCode($order->id, 'order');
				$job_id = \De\Service\CommonService::generateStockCode($workshop->job_id, 'order');
				
				$lookupTable = $this->getServiceLocator()->get('Inventory\Model\LookupTable');
			
				$jobTypes = $lookupTable->fetchWorkshopJobTypeOptions();
			}
			
			$viewModel = new ViewModel();
			$viewModel->setVariables(array('workshop' => $workshop, 'order' => $order, 'suppliersToWorkshop' => $suppliersToWorkshop, 'order_id' => $order_id, 'job_id' => $job_id, 'jobTypes' => $jobTypes))
					  ->setTerminal(true);
			
			return $viewModel;
			
		} catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
	}
	
	/**
	 *
	 */
	 public function updateworkshoptaskAction(){
	 	try{
			$sm = $this->getServiceLocator();
			
			$request = $this->getRequest();
			
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				
				$workshopTable = $sm->get('Order\Model\WorkshopTable');
				$suppliersToWorkshop = $workshopTable->fetchWorkshopSuppliers($posts['milestone_id'], $posts['id']);
				$tasks = unserialize($suppliersToWorkshop[0]['tasks']);
				
				foreach($tasks as $task => $taskInfo){
					if($task == $posts['task'])
						$tasks[$task]['cost'] = $posts['cost'];
				}
				
				$data = serialize($tasks);
				echo $workshopTable->updateTask($posts['id'], $posts['milestone_id'], $data);
			}
			exit;
		} catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
	 }
}

