<?php
namespace User\Model;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

class UserTable
{
	protected $dbAdapter;
	protected $resultSetPrototype;
	protected $config;
	
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}
	
	/**
	 * Store user data in db
	 * $data, user datain array format
	 */
	public function saveUser($data)
	{
     	try{
			$user_id = $data['user_id'];
			unset($data['user_id']);
			if (empty($user_id)) {print_r($data);
				$data['created_date'] = date('Y-m-d H:i:s');
				return $this->tableGateway->insert($data);
			} else {
				return $this->tableGateway->update($data, array('user_id' => $user_id));
			}
     	}catch(\Exception $e){echo $e->getMessage ();
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
     }
	 
	/**
	 * fetch all the users
	 * $limit = Number of records to be fetched
	 * $offset = Data fetch should start from
	 * $sortdatafield = optional, sort field
	 * $sortorder = optional, sort order 
	 */
	public function fetchAll($limit, $offset, $keyword = null,  $sortdatafield = null, $sortorder = null)
	{
		try{
			$keyword = trim($keyword);
			$select = new \Zend\Db\Sql\Select();
			
			$select->from(array('u' => 'de_users'))->columns(array('*'));
			/*if(!empty($keyword)){
				$where = new \Zend\Db\Sql\Where();
				$where->NEST->addPredicates(array(
					    new \Zend\Db\Sql\Predicate\Like('su.first_name', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('su.last_name', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('u.first_name', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('u.last_name', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\In('dwc.tracking_id', array($keyword)),
						new \Zend\Db\Sql\Predicate\In('chain.stock_code', array($keyword)),
				    ), 'OR'
				)->UNNEST;
				$select->where($where);
			}*/
			
			if(!empty($sortdatafield) && !empty($sortorder)){
				if($sortdatafield == 'first_name')
					$select->order("u.first_name $sortorder");
				elseif($sortdatafield == 'last_name')
					$select->order("u.last_name $sortorder");
				elseif($sortdatafield == 'email')
					$select->order("u.email $sortorder");
				elseif($sortdatafield == 'mobile_number')
					$select->order("u.mobile_number $sortorder");
			}else{
				$select->order("u.user_id DESC");
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
		}catch(\Exception $e){echo $e->getMessage ();
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch user data by user id
	 * $id, is id of the user
	 */
	public function fetchUserById($id)
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('u' => 'de_users'))->columns(array('*'))
				   ->where(array('u.user_id = ?' => $id));

			$adapter = $this->tableGateway->getAdapter();
			$statement = $adapter->createStatement();
			
			$select->prepareStatement($adapter, $statement);
			
			$resultSetLimit = new \Zend\Db\ResultSet\ResultSet();
			$resultSetLimit->initialize($statement->execute());
			$result = $resultSetLimit->current();
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Check duplicate email in user table
	 * $email, the email to be checked
	 * $user_id, optional, exclude the user id
	 */
	public function checkDuplicateEmail($email, $user_id = null){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('u' => 'de_users'))->columns(array('*'))
				   ->columns(array('counter' => new \Zend\Db\Sql\Expression('COUNT(user_id)')))
				   ->where(array('u.email = ?' => $email));
				   
			if($user_id)
				$select->where(array('u.user_id != ?' => $user_id));

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
	
	public function saveMasterPassword($password){
		try{
			$tableGateway = new TableGateway('de_master_password', $this->tableGateway->getAdapter());
			return $tableGateway->update(array('password' => md5($password)));
		}catch(\Exception $e){echo  $e->getMessage ();exit;
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}