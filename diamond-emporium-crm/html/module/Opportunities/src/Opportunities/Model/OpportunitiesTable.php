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
	
	public function fetchAll($limit, $offset, $keyword='', $oppCustomerId='', $sortdatafield='', $sortorder='', $filter = null)
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
				   ->join(
				   		array('cus' => 'de_customers'),
				   		'opp.user_id = cus.id',
				   		array('cust_id' => 'id',
				   			  'customer_fullname' => $customerFullname,
				   			  'customer_email' => 'email', 
							  'customer_mobile' =>  'mobile'),
				   		'left')
				   ->join(
				   		array('own' => 'de_users'),
				   		'opp.record_owner_id = own.user_id',
				   		array('lead_owner' => $ownerFullname),
				   		'left')
				   ->join(
				   		array('ord' => 'de_orders'),
				   		'opp.id = ord.opp_id',
				   		array('order_id' => 'id'),
				   		'left');
				   				 
			if(!is_null($keyword) && !empty($keyword)){
				$where = new \Zend\Db\Sql\Where();
				$where->NEST->addPredicates(array(
					    //new \Zend\Db\Sql\Predicate\Like('cus.first_name', "%$keyword%"),
					    //new \Zend\Db\Sql\Predicate\Like('cus.last_name', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('opp.opportunity_name', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('cus.mobile', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('cus.email', "%$keyword%")
				    ), 'OR'
				)->UNNEST;
				$select->where->addPredicate($where);
			}
				
			if(!empty($oppCustomerId)){
				$select->where(array('opp.user_id = ?' => $oppCustomerId));
			}
			
			if(!empty($filter['filter_status'])) {
				if (!is_array($filter['filter_status'])) {
					$select->where(array('opp.opportunity_status = ?' => $filter['filter_status']));
				} else {
					$where = new \Zend\Db\Sql\Where();
					$where->addPredicates(array(
								new \Zend\Db\Sql\Predicate\In('opp.opportunity_status', $filter['filter_status']),
							));
					$select->where->addPredicate($where);
				}
			}		

			if(!empty($filter['filter_owner'])) {
				$select->where(array('opp.record_owner_id = ?' => $filter['filter_owner']));
			}		
			
			$counter = $this->executeQuery($select);
			
			if ($limit) {
				$select->limit($limit);
			}
			
			if ($offset) {
				$select->offset($offset);
			}
			
			if(!empty($sortdatafield) && !empty($sortorder)){
				$select->order("$sortdatafield $sortorder");
			}else{
				$select->order("opp.id DESC");
			}
			
//			\De\Log::logApplicationInfo ( "SQL: " . $select->getSqlString() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
			
			$data = $this->executeQuery($select);
			$result['TotalRows'] = count($counter);
			$result['Rows'] = $data->toArray();
			foreach($result['Rows'] as $key => $data){
				$notesEmbid = $this->getLatestNoteByGrid('opportunity', $data['id']);
				/*
				 * TODO: the grid system looks like it's meant to be more flexible
				 * and allow the user to add (last) note columns like any other.
				 * To start, let's just use a single column though, and concatenate
				 * the values we need.
				 * TODO: this should use the same code as LeadsTable
				 */ 
				if ($notesEmbid) {
					/* TODO: this is pretty horrible, can't we get a proper date from the DB? */
					$result['Rows'][$key]['note_description'] =
					substr($notesEmbid['created_date'], 8, 2) . '/' .
					substr($notesEmbid['created_date'], 5, 2) . '/' .
					substr($notesEmbid['created_date'], 2, 2) . ' '.
					$notesEmbid['note_type'] . ' ' .
					$notesEmbid['note_description'];
					$result['Rows'][$key]['grid_type'] = $notesEmbid['grid_type'];
					$result['Rows'][$key]['grid_type_id'] = $notesEmbid['grid_type_id'];
					$result['Rows'][$key]['note_id'] = $notesEmbid['id'];
				}
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
				   ->join(
				   		array('cus' => 'de_customers'),
				   		'opp.user_id = cus.id',
				   		array(
				   				'customer_fullname' => $customerFullname,
				   				'customer_email' => 'email', 
				   				'customer_mobile' =>  'mobile',
				   				'partner_data_id' => 'partner_id'),
				   		'left')
				   ->join(
				   		array('ref' => 'de_customers'),
				   		'opp.referred_by_customer = ref.id',
				   		array(
				   				'refered_fullname' => $referedFullname,
				   				'refered_email' => 'email', 
				  				'refered_mobile' =>  'mobile'),
				   		'left')
				   ->join(
				   		array('ord' => 'de_orders'),
				   		'opp.id = ord.opp_id',
				   		array('order_id' => 'id'),
				   		'left')
				   	->join(array('l' => 'de_leads'), 'opp.lead_id = l.lead_id', array(), 'left')
				   	->join(array('hh' => 'de_how_heard_lookup'), 'l.how_heard = hh.id', array('how_heard' => 'id', 'how_heard_title' => 'how_heard'), 'left')
				    ->where(array('opp.id = ?' => $id));
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
	
	/**
	 * Fetch opportunity type options
	 * return an array in key value pair combination
	 */
	public function fetchOpportunityTypesOptions()
	{
		try{
			$opportunity_status = new \Zend\Db\Sql\Expression('DISTINCT(o.opportunity_status)');
		
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('o' => 'de_opportunities'));
			$select->columns(array('opportunity_status' => $opportunity_status));
			$select->order('o.opportunity_status');
				
			$result = $this->executeQuery($select)->toArray();
				
			$options = array();
			foreach($result as $value){
				$options[$value['opportunity_status']] = $value['opportunity_status'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchOpportunityIdsForLead($leadId, $exclude = array()) {
		$select = new \Zend\Db\Sql\Select();
		$select->from(array('o' => 'de_opportunities'));
		$select->columns(array('id'));
		$select->where(array('o.lead_id = ?' => $leadId));
		
		$result = $this->executeQuery($select)->toArray();

		/* Loop because array_column isn't available until 5.5 */
		$out = array();
		foreach($result as $row) {
			if (!in_array($row['id'], $exclude)) {
				$out[] = $row['id'];
			}
		}
		return $out;
	}
}