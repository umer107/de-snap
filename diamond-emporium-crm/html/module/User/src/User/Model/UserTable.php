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
	public function saveUser($data, $where = null)
	{
     	try{
			$user_id = $data['user_id'];
			unset($data['user_id']);
			if (empty($user_id)) {
				$data['created_date'] = date('Y-m-d H:i:s');
                               $data['user_status'] = 'Available';
			       return $this->tableGateway->insert($data);
			} else {
				if($where)
					return $this->tableGateway->update($data, $where);
				else
					return $this->tableGateway->update($data, array('user_id' => $user_id));
			}
     	}catch(\Exception $e){
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
	 * Fetch user data
	 * $where, array
	 */
	public function fetchUserDetails($where)
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('u' => 'de_users'))->columns(array('*'));
			
			if($where)
				$select->where($where);

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
	
	public function updateUser($user_id, $super_user_id){
	  
		try{
			$adapter = $this->tableGateway->getAdapter();			
			 
						
			 $sql1 = "UPDATE de_customers set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			
			$statement = $adapter->query($sql1,array($user_id));
			
			
			 $sql2 = "UPDATE de_inventory_chain set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?"; 
			$statement = $adapter->query($sql2,array($user_id));	
			
			 $sql3 = "UPDATE de_inventory_chain_consign set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql3,array($user_id)); 
			
			$sql4 = "UPDATE de_inventory_diamonds set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql4,array($user_id));
			
			$sql5 = "UPDATE de_inventory_diamonds_consign set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql5,array($user_id));
			
			$sql6 = "UPDATE de_inventory_ear_rings set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql6,array($user_id));
			
			$sql7 = "UPDATE de_inventory_earrings_consign set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql7,array($user_id));
			
			$sql8 = "UPDATE de_inventory_engagementrings_consign set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql8,array($user_id));
			
			$sql9 = "UPDATE de_inventory_miscellaneous set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql9,array($user_id));
			
			$sql10 = "UPDATE de_inventory_miscellaneous_consign set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql10,array($user_id));
			
			$sql11 = "UPDATE de_inventory_pendants set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql11,array($user_id));
			
			$sql12 = "UPDATE de_inventory_pendants_consign set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql12,array($user_id));
			
			$sql13 = "UPDATE de_inventory_weddingrings_consign set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql13,array($user_id));
			
			$sql14 = "UPDATE de_inventory_wedding_rings set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql14,array($user_id));
			
			$sql15 = "UPDATE de_invoice set created_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql15,array($user_id));
			
			$sql16 = "UPDATE de_invoice_email set created_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql16,array($user_id));
			
			$sql17 = "UPDATE de_job_packet set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql17,array($user_id));
			
			$sql18 = "UPDATE de_leads set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql18,array($user_id));
			
			$sql19 = "UPDATE de_leads set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql19,array($user_id));
			
			$sql20 = "UPDATE de_milestone_cad set created_by=$super_user_id, modified_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql20,array($user_id));
			
			//$sql21 = "UPDATE de_milestone_prototype set created_by=$super_user_id, modified_by=$super_user_id where created_by = ?";			
			//$sql22 = "UPDATE de_milestone_cast set created_by=$super_user_id, modified_by=$super_user_id where created_by = ?";			
			//$sql23 = "UPDATE de_milestone_workshop set created_by=$super_user_id, modified_by=$super_user_id where created_by = ?";
			
			
			$sql24 = "UPDATE de_notes set created_by=$super_user_id, modified_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql24,array($user_id));
			
			$sql25 = "UPDATE de_opportunities set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql25,array($user_id));
			
			$sql26 = "UPDATE de_orders set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql26,array($user_id));
			
			$sql27 = "UPDATE de_order_job_consign set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql27,array($user_id));
			
			$sql28 = "UPDATE de_suppliers set created_by=$super_user_id, updated_by=$super_user_id where created_by = ?";
			$statement = $adapter->query($sql28,array($user_id));			
			
			$sql30 = "UPDATE de_tasks set assigned_to=$super_user_id , created_by=$super_user_id, updated_by=$super_user_id  where created_by = ?";
			$statement = $adapter->query($sql30,array($user_id));	
			
			$sql29 = "INSERT INTO de_users_archive SELECT * FROM de_users where user_id = ?";
			$statement = $adapter->query($sql29,array($user_id));
			
			$sql31 = "DELETE FROM de_users where user_id = ?";
			$statement = $adapter->query($sql31,array($user_id));			    			
			
		}catch(\Exception $e){
		//echo $e->getMessage (); exit;
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
        
        /**
	 * Check User Status
	 * $status, the status to be checked
         * DateCreated : 19-9-2017	 
	 */
        
       public function updateUserStatus($data, $where = null)
	{
     	try{
			$user_id = $data['user_id'];
                        $status = $data['status'];
                        unset($data['status']);
                        $data['user_status'] = $status;
			unset($data['user_id']);
			if (empty($user_id)) {
				$data['created_date'] = date('Y-m-d H:i:s');
				return $this->tableGateway->insert($data);
			} else {
				if($where)
					return $this->tableGateway->update($data, $where);
				else
					return $this->tableGateway->update($data, array('user_id' => $user_id));
			}
     	}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
     }
        
        
}

