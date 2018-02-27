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
			$milestones = $data['milestones'];
			unset($data['milestones']);
			
			$id = $data['id'];
			unset($data['id']);
			
			if(empty($id)){
				if($this->tableGateway->insert($data)){
					$job_id = $this->tableGateway->lastInsertValue;
					
					$dbAdapter = $this->tableGateway->getAdapter();
					$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
					$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_job_to_milestone', $dbAdapter, null, $resultSetPrototype);
					
					foreach($milestones as $key => $milestone_type_id){
						$tableGateway->insert(array('job_id' => $job_id, 'milestone_type_id' => $milestone_type_id, 'milestone_order' => $key + 1));
					}
					
					return $job_id;
				}
			}else{
				$this->tableGateway->update($data, array('id' => $id));
				return $id;
			}
		}catch(\Exception $e){
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
	public function fetchAll($limit, $offset, $sortdatafield = null, $sortorder = null, $order_id = null, $cust_id = null, $keyword=null)
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
			// 'milestones', 'milestones_completed', 
			$select->from(array('ord' => 'de_orders'))
				   ->columns(array('comment', 'invoice_number', 'created_date'))
				   ->join(array('job' => 'de_job_packet'), 'ord.id = job.order_id', array('job_id' => 'id', 'exp_delivery_date', 'due_days' => $due_days, 'current_milestone_id', 'current_milestone_step_id', 'milestone_id', 'job_packet_status' => 'status'), 'inner')
				   ->join(array('dc' => 'de_order_job_consign'), 'dc.job_id = job.id', array('reserve_time', 'reserve_notes', 'tracking_id'), 'left')
				   ->join(array('isl' => 'de_inventory_status_lookup'), 'isl.id = dc.inventory_status_id', array('inventory_status_name'), 'left')
				   ->join(array('isrl' => 'de_inventory_status_reason_lookup'), 'isrl.id = dc.inventory_status_reason_id', array('inventory_status_reason'), 'left')
				   ->join(array('itl' => 'de_inventory_type_lookup'), 'itl.id = dc.inventory_type_id', array('inventory_type'), 'left')
				   ->join(array('itsl' => 'de_inventory_tracking_status_lookup'), 'itsl.id = dc.inventory_tracking_status_id', array('inventory_tracking_status'), 'left')
				   ->join(array('itrl' => 'de_inventory_tracking_reason_lookup'), 'itrl.id = dc.inventory_tracking_reason_id', array('inventory_tracking_reason'), 'left')
				   ->join(array('cust' => 'de_customers'), 'cust.id = ord.cust_id', array('customer_name' => $customer_name), 'inner')
				   ->join(array('u' => 'de_users'), 'u.user_id = dc.owner_id', array('owner_name' => $owner_name), 'left');
				   //->join(array('u' => 'de_users'), 'u.user_id = ord.created_by', array('owner_name' => $owner_name), 'left');
			if(!empty($keyword)){
				$where = new \Zend\Db\Sql\Where();
				$where->NEST->addPredicates(array(
					    new \Zend\Db\Sql\Predicate\Like('cust.first_name', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('cust.last_name', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('u.first_name', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('u.last_name', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\In('dc.tracking_id', array($keyword)),
				    ), 'OR'
				)->UNNEST;
				$select->where($where);
			}
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
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	/**
	 * Validate miestone, check all previous milestones are completed on not
	 * $milestone_id
	 * $milestone_type_id
	 * $step
	 */
	function checkMilestoneCanBeCompleted($milestone_id, $step, $isDisplay = false){
		try{
			// Fetching current milestone order
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('jtm' => 'de_job_to_milestone'))
				   ->columns(array('job_id', 'milestone_type_id', 'milestone_order'))
				   ->join(array('job' => 'de_job_packet'), 'job.id = jtm.job_id', array('job_status' => 'status'), 'inner')
				   ->where(array('jtm.id = ?' => $milestone_id));
				   
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			$currentMilestone = $result->current();
			
			if($currentMilestone->job_status == 1 || $isDisplay){
				// Fetching previous milestone id and type
				$select = new \Zend\Db\Sql\Select();
				$select->from(array('jtm' => 'de_job_to_milestone'))
					   ->columns(array('id', 'milestone_type_id'))
					   ->where(array('milestone_order < ?' => $currentMilestone->milestone_order, 'job_id' => $currentMilestone->job_id))
					   ->limit(1)
					   ->order('milestone_order DESC');
				
				$adapter = $this->tableGateway->getAdapter();				   
				$statement = $adapter->createStatement();
				$select->prepareStatement($adapter, $statement);
				
				$result = new \Zend\Db\ResultSet\ResultSet();
				$result->initialize($statement->execute());
				
				$previousMilestone = $result->current();
				
				$isPreviousMsCompleted = true;
				
				// If previous milestone exists then checking it completed or not
				if($previousMilestone){
				
					$select = new \Zend\Db\Sql\Select();
					
					if($previousMilestone->milestone_type_id == 1)
						$select->from(array('cad' => 'de_milestone_cad'));
					elseif($previousMilestone->milestone_type_id == 2)
						$select->from(array('prototype' => 'de_milestone_prototype'));
					elseif($previousMilestone->milestone_type_id == 3)
						$select->from(array('cast' => 'de_milestone_cast'));
					elseif($previousMilestone->milestone_type_id == 4)
						$select->from(array('workshop' => 'de_milestone_workshop'));				
					
					$select->columns(array('steps_completed'))
						   ->where(array('milestone_id = ?' => $previousMilestone->id));
						   
					$adapter = $this->tableGateway->getAdapter();				   
					$statement = $adapter->createStatement();
					$select->prepareStatement($adapter, $statement);
					
					$result = new \Zend\Db\ResultSet\ResultSet();
					$result->initialize($statement->execute());
					
					$previousMilestoneInfo = $result->current();
					
					if($previousMilestone->milestone_type_id == 1 && $previousMilestoneInfo->steps_completed < 3)
						$isPreviousMsCompleted = false;
					elseif($previousMilestone->milestone_type_id == 2 && $previousMilestoneInfo->steps_completed < 2)
						$isPreviousMsCompleted = false;
					elseif($previousMilestone->milestone_type_id == 3 && $previousMilestoneInfo->steps_completed < 2)
						$isPreviousMsCompleted = false;
					elseif($previousMilestone->milestone_type_id == 4 && $previousMilestoneInfo->steps_completed < 3)
						$isPreviousMsCompleted = false;
						
				}
				
				// If previous milestone does not exists then check if this step can be completed or not				
				if($isPreviousMsCompleted){
					$select = new \Zend\Db\Sql\Select();
					
					if($currentMilestone->milestone_type_id == 1)
						$select->from(array('cad' => 'de_milestone_cad'));
					elseif($currentMilestone->milestone_type_id == 2)
						$select->from(array('prototype' => 'de_milestone_prototype'));
					elseif($currentMilestone->milestone_type_id == 3)
						$select->from(array('cast' => 'de_milestone_cast'));
					elseif($currentMilestone->milestone_type_id == 4)
						$select->from(array('workshop' => 'de_milestone_workshop'));		
					
					$select->columns(array('steps_completed'))
						   ->where(array('milestone_id = ?' => $milestone_id));
					
					$adapter = $this->tableGateway->getAdapter();				   
					$statement = $adapter->createStatement();
					$select->prepareStatement($adapter, $statement);
					
					$result = new \Zend\Db\ResultSet\ResultSet();
					$result->initialize($statement->execute());
					
					$currentMilestoneInfo = $result->current();
					
					if(empty($currentMilestoneInfo) && $step == 1){
						return true;
					}else{
						if($isDisplay)
							return true;
						elseif($currentMilestoneInfo->steps_completed == $step - 1)
							return true;
					}
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
				   ->columns(array('milestone_id'))
				   ->where(array('cad.job_id = ?' => $job_id));
				   
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			$cadArr = $result->toArray();
			
			// Deliting images
			foreach($cadArr as $cad){
				$this->deleteMilestoneFiles($cad['milestone_id']);
			}
			
			// Deleting Milestones
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_milestone_cad', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('job_id' => $job_id));
			
			// Fetching prototype milestone
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('pro' => 'de_milestone_prototype'))
				   ->columns(array('milestone_id'))
				   ->where(array('pro.job_id = ?' => $job_id));
				   
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			$prototypeArr = $result->toArray();
			
			// Deliting images
			foreach($prototypeArr as $prototype){
				$this->deleteMilestoneFiles($prototype['milestone_id']);
			}
			
			// Deleting Milestones
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_milestone_prototype', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('job_id' => $job_id));
			
			// Fetching cast milestone
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('cast' => 'de_milestone_cast'))
				   ->columns(array('milestone_id'))
				   ->where(array('cast.job_id = ?' => $job_id));
				   
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			$castArr = $result->toArray();
			
			// Deliting images
			foreach($castArr as $cast){
				$this->deleteMilestoneFiles($cast['milestone_id']);
			}
			
			// Deleting Milestones
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_milestone_cast', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('job_id' => $job_id));
			
			// Fetching cast milestone
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('wrkshp' => 'de_milestone_workshop'))
				   ->columns(array('milestone_id'))
				   ->where(array('wrkshp.job_id = ?' => $job_id));
				   
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			$workshopArr = $result->toArray();
			
			// Deliting images
			foreach($workshopArr as $workshop){
				$this->deleteMilestoneFiles($workshop['milestone_id']);
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
			
			// Deleting Job to Milestones Table Data
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_job_to_milestone', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('job_id' => $job_id));
			
			// Deleting Job
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_job_packet', $dbAdapter, null, $resultSetPrototype);
			return $tableGateway->delete(array('id' => $job_id));
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	 /**
	  * Delete milestone files
	  * $milestones_ref_id, milestone id - primary key
	  * $step, milestone type
	  */
	 public function deleteMilestoneFiles($milestones_ref_id){
	 	try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('images' => 'de_milestone_images'))
				   ->columns(array('image'))
				   ->where(array('images.milestones_ref_id = ?' => $milestones_ref_id));
				   
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
			return $tableGateway->delete(array('milestones_ref_id' => $milestones_ref_id));
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	 /**
	  * Fetch milestones available for the job
	  * $job_id, primary key job packet table
	  */
	 
	 public function fetchMilestones($job_id){
	 	try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('jtm' => 'de_job_to_milestone'))
				   ->columns(array('id', 'milestone_type_id'))
				   ->where(array('jtm.job_id = ?' => $job_id))
				   ->order('jtm.milestone_order ASC');
				   
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
	  * Fetch milestones details
	  * $milestone_id, primary key of de_job_to_milestone table
	  * $milestone_type_id
	  */
	  
	 public function fetchMilestoneData($milestone_id, $milestone_type_id){
	 	try{
			$select = new \Zend\Db\Sql\Select();
			
			$supplier_name = new \Zend\Db\Sql\Expression(
				'CONCAT(sup.first_name, \' \', sup.last_name)'
			);
			if($milestone_type_id == 1){			
				$select->from(array('cad' => 'de_milestone_cad'))
					   ->columns(array('*'))
					   ->join(array('sup' => 'de_suppliers'), 'cad.supplier_id = sup.id', array('supplier_name' => $supplier_name, 'supplier_email' => 'email'), 'left')
					   ->where(array('cad.milestone_id = ?' => $milestone_id));
			}elseif($milestone_type_id == 2){			
				$select->from(array('prototype' => 'de_milestone_prototype'))
					   ->columns(array('*'))
					   ->join(array('sup' => 'de_suppliers'), 'prototype.supplier_id = sup.id', array('supplier_name' => $supplier_name, 'supplier_email' => 'email'), 'left')
					   ->where(array('prototype.milestone_id = ?' => $milestone_id));
			}elseif($milestone_type_id == 3){			
				$select->from(array('cast' => 'de_milestone_cast'))
					   ->columns(array('*'))
					   ->join(array('sup' => 'de_suppliers'), 'cast.supplier_id = sup.id', array('supplier_name' => $supplier_name, 'supplier_email' => 'email'), 'left')
					   ->where(array('cast.milestone_id = ?' => $milestone_id));
			}elseif($milestone_type_id == 4){			
				
				$reviewed_by = new \Zend\Db\Sql\Expression(
					'CONCAT(u.first_name, \' \', u.last_name)'
				);
				$select->from(array('ws' => 'de_milestone_workshop'))
					   ->columns(array('*'))
					   ->join(array('u' => 'de_users'), 'u.user_id = ws.qa_reviewed_by', array('reviewed_by' => $reviewed_by), 'left')
					   ->join(array('msimg' => 'de_milestone_images'), new \Zend\Db\Sql\Expression('msimg.milestones_ref_id = ws.milestone_id AND msimg.step = 1'), array('image'), 'left')
					   ->where(array('ws.milestone_id = ?' => $milestone_id))
					   ->order('ws.id DESC');
			}
			
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
	 * Get Media Files for Milestone
	 */
	function fetchMilestoneFiles($milestones_ref_id, $step = null){
		try{
			$select = new \Zend\Db\Sql\Select();
						
			$select->from(array('img' => 'de_milestone_images'))
				   ->columns(array('milestones_ref_id', 'step', 'image'))
				   ->where(array('img.milestones_ref_id = ?' => $milestones_ref_id));
			if($step)
				$select->where(array('img.step = ?' => $step));
			
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
	  * Save attachment files
	  * $milestone_type, $milestones_ref_id, $step, $files = json data
	  */
	 public function saveAttachedFiles($milestone_type, $milestones_ref_id, $step, $files){
	 	try{
			$attchmentTable = new TableGateway('de_milestone_images', $this->tableGateway->getAdapter());
			
			$data = array('milestone_type' => $milestone_type, 'milestones_ref_id' => $milestones_ref_id, 'step' => $step);
			foreach($files as $key => $value){
				$data['image'] = $value;
				$attchmentTable->insert($data);
			}
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	 /**
	  * Add milestone
	  * $current_milestone_id, which milestone add button is clicked
	  * $job_id, primary key job packet table
	  * $milestone_type_id
	  */
	 public function addMilestone($current_milestone_id, $job_id, $milestone_type_id){
	 	try{
			$milestoneTable = new TableGateway('de_job_to_milestone', $this->tableGateway->getAdapter());
			
			$select = new \Zend\Db\Sql\Select();			
			$select->from(array('jtm' => 'de_job_to_milestone'))
				   ->columns(array('milestone_order'))
				   ->where(array('jtm.id = ?' => $current_milestone_id));
			
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			$data = $result->current();
			
			$milestoneTable->update(array('milestone_order' =>  new \Zend\Db\Sql\Expression('milestone_order + 1')), array('job_id = ?' => $job_id, 'milestone_order > ?' => $data->milestone_order));
			
			$milestoneTable->insert(array('job_id' => $job_id, 'milestone_type_id' => $milestone_type_id, 'milestone_order' => $data->milestone_order + 1));
			
			return $milestoneTable->lastInsertValue;
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	/**
	 * Validate if miestone can be added on not
	 * $current_milestone_id
	 * $current_milestone_type_id
	 */
	function checkMilestoneCanBeAdded($current_milestone_id){
		try{
			// Fetching current milestone steps_completed and order
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('jtm' => 'de_job_to_milestone'))
				   ->columns(array('milestone_type_id', 'milestone_order'))
				   ->join(array('job' => 'de_job_packet'), 'job.id = jtm.job_id', array('job_status' => 'status'), 'inner')
				   ->where(array('jtm.id = ?' => $current_milestone_id));
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			$currentMilestoneInfo = $result->current();
			
			if($currentMilestoneInfo->job_status == 1){
			
				$current_milestone_type_id = $currentMilestoneInfo->milestone_type_id;
				
				$select = new \Zend\Db\Sql\Select();
				
				if($current_milestone_type_id == 1)
					$select->from(array('cad' => 'de_milestone_cad'));
				elseif($current_milestone_type_id == 2)
					$select->from(array('prototype' => 'de_milestone_prototype'));
				elseif($current_milestone_type_id == 3)
					$select->from(array('cast' => 'de_milestone_cast'));
				elseif($current_milestone_type_id == 4)
					$select->from(array('workshop' => 'de_milestone_workshop'));
				
				$select->columns(array('job_id', 'steps_completed'))
					   ->where(array('milestone_id = ?' => $current_milestone_id));
					   
				$adapter = $this->tableGateway->getAdapter();				   
				$statement = $adapter->createStatement();
				$select->prepareStatement($adapter, $statement);
				
				$result = new \Zend\Db\ResultSet\ResultSet();
				$result->initialize($statement->execute());
				
				$currentMilestone = $result->current();
				
				$currentMilestoneIsCompleted = false;
				
				if($current_milestone_type_id == 1 && $currentMilestone->steps_completed == 3){
					$currentMilestoneIsCompleted = true;
				}elseif($current_milestone_type_id == 2 && $currentMilestone->steps_completed == 2){
					$currentMilestoneIsCompleted = true;
				}elseif($current_milestone_type_id == 3 && $currentMilestone->steps_completed == 2){
					$currentMilestoneIsCompleted = true;
				}elseif($current_milestone_type_id == 4 && $currentMilestone->steps_completed == 3){
					$currentMilestoneIsCompleted = true;
				}
				
				if($currentMilestoneIsCompleted){
					// Fetching next milestone steps_completed
					$select = new \Zend\Db\Sql\Select();
					$select->from(array('jtm' => 'de_job_to_milestone'))
						   ->columns(array('id', 'milestone_type_id'))
						   ->where(array('milestone_order > ?' => $currentMilestoneInfo->milestone_order, 'job_id' => $currentMilestone->job_id))
						   ->limit(1)
						   ->order('milestone_order ASC');
					
					$adapter = $this->tableGateway->getAdapter();				   
					$statement = $adapter->createStatement();
					$select->prepareStatement($adapter, $statement);
					
					$result = new \Zend\Db\ResultSet\ResultSet();
					$result->initialize($statement->execute());
					
					$nextMilestoneInfo = $result->current();
					
					if(empty($nextMilestoneInfo)){
						return true;
					}else{
						$select = new \Zend\Db\Sql\Select();
						
						if($nextMilestoneInfo->milestone_type_id == 1)
							$select->from(array('cad' => 'de_milestone_cad'));
						elseif($nextMilestoneInfo->milestone_type_id == 2)
							$select->from(array('prototype' => 'de_milestone_prototype'));
						elseif($nextMilestoneInfo->milestone_type_id == 3)
							$select->from(array('cast' => 'de_milestone_cast'));
						elseif($nextMilestoneInfo->milestone_type_id == 4)
							$select->from(array('workshop' => 'de_milestone_workshop'));
							
						$select->columns(array('steps_completed'))
							   ->where(array('milestone_id = ?' => $nextMilestoneInfo->id));
							   
						$adapter = $this->tableGateway->getAdapter();				   
						$statement = $adapter->createStatement();
						$select->prepareStatement($adapter, $statement);
						
						$result = new \Zend\Db\ResultSet\ResultSet();
						$result->initialize($statement->execute());
						
						$nextMilestone = $result->current();
						
						if(empty($nextMilestone)) // If next milestone is not started yet then add milestone
							return true;
						
						/*if(!empty($nextMilestone)){
							if($nextMilestoneInfo->milestone_type_id == 1 && $nextMilestone->steps_completed == 3){
								return true;
							}elseif($nextMilestoneInfo->milestone_type_id == 2 && $nextMilestone->steps_completed == 2){
								return true;
							}elseif($nextMilestoneInfo->milestone_type_id == 3 && $nextMilestone->steps_completed == 2){
								return true;
							}elseif($nextMilestoneInfo->milestone_type_id == 4 && $nextMilestone->steps_completed == 3){
								return true;
							}
						}*/
					}
				}
			}
			
			return false;
						
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Change job status
	 * $job_id, primary_key
	 * $status, status to be set
	 */	 
	 public function changeJobStatus($job_id, $status){
	 	try{
						
			return $this->tableGateway->update(array('status' => $status), array('id' => $job_id, 'status > ?' => 0));
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	 /**
	  * Check if milestone can be deleted or not
	  * $milestone_id
	  */
	 function checkMilestoneCanBeDeleted($milestone_id){
	 	try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('jtm' => 'de_job_to_milestone'))
				   ->columns(array())
				   ->join(array('job' => 'de_job_packet'), 'job.id = jtm.job_id', array('job_status' => 'status'), 'inner')
				   ->where(array('jtm.id = ?' => $milestone_id));
				   
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			$resultData = $result->current();
			
			if($resultData->job_status == 1)
				return true;
			else
				return false;
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	 /**
	  * get milestones completed with respect to steps
	  */
	 function getMilestoneCompleted($job_id){
	 	try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('jtm' => 'de_job_to_milestone'))
				   ->columns(array())
				   ->join(array('job' => 'de_job_packet'), 'job.id = jtm.job_id', array('job_status' => 'status'), 'inner')
				   ->where(array('jtm.id = ?' => $milestone_id));
				   
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			$resultData = $result->current();
			
			if($resultData->job_status == 1)
				return true;
			else
				return false;
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	 /**
	 * Fetch Suppllier Data by MilestoneId
	 */
	public function getSupplierNameByMilestoneId($milestoneId, $milestoneType)
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$sup_name = new \Zend\Db\Sql\Expression(
				'CONCAT(sup.first_name, \' \', sup.last_name)'
			);
			
			if($milestoneType == 1){
				$select->from(array('cad' => 'de_milestone_cad'))
				   ->columns(array('supplier_id'))
				   ->join(array('sup' => 'de_suppliers'), 'cad.supplier_id = sup.id', array('supplier_name' => $sup_name), 'left');
			} else if($milestoneType == 2){
				$select->from(array('cad' => 'de_milestone_prototype'))
				   ->columns(array('supplier_id'))
				   ->join(array('sup' => 'de_suppliers'), 'cad.supplier_id = sup.id', array('supplier_name' => $sup_name), 'left');
			} else if($milestoneType == 3){
				$select->from(array('cad' => 'de_milestone_cast'))
				   ->columns(array('supplier_id'))
				   ->join(array('sup' => 'de_suppliers'), 'cad.supplier_id = sup.id', array('supplier_name' => $sup_name), 'left');
			} else if($milestoneType == 4){
				$select->from(array('cad' => 'de_milestone_cad'))
				   ->columns(array('supplier_id'))
				   ->join(array('sup' => 'de_suppliers'), 'cad.supplier_id = sup.id', array('supplier_name' => $sup_name), 'left');
			}
			$select->where(array('cad.milestone_id = ?' => $milestoneId));
			//echo $select->getSqlString(); exit;
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
	 * Fetch Completed Milestones By Job ID
	 */
	public function getMilestonesByJobId($jobId)
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('milestone' => 'de_job_to_milestone'))
			   ->columns(array('*'));
			$select->where(array('milestone.job_id = ?' => $jobId, 'status' => 1));
			//echo $select->getSqlString(); exit;
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			return count($result);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function saveMilestoneEmail($insetData){
	 	try{
			$tableMilestoneEmail = new TableGateway('de_milestone_email', $this->tableGateway->getAdapter());
		
			return $tableMilestoneEmail->insert($insetData);
		} catch (Exception $e) {
		    \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
	}
	
	/**
	 *
	 */	 
	 public function fetchAllEmail($limit, $offset, $sortdatafield = null, $sortorder = null, $customer_id = null, $keyword = null){
	 	try{
			$select = new \Zend\Db\Sql\Select();
						
			$resultSetPrototype = new HydratingResultSet();
			
			$tableGateway = new TableGateway($this->config["dbPrefix"] . 'milestone_email', $this->tableGateway->getAdapter(), null, $resultSetPrototype);
			
			$supplier_name = new \Zend\Db\Sql\Expression(
				'CONCAT(s.first_name, \' \', s.last_name)'
			);
	
			$select->from(array('me' => 'de_milestone_email'))
					->columns(array('*'))
					->join(array('s' => 'de_suppliers'), new \Zend\Db\Sql\Expression('s.id = me.supplier_id'), array('supplier_name' => $supplier_name, 'supplier_email' => 'email'), 'left');
			
			if(!empty($keyword)){
				$where = new \Zend\Db\Sql\Where();
				$where->NEST->addPredicates(array(
						new \Zend\Db\Sql\Predicate\Like('me.subject', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('me.message', "%$keyword%"),
					), 'OR'
				)->UNNEST;
				/*$where->addPredicates(array(
					new \Zend\Db\Sql\Predicate\Like('u.first_name', "%$keyword%"),
				));*/
				$select->where($where);
			}
			
			if(!empty($supplier_id))
				$select->where(array('s.id = ?' => $supplier_id));
			
			if(!empty($sortdatafield) && !empty($sortorder)){
				if($sortdatafield == 'created_date' || $sortdatafield == 'created_time')
					$select->order("me.created_date $sortorder");
				elseif($sortdatafield == 'subject')
					$select->order("me.subject $sortorder");
				elseif($sortdatafield == 'id')
					$select->order("me.id $sortorder");
			} else {
				$select->order('me.id DESC');
			}
			//echo $select->getSqlString();exit;
			$statement = $this->tableGateway->getAdapter()->createStatement();			
			$select->prepareStatement($this->tableGateway->getAdapter(), $statement);
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($statement->execute());
			
			$select->limit($limit);
			$select->offset($offset);
			
			$statement = $this->tableGateway->getAdapter()->createStatement();
			$select->prepareStatement($this->tableGateway->getAdapter(), $statement);
			$resultSetLimit = new \Zend\Db\ResultSet\ResultSet();
			$resultSetLimit->initialize($statement->execute());
			
			$result['TotalRows'] = count($resultSet);
			$result['Rows'] = $resultSetLimit->toArray();
			
			return $result;
		} catch (Exception $e) {echo $e->getMessage();
		    \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
	 }
	 
	/**
	 * Fetch email details
	 * $id, primary key
	 */
	public function fetchEmail($id){
		try{
			$select = new \Zend\Db\Sql\Select();
						
			$resultSetPrototype = new HydratingResultSet();
			
			$tableGateway = new TableGateway($this->config["dbPrefix"] . 'milestone_email', $this->tableGateway->getAdapter(), null, $resultSetPrototype);
			
			$supplier_name = new \Zend\Db\Sql\Expression(
				'CONCAT(s.first_name, \' \', s.last_name)'
			);
	
			$select->from(array('me' => 'de_milestone_email'))
					->columns(array('*'))
					->join(array('s' => 'de_suppliers'), new \Zend\Db\Sql\Expression('s.id = me.supplier_id'), array('supplier_name' => $supplier_name, 'supplier_email' => 'email'), 'left')
					->where(array('me.id = ?' => $id));
			//echo $select->getSqlString();exit;
			$statement = $this->tableGateway->getAdapter()->createStatement();			
			$select->prepareStatement($this->tableGateway->getAdapter(), $statement);
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($statement->execute());			
			
			return $resultSet->current();
		} catch (Exception $e) {
		    \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
	}
	
	/**
	 * Fetch previous milestone data
	 * $milestone_id, current milestone id
	 */
	 
	 public function getPreviousMilestone($milestone_id){
		try{
			$current_milestone_id = 0;
			$current_milestone_type_id = 0;
			$current_milestone_step_id = 0;
		
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('job' => 'de_job_packet'))
				   ->columns(array('job_id' => 'id', 'milestone_id', 'current_milestone_id'))
				   ->join(array('ms' => 'de_job_to_milestone'), 'ms.job_id = job.id', array('milestone_order'), 'left')
				   ->where(array('ms.id = ?' => $milestone_id));
				   
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			$currentMilestoneInfo = $result->current();
			
			if(!empty($currentMilestoneInfo) && $currentMilestoneInfo->milestone_id == $milestone_id){
				$select = new \Zend\Db\Sql\Select();
				$select->from(array('ms' => 'de_job_to_milestone'))
					   ->columns(array('id', 'milestone_type_id'))
					   ->where(array('ms.milestone_order < ?' => $currentMilestoneInfo->milestone_order, 'ms.job_id = ?' => $currentMilestoneInfo->job_id))
					   ->limit(1)
					   ->order('milestone_order DESC');
				
				$adapter = $this->tableGateway->getAdapter();				   
				$statement = $adapter->createStatement();
				$select->prepareStatement($adapter, $statement);
				
				$result = new \Zend\Db\ResultSet\ResultSet();
				$result->initialize($statement->execute());
				
				$previousMilestoneInfo = $result->current();
				
				if(!empty($previousMilestoneInfo)){
					$current_milestone_id = $previousMilestoneInfo->id;
					$current_milestone_type_id = $previousMilestoneInfo->milestone_type_id;
					
					if($previousMilestoneInfo->milestone_type_id == 1){
						$current_milestone_step_id = 3;
					} else if($previousMilestoneInfo->milestone_type_id == 2){
						$current_milestone_step_id = 2;
					} else if($previousMilestoneInfo->milestone_type_id == 3){
						$current_milestone_step_id = 2;
					} else if($previousMilestoneInfo->milestone_type_id == 4){
						$current_milestone_step_id = 3;
					}
				}
			}
			
			$data = array('current_milestone_id' => $current_milestone_id, 'current_milestone_type_id' => $current_milestone_type_id,
						  'current_milestone_step_id' => $current_milestone_step_id, 'job_id' => $currentMilestoneInfo->job_id);
			
			return $data;
			
		} catch (Exception $e) {echo $e->getMessage();
		    \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
	 }
	 
	 /**
	  * Update job data
	  * $id = job id, $data = array
	  */
	  
	  public function updateJobPacket($id, $data){
	  	try{
			return $this->tableGateway->update($data, array('id' => $id));
		} catch (Exception $e) {echo $e->getMessage();
		    \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
	  }
}