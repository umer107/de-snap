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
	
	public function fetchAll($limit, $offset, $keyword='', $sortdatafield='', $sortorder='', $filter = null)
	{
		try{
			$fullname = new \Zend\Db\Sql\Expression(
				'CONCAT(u.first_name, \' \', u.last_name)'
			);
			
			$snote = new \Zend\Db\Sql\Expression('(select replace(substring(max(n.follow_up_date), 1, 10), \'-\', \'/\') as note_follow_up_date from de_notes n where n.grid_type = \'lead\' and n.grid_type_id = l.lead_id)');
			
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('l' => 'de_leads'))
				   ->columns(array(
				   		'lead_id', 'title', 'first_name', 'last_name', 'email', 'mobile', 'budget', 'priority', 'created_date',
				   		'note_follow_up_date' => $snote
				   ))
				   ->join(array('p' => 'de_products'), 'l.product = p.id', array('product' => 'title'), 'left')
				   ->join(array('u' => 'de_users'), 'l.lead_owner = u.user_id', array('lead_owner' => $fullname), 'left');				   

			$keyword = trim($keyword);
			if(!empty($keyword)){
				$where = new \Zend\Db\Sql\Where();
				// Using predicates
				$where->nest()->addPredicates(array(
					    new \Zend\Db\Sql\Predicate\Like('l.first_name', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('l.last_name', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('l.mobile', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('l.email', "%$keyword%")
				    ), 'OR'
				)->unnest();
				
				$select->where($where);
			}
			
			if(!empty($filter['filter_owner'])) {
				$select->where(array('l.lead_owner = ?' => $filter['filter_owner']));
			}
							   
			if(!empty($filter['filter_status'])) {
				$select->where(array('l.lead_status = ?' => $filter['filter_status']));
			}
			
			
			$counter = $this->executeQuery($select);
			
			$select->limit($limit);
			$select->offset($offset);
			
			if(!empty($sortdatafield) && !empty($sortorder)){
				/* We always want nulls last, so for 'asc' change to 'desc' but put a minus sign on the column */
				if(in_array($sortdatafield, array('title', 'first_name', 'last_name', 'email', 'mobile', 'budget', 'priority', 'created_date')))
					$select->order(new \Zend\Db\Sql\Expression("case when l.$sortdatafield is null then 1 else 0 end, l.$sortdatafield $sortorder"));
				elseif(in_array($sortdatafield, array('product', 'note_follow_up_date', 'lead_owner')))
					$select->order(new \Zend\Db\Sql\Expression("case when $sortdatafield is null then 1 else 0 end, $sortdatafield $sortorder"));
			}
			
			$data = $this->executeQuery($select);
			$result['TotalRows'] = count($counter);
			$result['Rows'] = $data->toArray();
			foreach($result['Rows'] as $key => $data){
				$notesEmbid = $this->getLatestNoteByGrid('lead', $data['lead_id']);
				/*
				 * TODO: the grid system looks like it's meant to be more flexible
				 * and allow the user to add (last) note columns like any other.
				 * To start, let's just use a single column though, and concatenate
				 * the values we need.
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
				   ->join(array('hh' => 'de_how_heard_lookup'), 'l.how_heard = hh.id', array('how_heard' => 'id', 'how_heard_title' => 'how_heard'), 'left')
				   ->join(array('s' => 'de_states'), 'l.state = s.id', array('name'), 'left')
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
		$list['opportunity_name'] = 'Opportunity Name';
		$list['customer_mobile'] = 'Mobile Number';
		$list['rating'] = 'Rating';
		$list['budget'] = 'Est. Revenue';
		$list['est_close_date'] = 'Predicted Close Date';
		$list['note_description'] = 'Notes';
		$list['note_id'] = 'Edit Notes';
		$list['preferred_contact'] = 'Follow up Date';
		$list['lead_owner'] = 'Lead Owner';
		$list['opportunity_status'] = 'Status';
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

     /**
	 * Fetch lead type options
	 * return an array in key value pair combination
	 */
	public function fetchLeadTypesOptions()
	{
		try{
			$opportunity_status = new \Zend\Db\Sql\Expression('DISTINCT(l.lead_status)');
		
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('l' => 'de_leads'));
			$select->columns(array('lead_status' => $opportunity_status));
			$select->order('l.lead_status');
				
			$result = $this->executeQuery($select)->toArray();
				
			$options = array();
			foreach($result as $value){
				$options[$value['lead_status']] = $value['lead_status'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

	/*
	 * Return values for lead_source column.
	 * Just hardcode for now, but at least different code can use this for
	 * consistancy.
	 */

	public function fetchLeadSourceOptions() {
		return array(
			0 => 'Select',
			'Web Form' => 'Web Form',
			'Phone' => 'Phone',
			'Direct Email' => 'Direct Email',
			'Live Chat' => 'Live Chat',
			'Walk In' => 'Walk In',
			'Facebook' => 'Facebook',
			'Instagram' => 'Instagram',
		);
	}

}