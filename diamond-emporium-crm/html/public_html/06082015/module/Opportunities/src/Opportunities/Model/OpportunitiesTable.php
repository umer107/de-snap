<?php
namespace Opportunities\Model;

use Zend\Db\Sql\Where;

use Zend\Db\TableGateway\TableGateway;

class OpportunitiesTable
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
	
	public function fetchAll($limit, $offset, $keyword='', $oppCustomerId='', $sortdatafield='', $sortorder='')
	{
		try{
			$keyword = trim($keyword);
			$select = new \Zend\Db\Sql\Select();
			$customerFullname = new \Zend\Db\Sql\Expression(
				'CONCAT(cus.first_name, \' \', cus.last_name)'
			);
			$ownerFullname = new \Zend\Db\Sql\Expression(
				'CONCAT(own.first_name, \' \', own.last_name)'
			);
                            $select->from(array('opp' => 'de_opportunities'))
				   ->columns(array('*'))
				   ->join(array('cus' => 'de_customers'), 'opp.user_id = cus.id', array('cust_id' => 'id', 'customer_fullname' => $customerFullname, 'customer_email' => 'email', 
				   'customer_mobile' =>  'mobile'), 'left')
				   ->join(array('own' => 'de_users'), 'opp.created_by = own.user_id', array('account_owner' => $ownerFullname), 'left');
				  // ->join(array('notes' => 'de_notes'), "notes.grid_type='opportunity' AND opp.id=notes.grid_type_id", array('*'), 'left');
			if(!empty($keyword)){
				$where = new \Zend\Db\Sql\Where();
				$where->NEST->addPredicates(array(
					    new \Zend\Db\Sql\Predicate\Like('cus.first_name', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('cus.last_name', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('cus.mobile', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('cus.email', "%$keyword%")
				    ), 'OR'
				)->UNNEST;
				$select->where($where);
			}

			if(!empty($oppCustomerId)){
				$select->where(array('opp.user_id = ?' => $oppCustomerId));
			}

			$counter = $this->executeQuery($select);
			
			$select->limit($limit);
			$select->offset($offset);
			
			if(!empty($sortdatafield) && !empty($sortorder)){
				$select->order("$sortdatafield $sortorder");
			}else{
				$select->order("opp.id DESC");
			}
			
			$data = $this->executeQuery($select);
			$result['TotalRows'] = count($counter);
			$result['Rows'] = $data->toArray();
			foreach($result['Rows'] as $key => $data){
				$notesEmbid = $this->getLatestNoteByGrid('opportunity', $data['id']);
				$result['Rows'][$key]['note_description'] = $notesEmbid['note_description'];
				$result['Rows'][$key]['grid_type'] = $notesEmbid['grid_type'];
				$result['Rows'][$key]['grid_type_id'] = $notesEmbid['grid_type_id'];
				$result['Rows'][$key]['note_id'] = $notesEmbid['id'];
			}
			
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchOpportunityDetails($id)
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$customerFullname = new \Zend\Db\Sql\Expression(
				'CONCAT(cus.first_name, \' \', cus.last_name)'
			);
			$referedFullname = new \Zend\Db\Sql\Expression(
				'CONCAT(ref.first_name, \' \', ref.last_name)'
			);
			$ownerFullname = new \Zend\Db\Sql\Expression(
				'CONCAT(own.first_name, \' \', own.last_name)'
			);
			$select->from(array('opp' => 'de_opportunities'))
				   ->columns(array('*'))
				   ->join(array('cus' => 'de_customers'), 'opp.user_id = cus.id', array('customer_fullname' => $customerFullname, 'customer_email' => 'email', 
				   'customer_mobile' =>  'mobile', 'partner_data_id' => 'partner_id'), 'left')
				   ->join(array('ref' => 'de_customers'), 'opp.referred_by_customer = ref.id', array('refered_fullname' => $referedFullname, 'refered_email' => 'email', 
				   'refered_mobile' =>  'mobile'), 'left')
				   //->join(array('own' => 'de_users'), 'opp.record_owner_id = own.user_id', array('account_owner' => $ownerFullname), 'left')
				   ->where(array('opp.id = ?' => $id));
			//$select->where('opp.id='$id);	
			//echo $select->getSqlString(); exit;	
			$data = $this->executeQuery($select);
			$result = $data->toArray();
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function saveOpportunities($data, $updateId='')
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
	
	public function getPartnerDetails($id)
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$partnerFullname = new \Zend\Db\Sql\Expression(
				'CONCAT(cus.first_name, \' \', cus.last_name)'
			);
			$select->from(array('cus' => 'de_customers'))
				   ->columns(array('id', 'fullname' => $partnerFullname, 'email', 'mobile'))
				   ->where(array('cus.id = ?' => $id));
			//echo $select->getSqlString(); exit;	
			$data = $this->executeQuery($select);
			$result = $data->toArray();
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deleteOpportunity($where){
		try{
			if(is_array($where))
				return $this->tableGateway->delete($where);
			else
				return $this->tableGateway->delete(array('id' => $where));
		}catch(\Exception $e){
			echo $e->getMessage (); exit;
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchNotes($limit, $offset, $type, $typeId){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('notes' => 'de_notes'))
				   ->columns(array('*'));
			$select->where(array('notes.grid_type_id = ?' => $typeId, 'notes.grid_type = ?' => $type));
			$select->order('notes.id DESC');
			$data = $this->executeQuery($select);
			$result = $data->toArray();
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function getLatestNoteByGrid($gridType, $id)
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('notes' => 'de_notes'))
				   ->columns(array('*'));
			$select->where("grid_type='".$gridType."' AND grid_type_id=".$id);
			$select->order('id DESC');
			$select->limit(1);
			//echo $select->getSqlString(); exit;
			$data = $this->executeQuery($select);
			$result = $data->toArray();
			return $result[0];
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}