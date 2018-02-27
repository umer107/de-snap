<?php
namespace Alert\Model;

use Zend\Db\TableGateway\TableGateway;

class AlertTable
{
	protected $dbAdapter;
	protected $resultSetPrototype;
	protected $config;
	
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	private function selectAlerts(&$select, $id, $roleId) {
		$select->
		from(array('a' => 'de_alerts'))->
		columns(array('*'))->
		where('a.cleared_date is null');
		
		$where = new \Zend\Db\Sql\Where();
		$where->nest()->
		equalTo('a.target_type', 'user')->
		equalTo('a.target_id', $id)->
		unnest()->
		or->
		nest()->
		equalTo('a.target_type', 'role')->
		equalTo('a.target_id', $roleId)->
		unnest();
		$select->where->addPredicate($where);
	}
	
	
	/**
	 * fetch all alerts for the given user
	 * $limit = Number of records to be fetched
	 * $offset = Data fetch should start from
	 * $sortdatafield = optional, sort field
	 * $sortorder = optional, sort order 
	 */
	public function fetchAllForUser($limit, $offset, $id, $roleId, $sortdatafield = null, $sortorder = null)
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$this->selectAlerts($select, $id, $roleId);
					
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
		}catch(\Exception $e){echo $e->getMessage ();
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

	/**
	 * fetch all alerts
	 * $limit = Number of records to be fetched
	 * $offset = Data fetch should start from
	 * $sortdatafield = optional, sort field
	 * $sortorder = optional, sort order 
	 */
	public function fetchAll($limit, $offset, $sortdatafield = null, $sortorder = null)
	{
		try{
			
			$target = new \Zend\Db\Sql\Expression(
				"IF (a.target_type='role'," .
				"(SELECT role_name FROM de_roles WHERE role_id = a.target_id)," .
				"(SELECT CONCAT(first_name, ' ', last_name) FROM de_users WHERE user_id = a.target_id)" .
				")"
			);
		
			$select = new \Zend\Db\Sql\Select();
			$select->
			from(array('a' => 'de_alerts'))->
			columns(array('alert_id', 'target' => $target, 'created_date', 'message'))->
			where('a.cleared_date is null');
				
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
		}catch(\Exception $e){echo $e->getMessage ();
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

	/**
	 * Count unseen alerts for the given user
	 */
	public function countUnseenForUser($id, $roleId)
	{
		try{
			/* TODO: use count(*) for efficiency */
			$select = new \Zend\Db\Sql\Select();
			$this->selectAlerts($select, $id, $roleId);
			
			$adapter = $this->tableGateway->getAdapter();
			$statement = $adapter->createStatement();
			
			$select->prepareStatement($adapter, $statement);
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($statement->execute());
			
			$result['TotalRows'] = count($resultSet);
			return $result;
		}catch(\Exception $e){echo $e->getMessage ();
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Create a user level alert.
	 * Wrapper to saveAlerts.
	 */
	public function createAlert($creator_id, $target_id, $message) {
		$alertData = array(
			'created_by' => $creator_id,
			'target_type' => 'user',
			'target_id' => $target_id,
			'message' => $message,
			'created_date' => date('Y-m-d H:i:s'),
		);
		
		return $this->saveAlert($alertData);
	}
	
	/**
	 * Create a role level alert.
	 * Wrapper to saveAlerts.
	 */
	public function createRoleAlert($creator_id, $target_id, $message) {
		$alertData = array(
			'created_by' => $creator_id,
			'target_type' => 'role',
			'target_id' => $target_id,
			'message' => $message,
			'created_date' => date('Y-m-d H:i:s'),
		);
		
		return $this->saveAlert($alertData);
	}
	
	/**
	 * Save alert
	 */
	private function saveAlert($data, $updateId='')
	{
		try{
			if($updateId != ''){
				return $this->tableGateway->update($data, array('id' => $updateId));
			} else {
				$this->tableGateway->insert($data);
				return $this->tableGateway->lastInsertValue;
			}
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

	/**
	 * Clear alert
	 */
	public function clearAlert($id, $cleared_by)
	{
		try{
			return $this->tableGateway->update(
				array(
					'cleared_date' => new \Zend\Db\Sql\Expression('NOW()'),
					'cleared_by' => $cleared_by
				),
				array('alert_id' => $id)
			);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}