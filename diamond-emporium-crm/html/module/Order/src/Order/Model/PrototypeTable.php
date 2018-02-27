<?php
/**
 *	This class is the main model for order module
 */

namespace Order\Model;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

class PrototypeTable
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
	public function savePrototypeStep($data){
		try{
			$cadDesignTable = $this->serviceManager->get('Order\Model\CaddesignTable');
			if($data['steps_completed'] == 1){
				$data_changes = $this->tableGateway->insert($data);
				if($data_changes){
					$cadDesignTable->updateJobPacket(2, $data['steps_completed'], $data['job_id'], $data['milestone_id']);
					return $data_changes;
				}
			} else {
				$job_id = $data['job_id'];
				unset($data['job_id']);
				$data_changes = $this->tableGateway->update($data, array('milestone_id' => $data['milestone_id']));
				if($data_changes){
					$cadDesignTable->updateJobPacket(2, $data['steps_completed'], $job_id, $data['milestone_id']);
					return $data_changes;
				}
			}
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchPrototype($job_id){
		try{
			$supplier_name = new \Zend\Db\Sql\Expression(
				'CONCAT(sup.first_name, \' \', sup.last_name)'
			);
			
			$select = new \Zend\Db\Sql\Select();
			
			$select->from(array('proto' => 'de_milestone_prototype'))
				   ->columns(array('*'))
				   ->join(array('sup' => 'de_suppliers'), 'sup.id = proto.supplier_id', array('supplier_name' => $supplier_name), 'left')
				   ->where(array('proto.job_id = ?' => $job_id))
				   ->order('id DESC')
				   ->limit(1);
			
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			return $result->current();
		}catch(\Exception $e){echo $e->getMessage ();
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
			
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_job_to_milestone', $dbAdapter, null, $resultSetPrototype);
			return $tableGateway->delete(array('id' => $milestone_id));
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	 /**
	  * Update milestone
	  * $data = array
	  * $where = array
	  */
	 public function updateMilestone($data, $where){
	 	try{
			return $this->tableGateway->update($data, $where);			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
}