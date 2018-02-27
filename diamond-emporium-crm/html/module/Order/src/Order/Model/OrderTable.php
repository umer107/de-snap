<?php
/**
 *	This class is the main model for order module
 */

namespace Order\Model;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

class OrderTable
{
	protected $dbAdapter;
	protected $resultSetPrototype;
	protected $config;
	protected $serviceManager;
	
	public function __construct(TableGateway $tableGateway, $config, $serviceManager)
	{
		$this->tableGateway = $tableGateway;
		$this->config = $config;
		$this->serviceManager = $serviceManager;
	}
	
	/**
	 * Store order data in database
	 */
	public function createOrder($data){
		try{
			$id = $data['id'];
			unset($data['id']);
			
			$attachment['attachment'] = json_decode($data['order_attachment']);
			unset($data['order_attachment']);			
			
			if(!empty($id)){
			
				$select = new \Zend\Db\Sql\Select();
				$this->tableGateway->update($data, array('id' => $id));
				
				if(count($attachment['attachment']) > 0){
					foreach($attachment['attachment'] as $key => $value){
						$tableOrderAttchments = new TableGateway('de_order_attchments', $this->tableGateway->getAdapter());
						$order_attachment['order_id'] = $id;
						$order_attachment['attachment'] = $value;
						if($this->checkMediaFileByName($order_attachment['attachment']) == 0){
							$tableOrderAttchments->insert($order_attachment);
						}
					}
				}
				return $id;
				
			} else {
				if($this->tableGateway->insert($data)){
					$id = $this->tableGateway->lastInsertValue;
					if(count($attachment['attachment']) > 0){
						foreach($attachment['attachment'] as $key => $value){
							$tableOrderAttchments = new TableGateway('de_order_attchments', $this->tableGateway->getAdapter());
							$order_attachment['order_id'] = $id;
							$order_attachment['attachment'] = $value;
							$tableOrderAttchments->insert($order_attachment);
						}
					}
					
					return $id;
				}
			}
			
			return 0;
		}catch(\Exception $e){echo $e->getMessage ();
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch orders
	 * $limit = Number of records to be fetched
	 * $offset = Data fetch should start from
	 * $keyword = optional, Search string
	 * $cust_id = optional, orders belongs to the customer
	 * $sortdatafield = optional, sort field
	 * $sortorder = optional, sort order 
	 */
	public function fetchAll($limit, $offset, $keyword = null, $cust_id = null,  $sortdatafield = null, $sortorder = null)
	{
		try{
			$keyword = trim($keyword);
			$select = new \Zend\Db\Sql\Select();
			$customer_name = new \Zend\Db\Sql\Expression(
				'CONCAT(cust.first_name, \' \', cust.last_name)'
			);
			$owner_name = new \Zend\Db\Sql\Expression(
				'CONCAT(u.first_name, \' \', u.last_name)'
			);
						
			$select->from(array('o' => 'de_orders'))
				   ->columns(array('id', 'exp_delivery_date', 'comment', 'opp_id', 'invoice_number', 'created_date', 'special_request'))
				   ->join(array('opp' => 'de_opportunities'), 'o.opp_id = opp.id', array('opportunity_name'), 'left')
				   ->join(array('cust' => 'de_customers'), 'cust.id = o.cust_id', array('customer_name' => $customer_name), 'left')
				   ->join(array('u' => 'de_users'), 'u.user_id = o.created_by', array('owner_name' => $owner_name), 'left')
				    ->join(array('inv' => 'de_invoice'), 'inv.invoice_number = o.invoice_number', array('xero_tax_rate','xero_date_due','xero_payment_made', 'xero_total' ), 'left');
			if(!empty($keyword)){
				$where = new \Zend\Db\Sql\Where();
				$where->NEST->addPredicates(array(
					    new \Zend\Db\Sql\Predicate\Like('o.id', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('cust.first_name', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('cust.last_name', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('o.invoice_number', "%$keyword%"),
				    ), 'OR'
				)->UNNEST;
				/*$where->addPredicates(array(
					new \Zend\Db\Sql\Predicate\Like('u.first_name', "%$keyword%"),
				));*/
				$select->where($where);
			}
			
			if(!empty($cust_id))
				$select->where(array('o.cust_id = ?' => $cust_id));
			
			if(!empty($sortdatafield) && !empty($sortorder)){
				if($sortdatafield == 'id')
					$select->order("o.id $sortorder");					
				elseif($sortdatafield == 'comment')
					$select->order("o.comment $sortorder");
				elseif($sortdatafield == 'customer_name')
					$select->order("cust.first_name $sortorder");
				if($sortdatafield == 'opportunity_name')
					$select->order("opp.opportunity_name $sortorder");
				elseif($sortdatafield == 'invoice_number')
					$select->order("o.invoice_number $sortorder");
				elseif($sortdatafield == 'owner_name')
					$select->order("u.first_name $sortorder");
				elseif($sortdatafield == 'created_date')
					$select->order("o.created_date $sortorder");
			}else{
				$select->order("o.id DESC");
			}
			//echo $select->getSqlString();exit;

			$adapter = $this->tableGateway->getAdapter();
			
			$statement = $adapter->createStatement();			
			$select->prepareStatement($adapter, $statement);
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($statement->execute());
			
			$select->limit($limit);
			$select->offset($offset);
			
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			$resultSetLimit = new \Zend\Db\ResultSet\ResultSet();
			$resultSetLimit->initialize($statement->execute());
			
			$result['TotalRows'] = count($resultSet);
			$result['Rows'] = $resultSetLimit->toArray();
			
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch order details
	 * $order_id - order id, primary key
	 */
	public function fetchOrderDetails($order_id){
		try{
			$select = new \Zend\Db\Sql\Select();
			
			$customer_name = new \Zend\Db\Sql\Expression(
				'CONCAT(cust.first_name, \' \', cust.last_name)'
			);
			$partner_name = new \Zend\Db\Sql\Expression(
				'CONCAT(part.first_name, \' \', part.last_name)'
			);
			$job_count = new \Zend\Db\Sql\Expression(
				'COUNT(jp.id)'
			);
						
			$select->from(array('o' => 'de_orders'))
				   ->columns(array('id', 'cust_id', 'opp_id', 'exp_delivery_date', 'comment', 'invoice_number', 'created_date', 'special_request'))
				   ->join(array('cust' => 'de_customers'), 'cust.id = o.cust_id', array('fullname' => $customer_name, 'email', 'mobile'), 'left')
				   ->join(array('part' => 'de_customers'), 'part.id = cust.partner_id', array('partner_id' => 'id', 'partner_name' => $partner_name, 'part_email' => 'email', 'part_mobile' => 'mobile'), 'left')
				   ->join(array('opp' => 'de_opportunities'), 'opp.id = o.opp_id', array('opp_name' => 'opportunity_name'), 'left')
				   ->join(array('jp' => 'de_job_packet'), 'jp.order_id = o.id', array('job_count' => $job_count), 'left')
				    ->join(array('inv' => 'de_invoice'), 'inv.invoice_number = o.invoice_number', array('inv_id' => 'id'), 'left')
				   ->where(array('o.id = ?' => $order_id))
				   ->group('o.id');
			//echo $select->getSqlString();exit;
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			return $result->current();
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Delete order from the db, check if the order have any job or not
	 * $order_id - order id, primary key
	 */
	public function deleteOrder($order_id){
		try{
			$cadDesignTable = $this->serviceManager->get('Order\Model\CaddesignTable');
			$listJobs = $cadDesignTable->getJobsByOrderId($order_id);
			$jobPacketTable = $this->serviceManager->get('Order\Model\JobPacketTable');
			foreach($listJobs as $jobIdentity){
				$jobPacketTable->deleteJobPacket($jobIdentity);
			}
			
			/*$select = new \Zend\Db\Sql\Select();

			$job_count = new \Zend\Db\Sql\Expression(
				'COUNT(jp.id)'
			);
						
			$select->from(array('jp' => 'de_job_packet'))
				   ->columns(array('job_count' => $job_count))
				   ->where(array('jp.order_id = ?' => $order_id));
			//echo $select->getSqlString();exit;
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			$count = (array)$result->current();
			
			if($count['job_count'] == 0){*/
				if($this->tableGateway->delete(array('id' => $order_id))){
					$dbAdapter = $this->tableGateway->getAdapter();
					$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
					
					$imageArr = $this->fetchOrderAttachments($order_id);
					
					$path = $this->config['documentRoot'].'order_attachment/';
					foreach($imageArr as $image){
						if(file_exists($path.$image))
							unlink($path.$image);
					}
					
					$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_order_attchments', $dbAdapter, null, $resultSetPrototype);
					$tableGateway->delete(array('order_id' => $order_id));
					return true;
				}				
			//}
			
			return false;
		}catch(\Exception $e){echo $e->getMessage ();
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch order details
	 * $job_id - job id, primary key
	 */
	function fetchOrderDetailsByJobId($job_id){
		try{
			$select = new \Zend\Db\Sql\Select();
			
			$customer_name = new \Zend\Db\Sql\Expression(
				'CONCAT(cust.first_name, \' \', cust.last_name)'
			);
			$partner_name = new \Zend\Db\Sql\Expression(
				'CONCAT(part.first_name, \' \', part.last_name)'
			);
						
			$select->from(array('o' => 'de_orders'))
				   ->columns(array('id', 'cust_id', 'opp_id', 'exp_delivery_date', 'comment', 'invoice_number', 'created_date', 'special_request'))
				   ->join(array('jp' => 'de_job_packet'), 'jp.order_id = o.id', array('items'), 'inner')
				   ->join(array('cust' => 'de_customers'), 'cust.id = o.cust_id', array('fullname' => $customer_name, 'email', 'mobile'), 'left')
				   ->join(array('part' => 'de_customers'), 'part.id = cust.partner_id', array('partner_id' => 'id', 'partner_name' => $partner_name, 'part_email' => 'email', 'part_mobile' => 'mobile'), 'left')
				   ->join(array('opp' => 'de_opportunities'), 'opp.id = o.opp_id', array('opp_name' => 'opportunity_name'), 'left')				  
				   ->where(array('jp.id = ?' => $job_id))
				   ->group('o.id');
			//echo $select->getSqlString();exit;
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			return $result->current();
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch order details
	 * $job_id - job id, primary key
	 */
	function fetchJobDetails($job_id){
		try{
			$select = new \Zend\Db\Sql\Select();
			
			$customer_name = new \Zend\Db\Sql\Expression(
				'CONCAT(cust.first_name, \' \', cust.last_name)'
			);
			$owner_name = new \Zend\Db\Sql\Expression(
				'CONCAT(owner.first_name, \' \', owner.last_name)'
			);
						
			$select->from(array('job' => 'de_job_packet'))
				   ->columns(array('job_id' => 'id', 'exp_delivery_date', 'items', 'status', 'created_date'))
				   ->join(array('ord' => 'de_orders'), 'ord.id = job.order_id', array('order_id' => 'id', 'invoice_number'), 'inner')
				   ->join(array('owner' => 'de_users'), 'owner.user_id = job.owner_id', array('owner_id' => 'user_id', 'owner_name' => $owner_name), 'left')
				   ->join(array('cust' => 'de_customers'), 'cust.id = ord.cust_id', array('customer_name' => $customer_name, 'email', 'mobile'), 'left')
				   ->join(array('opp' => 'de_opportunities'), 'opp.id = ord.opp_id', array('opportunity_name'), 'left')
				   ->where(array('job.id = ?' => $job_id));
			//echo $select->getSqlString();exit;
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			return $result->current();
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Get Media Files for Order
	 */
	function fetchOrderAttachments($order_id, $fullDetails = false){
		try{
			$select = new \Zend\Db\Sql\Select();
			if($fullDetails){
				$select->from(array('attchments' => 'de_order_attchments'))
				   ->columns(array('id', 'order_id', 'attachment'))
				   ->where(array('attchments.order_id = ?' => $order_id));
			} else {
				$select->from(array('attchments' => 'de_order_attchments'))
				   ->columns(array('attachment'))
				   ->where(array('attchments.order_id = ?' => $order_id));
			}
			//echo $select->getSqlString();exit;
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			$arrayResults = $result->toArray();
			if(!$fullDetails){
				foreach($arrayResults as $key => $data){
					$modifiedArrayResults[] = $data['attachment'];
				}
			} else {
				$modifiedArrayResults = $arrayResults;
			}
			return $modifiedArrayResults;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Remove Media Image From DB
	 */
	function removeOrderAttachments($id){
		try{
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_order_attchments', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('id' => $id));
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Checking Media file unique name
	 */
	 
	public function checkMediaFileByName($fileName){
		$select = new \Zend\Db\Sql\Select();
		$select->from(array('dmi' => 'de_order_attchments'))
			   ->columns(array('id'))
			   ->where(array('dmi.attachment = ?' => $fileName));
		$adapter = $this->tableGateway->getAdapter();				   
		$statement = $adapter->createStatement();
		$select->prepareStatement($adapter, $statement);
		
		$result = new \Zend\Db\ResultSet\ResultSet();
		$result->initialize($statement->execute());
		return $result->count();
	}
}
