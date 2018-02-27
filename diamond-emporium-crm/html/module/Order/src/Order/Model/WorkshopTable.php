<?php
/**
 *	This class is the main model for order module
 */

namespace Order\Model;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

class WorkshopTable
{
	protected $dbAdapter;
	protected $resultSetPrototype;
	protected $config;
	
	public function __construct(TableGateway $tableGateway, $serviceManager)
	{
		$this->tableGateway = $tableGateway;
		$this->serviceManager = $serviceManager;
	}
	
	/**
	 * Store order data in database
	 */
	public function saveWorkshopStep($data){
		try{
			$cadDesignTable = $this->serviceManager->get('Order\Model\CaddesignTable');
			if($data['steps_completed'] == 1){
				$data_changes = $this->tableGateway->insert($data);
				if($data_changes){
					$cadDesignTable->updateJobPacket(4, $data['steps_completed'], $data['job_id'], $data['milestone_id']);
					return $data_changes;
				}
			} else {
				$job_id = $data['job_id'];
				unset($data['job_id']);
				$data_changes = $this->tableGateway->update($data, array('milestone_id' => $data['milestone_id']));
				if($data_changes){
					$cadDesignTable->updateJobPacket(4, $data['steps_completed'], $job_id, $data['milestone_id']);
					return $data_changes;
				}
			}
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchWorkshop($job_id, $status = null){
		try{
			$reviewed_by = new \Zend\Db\Sql\Expression(
				'CONCAT(u.first_name, \' \', u.last_name)'
			);
			
			$select = new \Zend\Db\Sql\Select();
			
			$select->from(array('ws' => 'de_milestone_workshop'))
				   ->columns(array('*'))
				   ->join(array('u' => 'de_users'), 'u.user_id = ws.qa_reviewed_by', array('reviewed_by' => $reviewed_by), 'left')
				   ->join(array('msimg' => 'de_milestone_images'), new \Zend\Db\Sql\Expression('msimg.milestones_ref_id = ws.id AND msimg.milestone_type = 4 AND msimg.step = 1'), array('production_line_image' => 'image'), 'left')
				   ->where(array('ws.job_id = ?' => $job_id))
				   ->order('ws.id DESC');
				   
			if(!is_null($status))
				$select->where(array('ws.status = ?' => $status));
			
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			return $result;
		}catch(\Exception $e){echo $e->getMessage ();
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 *	Save milestine images
	 */
	function saveMilestoneImages($data){
		try{
			$milestoneFiles = new TableGateway('de_milestone_images', $this->tableGateway->getAdapter());
			
			return $milestoneFiles->insert($data);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}	
	
	/**
	 *	Save Workhop supplier data
	 */
	function saveSupplierData($data){
		try{
			$milestoneFiles = new TableGateway('de_supplier_to_workshop', $this->tableGateway->getAdapter());
			
			if($milestoneFiles->insert($data))
				return $milestoneFiles->lastInsertValue;
			else
				return false;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Increase workshop's completred step count
	 */
	public function increaseWorkshopStepCount($job_id){
		try{
			$steps_completed = new \Zend\Db\Sql\Expression(
				'steps_completed + 1'
			);
			return $this->tableGateway->update(array('steps_completed' => $steps_completed), array('job_id' => $job_id));
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch suppliers task list
	 * $job_id, $milestone_id
	 */
	 
	public function fetchWorkshopSuppliers($milestone_id = null, $id = null){
		try{
			$supplier_name = new \Zend\Db\Sql\Expression(
				'CONCAT(sup.first_name, \' \', sup.last_name)'
			);
			
			$select = new \Zend\Db\Sql\Select();
			
			$select->from(array('stw' => 'de_supplier_to_workshop'))
				   ->columns(array('id', 'milestone_id', 'tasks', 'exp_delivery_date'))
				   ->join(array('sup' => 'de_suppliers'), 'sup.id = stw.supplier_id', array('supplier_name' => $supplier_name), 'left');
				   
			if($milestone_id)
				$select->where(array('stw.milestone_id = ?' => $milestone_id));
			if($id)
				$select->where(array('stw.id = ?' => $id));
			
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
	 * Delete milestone
	 * $milestone_id
	 */
	 public function deleteMilestone($milestone_id){
	 	try{
			$this->tableGateway->delete( array('milestone_id' => $milestone_id));	
			
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_supplier_to_workshop', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('milestone_id' => $milestone_id));
			
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_job_to_milestone', $dbAdapter, null, $resultSetPrototype);
			return $tableGateway->delete(array('id' => $milestone_id));
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	 /**
	 * Update workshop cost
	 */
	public function updateTask($id, $milestone_id, $tasks){
		try{
			$milestoneFiles = new TableGateway('de_supplier_to_workshop', $this->tableGateway->getAdapter());
			return $milestoneFiles->update(array('tasks' => $tasks), array('id' => $id, 'milestone_id' => $milestone_id));
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}