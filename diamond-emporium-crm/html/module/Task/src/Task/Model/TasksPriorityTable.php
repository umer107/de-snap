<?php
namespace Task\Model;

use Zend\Db\Sql\Where;

use Zend\Db\TableGateway\TableGateway;

class TasksPriorityTable
{
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}
	
	public function executeQuery($select){
		$adapter = $this->tableGateway->getAdapter();
		$statement = $adapter->createStatement();
		$select->prepareStatement($adapter, $statement);
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet;
	}
	
	public function fetchAll()
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('t' => 'de_tasks_priority'))
				   ->columns(array('priority_id' => 'id', 'priority_title' => 'title'));
			
			$adapter = $this->tableGateway->getAdapter();
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($statement->execute());
			
			$result = $resultSet->toArray();
			
			return $result;
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}