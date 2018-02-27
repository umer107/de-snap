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
	
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}
	
	/**
	 * Store order data in database
	 */
	public function savePrototypeStep($data){
		try{
			$id = $data['id'];
			unset($data['id']);
			
			if(!empty($id)){
				$this->tableGateway->update($data, array('id' => $id));
				return $id;
			}else{
				if($this->tableGateway->insert($data)){
					$id = $this->tableGateway->lastInsertValue;
					
					return $id;
				}
			}
			
			return 0;
		}catch(\Exception $e){echo $e->getMessage ();
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
}