<?php
namespace Customer\Model;

use Zend\Db\TableGateway\TableGateway;

class LeadsTable
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
	
	public function fetchAll($limit, $offset, $keyword='', $sortdatafield='', $sortorder='')
	{
		try{
			$keyword = trim($keyword);
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_leads')->columns(array('lead_id'));
			if(!empty($keyword)){
				$where = new \Zend\Db\Sql\Where();
				// Using predicates
				$where->addPredicates(array(
					    new \Zend\Db\Sql\Predicate\Like('first_name', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('last_name', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('mobile', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('email', "%$keyword%")
				    ), 'OR'
				);
				
				$select->where($where);
			}
			
			$counter = $this->tableGateway->selectWith($select);
			
			
			$fullname = new \Zend\Db\Sql\Expression(
				'CONCAT(u.first_name, \' \', u.last_name)'
			);
			
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('l' => 'de_leads'))
				   ->columns(array('lead_id', 'title', 'first_name', 'last_name', 'email', 'mobile', 'budget', 'priority', 'created_date'))
				   ->join(array('p' => 'de_products'), 'l.product = p.id', array('product' => 'title'), 'left')
				   ->join(array('u' => 'de_users'), 'l.lead_owner = u.user_id', array('lead_owner' => $fullname), 'left')
				   ->limit($limit)
				   ->offset($offset);
				   
			if(!empty($keyword)){
				$where = new \Zend\Db\Sql\Where();
				// Using predicates
				$where->addPredicates(array(
					    new \Zend\Db\Sql\Predicate\Like('l.first_name', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('l.last_name', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('l.mobile', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('l.email', "%$keyword%")
				    ), 'OR'
				);
				
				$select->where($where);
			}
				   
			if(!empty($sortdatafield) && !empty($sortorder)){
				if(in_array($sortdatafield, array('title', 'first_name', 'last_name', 'email', 'mobile', 'budget', 'priority', 'created_date')))
					$select->order("l.$sortdatafield $sortorder");
				elseif(in_array($sortdatafield, array('product')))
					$select->order("$sortdatafield $sortorder");
				elseif(in_array($sortdatafield, array('lead_owner')))
					$select->order("$sortdatafield $sortorder");
			}
			
			$adapter = $this->tableGateway->getAdapter();
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($statement->execute());			
			$data = $resultSet->toArray();
			
			$result['TotalRows'] = count($counter);
			$result['Rows'] = $data;
			foreach($result['Rows'] as $key => $data){
				$notesEmbid = $this->getLatestNoteByGrid('lead', $data['lead_id']);
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
	
	public function saveLead($data)
	{
     	try{
			$lead_id = (int) $data['lead_id'];
			if (empty($lead_id)) {
				$this->tableGateway->insert($data);
				return $this->tableGateway->lastInsertValue;
			} else {
				return $this->tableGateway->update($data, array('lead_id' => $lead_id));
			}
     	}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
     }
     
     public function fetchLeadDetails($lead_id){
     	try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('l' => 'de_leads'))
				   ->columns(array('*'))
				   ->join(array('lt' => 'de_lead_types'), 'l.lead_type = lt.id', array('lead_type' => 'id', 'lead_type_title' => 'title'), 'inner')
				   ->join(array('s' => 'de_states'), 'l.state = s.id', array('name'), 'inner')
				   ->join(array('p' => 'de_products'), 'l.product = p.id', array('product' => 'id', 'product_title' => 'title'), 'left')
				   ->join(array('c' => 'de_customers'), 'l.referred_by_customer = c.id', array('customer_id' => 'id', 'cust_first_name' => 'first_name', 'cust_last_name' => 'last_name'), 'left')
				   ->join(array('u' => 'de_users'), 'l.lead_owner = u.user_id', array('user_id', 'owner_first_name' => 'first_name', 'owner_last_name' => 'last_name'), 'left')
				   ->where(array('l.lead_id = ?' => $lead_id))
				   ->group('l.lead_id');
			
			$adapter = $this->tableGateway->getAdapter();
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($statement->execute());
			
			return $resultSet->current();
     	}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
     }
	 
	public function listColumns($mode)
	{
		try{
			if($mode == 'leads'){
				$result = $this->getLeadsColumns();
			} else if($mode == 'customers'){
				$result = $this->getCustomersColumns();
			} else if($mode == 'opportunities'){
				$result = $this->getOpportunitiesColumns();
			} else if($mode == 'suppliers'){
				$result = $this->getSuppliersColumns();
			}
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function getLeadsColumns(){
		$list = array();
		$list['created_date'] = 'Date Created';
		//$list['created_date'] = 'Status';
		//$list['created_date'] = 'Last Contacted';
		$list['first_name'] = 'First Name';
		$list['last_name'] = 'Last Name';
		$list['mobile'] = 'Mobile Number';
		$list['email'] = 'Email';
		$list['product'] = 'Product';
		$list['budget'] = 'Budget';
		$list['priority'] = 'Priority';
		//$list['priority'] = 'Notes';
		//$list['priority'] = 'Edit Notes';
		//$list['priority'] = 'Follow up Date';
		$list['note_description'] = 'Notes';
		$list['note_id'] = 'Edit Notes';
		$list['lead_owner'] = 'Lead Owner';
		return $list;
	}
	
	public function getCustomersColumns(){
		$list = array();
		$list['fullname'] = 'Customer Name';
		$list['mobile'] = 'Phone Number';
		$list['email'] = 'Email';
		$list['state_code'] = 'State';
		$list['partner_fullname'] = 'Partner Name';
		$list['owner_fullname'] = 'Customer Owner';
		return $list;
	}
	
	public function getOpportunitiesColumns(){
		$list = array();
		$list['created_date'] = 'Created';
		$list['customer_fullname'] = 'Customer Owner';
		$list['customer_email'] = 'Email';
		$list['customer_mobile'] = 'Mobile Number';
		$list['urgency'] = 'Urgency';
		$list['rating'] = 'Rating';
		$list['progress_of_opportunity'] = 'Progress';
		$list['probability'] = 'Probability';
		$list['budget'] = 'Est. Revenue';
		$list['est_close_date'] = 'Predicted Close Date';
		$list['looking_for'] = 'Notes';
		$list['product'] = 'Edit Notes';
		$list['preferred_contact'] = 'Follow up Date';
		$list['note_description'] = 'Notes';
		$list['note_id'] = 'Edit Notes';
		$list['account_owner'] = 'Account Owner';
		return $list;
	}
	
	public function getSuppliersColumns(){
		$list = array();
		$list['created_date'] = 'Date Created';
		$list['company_name'] = 'Company Name';
		$list['first_name'] = 'First Name';
		$list['last_name'] = 'Last Name';
		$list['email'] = 'Email';
		$list['phone'] = 'Number';
		$list['mobile'] = 'Mobile No.';
		$list['service_name'] = 'Services Offered';
		return $list;
	}
	
	public function deleteLead($lead_id){
		try{
			return $this->tableGateway->delete(array('lead_id' => $lead_id));
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
	
	public function getLeadDetails($lead_id){
     	try{
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_leads')
				   ->columns(array('*'))
				    ->where(array('lead_id' => $lead_id));
			
			$result = $this->tableGateway->selectWith($select)->toArray();
			return $result[0];
     	}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
     }
}