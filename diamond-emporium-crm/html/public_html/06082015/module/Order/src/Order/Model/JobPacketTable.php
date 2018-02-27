<?php
/**
 *	This class is the main model for order module
 */

namespace Order\Model;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

class JobPacketTable
{
	protected $dbAdapter;
	protected $resultSetPrototype;
	protected $config;
	
	public function __construct(TableGateway $tableGateway, $config)
	{
		$this->tableGateway = $tableGateway;
		$this->config = $config;
	}
	
	/**
	 * Store order data in database
	 */
	public function createJobPacket($data){
		try{
			if($this->tableGateway->insert($data)){
				return $this->tableGateway->lastInsertValue;
			}
		}catch(\Exception $e){echo $e->getMessage ();
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch orders
	 * $limit = Number of records to be fetched
	 * $offset = Data fetch should start from
	 * $sortdatafield = optional, sort field
	 * $sortorder = optional, sort order 
	 */
	public function fetchAll($limit, $offset, $sortdatafield = null, $sortorder = null, $order_id = null, $cust_id = null)
	{
		try{
			$keyword = trim($keyword);
			$select = new \Zend\Db\Sql\Select();
			$customer_name = new \Zend\Db\Sql\Expression(
				'CONCAT(cust.first_name, \' \', cust.last_name)'
			);
			
			$due_days = new \Zend\Db\Sql\Expression(
				'DATEDIFF(job.exp_delivery_date, CURDATE())'
			);
			
			$owner_name = new \Zend\Db\Sql\Expression(
				'CONCAT(u.first_name, \' \', u.last_name)'
			);
						
			$select->from(array('ord' => 'de_orders'))
				   ->columns(array('comment', 'invoice_number', 'created_date'))
				   ->join(array('job' => 'de_job_packet'), 'ord.id = job.order_id', array('job_id' => 'id', 'milestones', 'exp_delivery_date', 'milestones_completed', 'due_days' => $due_days), 'inner')
				   ->join(array('dc' => 'de_order_job_consign'), 'dc.job_id = job.id', array('reserve_time', 'reserve_notes', 'tracking_id'), 'left')
				   ->join(array('isl' => 'de_inventory_status_lookup'), 'isl.id = dc.inventory_status_id', array('inventory_status_name'), 'left')
				   ->join(array('isrl' => 'de_inventory_status_reason_lookup'), 'isrl.id = dc.inventory_status_reason_id', array('inventory_status_reason'), 'left')
				   ->join(array('itl' => 'de_inventory_type_lookup'), 'itl.id = dc.inventory_type_id', array('inventory_type'), 'left')
				   ->join(array('itsl' => 'de_inventory_tracking_status_lookup'), 'itsl.id = dc.inventory_tracking_status_id', array('inventory_tracking_status'), 'left')
				   ->join(array('itrl' => 'de_inventory_tracking_reason_lookup'), 'itrl.id = dc.inventory_tracking_reason_id', array('inventory_tracking_reason'), 'left')
				   ->join(array('cust' => 'de_customers'), 'cust.id = ord.cust_id', array('customer_name' => $customer_name), 'inner')
				   ->join(array('u' => 'de_users'), 'u.user_id = dc.owner_id', array('owner_name' => $owner_name), 'left');
				   //->join(array('u' => 'de_users'), 'u.user_id = ord.created_by', array('owner_name' => $owner_name), 'left');
						
			if(!empty($order_id))
				$select->where(array('ord.id = ?' => $order_id));
				
			if(!empty($cust_id))
				$select->where(array('ord.cust_id = ?' => $cust_id));
			
			if(!empty($sortdatafield) && !empty($sortorder)){
				if($sortdatafield == 'id')
					$select->order("job.id $sortorder");
				elseif($sortdatafield == 'customer_name')
					$select->order("cust.first_name $sortorder");
				elseif($sortdatafield == 'due_days')
					$select->order("job.exp_delivery_date $sortorder");
				elseif($sortdatafield == 'exp_delivery_date')
					$select->order("job.exp_delivery_date $sortorder");
			}else{
				$select->order("ord.id DESC");
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
	 * fetch details of a specefic order
	 * @params $order_id
	 */
	public function fetchOrderDetails($order_id){
		try{
			$select = new \Zend\Db\Sql\Select();
						
			$select->from(array('o' => 'de_orders'))
				   ->columns(array('id', 'exp_delivery_date', 'comment', 'invoice_number', 'created_date'))
				   ->where(array('o.id = ?' => $order_id));
			
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
	 * Change job status to 1
	 * $job_id, primary_key
	 * $data, array
	 */	 
	 public function startJob($job_id, $data){
	 	try{
						
			return $this->tableGateway->update($data, array('id' => $job_id));
			
		}catch(\Exception $e){echo $e->getMessage ();
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	/**
	 * Validate miestone, check all previous milestones are completed on not
	 * $job_id
	 * $milestone_id
	 */
	function checkMilestoneCanBeCompleted($job_id, $milestone_id){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('job' => 'de_job_packet'))
				   ->columns(array('milestones', 'milestones_completed'))
				   ->where(array('job.id = ?' => $job_id));
				   
				   
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			$job = $result->current();
			$milestones = empty($job->milestones) ? null : explode(',', $job->milestones);
			$milestones_completed = empty($job->milestones_completed) ? null : explode(',', $job->milestones_completed);
			
			if(empty($job))
				return false;
			
			if(in_array($milestone_id, $milestones)){ // If milestone exists in the requirement
				$milestone_index = array_search($milestone_id, $milestones);
				
				if(empty($milestones_completed) && $milestone_index == 0){ // If no milestone is completed
					return true;
				}elseif(in_array($milestone_id, $milestones_completed)){ // Cecking if the milestone is already completed
					return false;
				}else{
					foreach($milestones as $key => $value){
						if($key < $milestone_index){
							if(!in_array($value, $milestones_completed))
								return false;
						}
					}
					return true;
				}
			}
			
			return false;
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Mark a milestone as completed
	 * $job_id
	 * $milestone_id
	 */
	public function completeMilestone($job_id, $milestone_id){
		try{
			$select = new \Zend\Db\Sql\Select();
						
			$select->from(array('job' => 'de_job_packet'))
				   ->columns(array('milestones_completed'))
				   ->where(array('job.id = ?' => $job_id));
				   
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			$job = $result->current();
			$milestones_completed = explode(',', $job->milestones_completed);
			$milestones_completed[] = $milestone_id;
			
			$tableJobPackets = new TableGateway('de_job_packet', $this->tableGateway->getAdapter());
			return $tableJobPackets->update(array('milestones_completed' => implode(',', $milestones_completed)), array('id' => $job_id));
					
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Save milestine images
	 * $data, array
	 */
	public function saveMilestoneImages($data){
		try{
			$milestoneFiles = new TableGateway('de_milestone_images', $this->tableGateway->getAdapter());
			
			return $milestoneFiles->insert($data);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * fetch all attached files for milestone
	 * $milestones_ref_id, $milestone_type
	 */	 
	 public function fetchMilestoneImages($milestones_ref_id, $milestone_type){
	 	try{
			$select = new \Zend\Db\Sql\Select();
						
			$select->from(array('mi' => 'de_milestone_images'))
				   ->columns(array('step', 'image'))
				   ->where(array('milestone_type' => $milestone_type, 'mi.milestones_ref_id = ?' => $milestones_ref_id));
				   
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			return $result->toArray();
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	 /**
	  * Validate if request to start a job is valid or not
	  * $job_id, primary key
	  * $approval_code, unique no
	  */
	 public function validateApprovalCode($job_id, $approval_code){
	 	try{
			$select = new \Zend\Db\Sql\Select();
			
			$count = new \Zend\Db\Sql\Expression(
				'COUNT(job.id)'
			);
						
			$select->from(array('job' => 'de_job_packet'))
				   ->columns(array('count' => $count))
				   ->where(array('job.id = ?' => $job_id, 'job.approval_code = ?' => $approval_code, 'status' => 3));
				   
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			$count = $result->current();
			
			return $count->count;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	 /**
	  * Delete  job and its all related data
	  * $job_id, primary key
	  */
	 public function deleteJobPacket($job_id){
	 	try{
		
			// Fetching cad milestone
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('cad' => 'de_milestone_cad'))
				   ->columns(array('id'))
				   ->where(array('cad.job_id = ?' => $job_id));
				   
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			$cadArr = $result->toArray();
			
			// Deliting images
			foreach($cadArr as $cad){
				$this->deleteMilestoneFiles($cad['id'], 1);
			}
			
			// Deleting Milestones
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_milestone_cad', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('job_id' => $job_id));
			
			// Fetching prototype milestone
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('pro' => 'de_milestone_prototype'))
				   ->columns(array('id'))
				   ->where(array('pro.job_id = ?' => $job_id));
				   
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			$prototypeArr = $result->toArray();
			
			// Deliting images
			foreach($prototypeArr as $prototype){
				$this->deleteMilestoneFiles($prototype['id'], 2);
			}
			
			// Deleting Milestones
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_milestone_prototype', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('job_id' => $job_id));
			
			// Fetching cast milestone
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('cast' => 'de_milestone_cast'))
				   ->columns(array('id'))
				   ->where(array('cast.job_id = ?' => $job_id));
				   
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			$castArr = $result->toArray();
			
			// Deliting images
			foreach($castArr as $cast){
				$this->deleteMilestoneFiles($cast['id'], 3);
			}
			
			// Deleting Milestones
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_milestone_cast', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('job_id' => $job_id));
			
			// Fetching cast milestone
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('wrkshp' => 'de_milestone_workshop'))
				   ->columns(array('id'))
				   ->where(array('wrkshp.job_id = ?' => $job_id));
				   
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			$workshopArr = $result->toArray();
			
			// Deliting images
			foreach($workshopArr as $workshop){
				$this->deleteMilestoneFiles($workshop['id'], 4);
			}
			
			// Deleting Milestones
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_milestone_workshop', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('job_id' => $job_id));
			
			// Deleting Suppliers in workshop
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_supplier_to_workshop', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('job_id' => $job_id));
			
			// Deleting Job
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_job_packet', $dbAdapter, null, $resultSetPrototype);
			return $tableGateway->delete(array('id' => $job_id));
			
		}catch(\Exception $e){echo $e->getMessage ();
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	 /**
	  * Delete milestone files
	  * $milestones_ref_id, milestone id - primary key
	  * $step, milestone type
	  */
	 public function deleteMilestoneFiles($milestones_ref_id, $step){
	 	try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('images' => 'de_milestone_images'))
				   ->columns(array('image'))
				   ->where(array('images.milestones_ref_id = ?' => $milestones_ref_id, 'step = ?' => $step));
				   
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			$imageArr = $result->toArray();
			
			$path = $this->config['documentRoot'].'milestone_attachments/';
			foreach($imageArr as $image){
				if(file_exists($path.$image['image']))
					unlink($path.$image['image']);
			}
			
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_milestone_images', $dbAdapter, null, $resultSetPrototype);
			return $tableGateway->delete(array('milestones_ref_id' => $milestones_ref_id, 'step = ?' => $step));
			
		}catch(\Exception $e){echo $e->getMessage ();
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
}