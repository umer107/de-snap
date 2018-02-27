<?php
namespace Customer\Model;

use Zend\Db\Sql\Where;

use Zend\Db\TableGateway\TableGateway;

class CustomersTable
{
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function customerLookup($limit, $offset, $keyword = null, $sortdatafield = null, $sortorder = null)
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_customers');
			$select->columns(array('id'));
			
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
			
			$select->columns(array('*'));
			//$select->order("id DESC");
			
			if(!empty($sortdatafield) && !empty($sortorder)){
				if(in_array($sortdatafield, array('first_name', 'last_name', 'email', 'mobile'))){
					$select->order("$sortdatafield $sortorder");
				}
			} else {
				$select->order("id desc");
			}
			
			$select->limit($limit);
			$select->offset($offset);
			$data = $this->tableGateway->selectWith($select);
			
			$result['TotalRows'] = count($counter);
			$result['Rows'] = $data->toArray();
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function checkValueExists($field, $exists, $exceptionField = null, $exceptionValue = null){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_customers');
			$select->columns(array('id'));
			
			$select->where($field." = '".$exists."'");
			if(!empty($exceptionField) && !empty($exceptionValue))
				$select->where($exceptionField." != '".$exceptionValue."'");
				
			$counter = $this->tableGateway->selectWith($select);
			echo count($counter) > 0 ? 1 : 0;				
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchCustomers($limit, $offset, $keyword='', $sortdatafield='', $sortorder=''){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_customers')
				   ->columns(array('id'));
			
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
			$result['TotalRows'] = count($counter);
			
			$select = new \Zend\Db\Sql\Select();
			$fullname = new \Zend\Db\Sql\Expression(
				//'CONCAT(?, \' \', ?)',  array(new \Zend\Db\Sql\Expression('c.first_name'), new \Zend\Db\Sql\Expression('c.last_name'))
				'CONCAT(c.first_name, \' \', c.last_name)'
			);
			$owner_fullname = new \Zend\Db\Sql\Expression(
				'CONCAT(u.first_name, \' \', u.last_name)'
			);
			$partner_fullname = new \Zend\Db\Sql\Expression(
				'CONCAT(p.first_name, \' \', p.last_name)'
			);
			$select->from(array('c' => 'de_customers'))
				   ->columns(array('id', 'first_name', 'last_name', 'fullname' => $fullname, 'email', 'mobile'))
				   ->join(array('s' => 'de_states'), 'c.state_id = s.id', array('state_id' => 'id', 'state_name' => 'name', 'state_code'), 'inner')
				   ->join(array('u' => 'de_users'), 'c.created_by = u.user_id', array('owner_first_name' => 'first_name', 'owner_last_name' => 'last_name', 'owner_fullname' => $owner_fullname), 'inner')
				   ->join(array('p' => 'de_customers'), 'c.partner_id = p.id', array('partner_first_name' => 'first_name', 'partner_last_name' => 'last_name', 'partner_fullname' => $partner_fullname), 'left');
			
			if(!empty($keyword)){
				$where = new \Zend\Db\Sql\Where();
				// Using predicates
				$where->addPredicates(array(
					    new \Zend\Db\Sql\Predicate\Like('c.first_name', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('c.last_name', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('c.mobile', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('c.email', "%$keyword%")
				    ), 'OR'
				);
				
				$select->where($where);
			}
			$select->limit($limit)
				   ->offset($offset);
				   
			if(!empty($sortdatafield) && !empty($sortorder)){
				if(in_array($sortdatafield, array('fullname', 'email', 'mobile'))){
					if($sortdatafield == 'fullname')
						$select->order("$sortdatafield $sortorder");
					else
						$select->order("c.$sortdatafield $sortorder");
				}elseif(in_array($sortdatafield, array('state_code'))){
					$select->order("s.$sortdatafield $sortorder");
				}elseif(in_array($sortdatafield, array('owner_fullname'))){
					$select->order("$sortdatafield $sortorder");
				}elseif(in_array($sortdatafield, array('partner_fullname'))){
					$select->order("$sortdatafield $sortorder");
				}
			} else {
				$select->order("c.id desc");
			}
				   
			//echo $select->getSqlString(); exit;
			$adapter = $this->tableGateway->getAdapter();
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($statement->execute());
			
			$result['Rows'] = $resultSet->toArray();
			
			return $result;
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchCustomedetails($id)
	{
		try{
			$fullname = new \Zend\Db\Sql\Expression(
				//'CONCAT(?, \' \', ?)',  array(new \Zend\Db\Sql\Expression('c.first_name'), new \Zend\Db\Sql\Expression('c.last_name'))
				'CONCAT(c.first_name, \' \', c.last_name)'
			);
			
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('c' => 'de_customers'))
				   ->columns(array('*', 'fullname' => $fullname))
				   ->join(array('s' => 'de_states'), 'c.state_id = s.id', array('state_name' => 'name', 'state_code'), 'inner')
				   ->join(array('p' => 'de_customers'), 'c.partner_id = p.id', array('partner_id' => 'id'), 'left')
				   ->join(array('e' => 'de_ethnicity'), 'c.ethnicity = e.id', array('ethnicity_text' => 'ethnicity'), 'left')
				   ->join(array('pr' => 'de_professions'), 'c.profession = pr.id', array('profession_text' => 'profession'), 'left')
				   ->join(array('rf' => 'de_ring_finger'), 'c.dress_ring_finger = rf.id', array('dress_ring_finger_text' => 'finger'), 'left')
				   ->join(array('drs' => 'de_ring_size'), 'c.dress_ring_size = drs.id', array('dress_ring_size_text' => 'size'), 'left')
				   ->join(array('ersl' => 'de_ring_size'), 'c.engagement_ring_size_left = ersl.id', array('engagement_ring_size_left_text' => 'size'), 'left')
				   ->join(array('ersr' => 'de_ring_size'), 'c.engagement_ring_size_right = ersr.id', array('engagement_ring_size_right_text' => 'size'), 'left')
				   ->where(array('c.id' => $id));
			
			$adapter = $this->tableGateway->getAdapter();
			$statement = $adapter->createStatement();			
			$select->prepareStatement($adapter, $statement);
			
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($statement->execute());
			
			$result = (array) $resultSet->current();
			
			return $result;
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function partnerLookup($limit, $offset, $customer_id, $keyword = null, $sortdatafield = null, $sortorder = null)
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_customers')
				   ->columns(array('*'))
				   ->where('id != ' . $customer_id . ' AND (partner_id IS NULL OR partner_id = 0)');
			
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
			
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('c' => 'de_customers'))
				   ->columns(array('*'))
				   ->join(array('s' => 'de_states'), 'c.state_id = s.id', array('state_name' => 'name', 'state_code'), 'inner')
				   ->join(array('p' => 'de_customers'), 'c.partner_id = p.id', array('partner_id' => 'id'), 'left')
				   ->join(array('e' => 'de_ethnicity'), 'c.ethnicity = e.id', array('ethnicity_text' => 'ethnicity'), 'left')
				   ->join(array('pr' => 'de_professions'), 'c.profession = pr.id', array('profession_text' => 'profession'), 'left')
				   ->join(array('rf' => 'de_ring_finger'), 'c.dress_ring_finger = rf.id', array('dress_ring_finger_text' => 'finger'), 'left')
				   ->join(array('drs' => 'de_ring_size'), 'c.dress_ring_size = drs.id', array('dress_ring_size_text' => 'size'), 'left')
				   ->join(array('ersl' => 'de_ring_size'), 'c.engagement_ring_size_left = ersl.id', array('engagement_ring_size_left_text' => 'size'), 'left')
				   ->join(array('ersr' => 'de_ring_size'), 'c.engagement_ring_size_right = ersr.id', array('engagement_ring_size_right_text' => 'size'), 'left');
			
				   
			$select->where('c.id != ' . $customer_id . ' AND (c.partner_id IS NULL OR c.partner_id = 0)');
			if(!empty($keyword)){
				$where = new \Zend\Db\Sql\Where();
				// Using predicates
				$where->addPredicates(array(
					    new \Zend\Db\Sql\Predicate\Like('c.first_name', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('c.last_name', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('c.mobile', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('c.email', "%$keyword%")
				    ), 'OR'
				);
				
				$select->where($where);
			}
			
			$select->where('c.id != ' . $customer_id);
			//$select->order("c.id DESC");
			$select->limit($limit)
				   ->offset($offset);
			
			if(!empty($sortdatafield) && !empty($sortorder)){
				if(in_array($sortdatafield, array('first_name', 'last_name', 'email', 'mobile'))){
					$select->order("c.$sortdatafield $sortorder");
				}
			} else {
				$select->order("c.id desc");
			}
			
			$adapter = $this->tableGateway->getAdapter();
			$statement = $adapter->createStatement();			
			$select->prepareStatement($adapter, $statement);
			
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($statement->execute());
			
			$result['TotalRows'] = count($counter);
			$result['Rows'] = $resultSet->toArray();
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}	
	
	public function fetchAllCounts($gridType, $customerId=''){
		try{
			$result = array();
			if($gridType == 'customers'){
				$result['customers'] = $this->fetchCustomerCounts();
			}
			if($gridType == 'leads'){
				$result['leads'] = $this->fetchLeadCounts();
			}
			if($gridType == 'suppliers'){
				$result['suppliers'] = $this->fetchSupplierCounts();
			}
			if($gridType == 'opportunities'){
				if(!empty($customerId)){
					$result['opportunities'] = $this->fetchOpportunitiesCounts($customerId);
				} else {
					$result['opportunities'] = $this->fetchOpportunitiesCounts();
				}
			}
			if($gridType == 'inventory'){
				$result['inventory_diamonds'] = $this->fetchInventoryCategoriesCounts('diamond');
				$result['inventory_weddingrings'] = $this->fetchInventoryCategoriesCounts('weddingring');
				$result['inventory_engagementrings'] = $this->fetchInventoryCategoriesCounts('engagementring');
				$result['inventory_earrings'] = $this->fetchInventoryCategoriesCounts('earring');
				$result['inventory_pendants'] = $this->fetchInventoryCategoriesCounts('pendant');
				$result['inventory_miscellaneous'] = $this->fetchInventoryCategoriesCounts('miscellaneous');
				$result['inventory_chains'] = $this->fetchInventoryCategoriesCounts('chain');
				$result['inventory_total'] = $result['inventory_diamonds'] + $result['inventory_weddingrings'] + $result['inventory_engagementrings'] + $result['inventory_earrings'] + $result['inventory_pendants'] + $result['inventory_miscellaneous'] + $result['inventory_chains'];
			}
			if($gridType == 'gridType'){
				$result['customers'] = $this->fetchCustomerCounts();
				$result['leads'] = $this->fetchLeadCounts();
				$result['opportunities'] = $this->fetchOpportunitiesCounts();
				$result['suppliers'] = $this->fetchSupplierCounts();
				$result['ordersList_total'] = $this->fetchOrdersCounts();
				$result['inventory_diamonds'] = $this->fetchInventoryCategoriesCounts('diamond');
				$result['inventory_weddingrings'] = $this->fetchInventoryCategoriesCounts('weddingring');
				$result['inventory_engagementrings'] = $this->fetchInventoryCategoriesCounts('engagementring');
				$result['inventory_earrings'] = $this->fetchInventoryCategoriesCounts('earring');
				$result['inventory_pendants'] = $this->fetchInventoryCategoriesCounts('pendant');
				$result['inventory_miscellaneous'] = $this->fetchInventoryCategoriesCounts('miscellaneous');
				$result['inventory_chains'] = $this->fetchInventoryCategoriesCounts('chain');
				$result['inventory_total'] = $result['inventory_diamonds'] + $result['inventory_weddingrings'] + $result['inventory_engagementrings'] + $result['inventory_earrings'] + $result['inventory_pendants'] + $result['inventory_miscellaneous'] + $result['inventory_chains'];
				$result['quotes_total'] = $this->fetchQuotesInvoicesCounts('quotes');
				$result['invoices_total'] = $this->fetchQuotesInvoicesCounts('invioces');
				$result['quoteInvoices_total'] = $result['quotes_total'] + $result['invoices_total'];
				$result['userList_total'] = $this->fetchUserListCounts();
			}
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchCustomerCounts(){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_customers')
				   ->columns(array('id'));
			$resultSet = $this->executeQuery($select);
			$result = count($resultSet->toArray());
			if($result > 0){
				$result = $result;
			} else {
				$result = 0;
			}
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchLeadCounts(){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_leads')
				   ->columns(array('lead_id'));
			$resultSet = $this->executeQuery($select);
			$result = count($resultSet->toArray());
			if($result > 0){
				$result = $result;
			} else {
				$result = 0;
			}
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchSupplierCounts(){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_suppliers')
				   ->columns(array('id'));
			$resultSet = $this->executeQuery($select);
			$result = count($resultSet->toArray());
			if($result > 0){
				$result = $result;
			} else {
				$result = 0;
			}
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function saveCustomer($data)
	{
     	try{
			$id = (int) $data['id'];
			if ($id == 0) {
				$this->tableGateway->insert($data);
				return $this->tableGateway->lastInsertValue;
			} else {
				//if ($this->getClient($id)) {
					return $this->tableGateway->update($data, array('id' => $id));
				//} else {
					//throw new \Exception('Client id does not exist');
				//}
			}
     	}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deleteCustomer($customer_id)
	{
     	try{
			return $this->tableGateway->delete(array('id' => $customer_id));
     	}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchOpportunitiesCounts($customerId = ''){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_opportunities')
				   ->columns(array('id'));
			if(!empty($customerId)){
				$select->where(array('user_id' => $customerId));
			}
			$resultSet = $this->executeQuery($select);
			$result = count($resultSet->toArray());
			if($result > 0){
				$result = $result;
			} else {
				$result = 0;
			}
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function executeQuery($select){
		$adapter = $this->tableGateway->getAdapter();
		$statement = $adapter->createStatement();
		$select->prepareStatement($adapter, $statement);
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet;
	}
	
	public function fetchMatchedCustomer($keywords){
		try{
			$select = new \Zend\Db\Sql\Select();
			$fullname = new \Zend\Db\Sql\Expression(
				//'CONCAT(?, \' \', ?)',  array(new \Zend\Db\Sql\Expression('c.first_name'), new \Zend\Db\Sql\Expression('c.last_name'))
				'CONCAT(c.first_name, \' \', c.last_name)'
			);
			$select->from(array('c' => 'de_customers'))
				   ->columns(array('*', 'fullname' => $fullname));
			
			$where = new \Zend\Db\Sql\Where();
			// Using predicates
			$where->addPredicates($keywords, 'OR');
			
			$select->where($where);
			//echo $select->getSqlString(); exit;
			$adapter = $this->tableGateway->getAdapter();
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($statement->execute());
			
			$result = (array) $resultSet->current();
			
			return $result;
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function checkDuplicate($where){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_customers')
				   ->columns(array('id'))
				   ->where($where);
			//echo $select->getSqlString();exit;
			$counter = $this->tableGateway->selectWith($select);
			
			return count($counter);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function unassignPartner($customerId, $partnerId)
	{
     	try{
     		$expression = new \Zend\Db\Sql\Expression(
				'IN(?,?)',  array($customerId, $partnerId)
			);
			return $this->tableGateway->update(array('partner_id' => null), $expression);
     	}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchInventoryCategoriesCounts($type){
		try{
			$select = new \Zend\Db\Sql\Select();
			if($type == "diamond"){
				$select->from('de_inventory_diamonds')
					   ->columns(array('id'));
			} else if($type == "weddingring"){
				$select->from('de_inventory_wedding_rings')
					   ->columns(array('id'));
			} else if($type == "engagementring"){
				$select->from('de_inventory_engagement_rings')
					   ->columns(array('id'));
			} else if($type == "earring"){
				$select->from('de_inventory_ear_rings')
					   ->columns(array('id'));
			} else if($type == "pendant"){
				$select->from('de_inventory_pendants')
					   ->columns(array('id'));
			} else if($type == "miscellaneous"){
				$select->from('de_inventory_miscellaneous')
					   ->columns(array('id'));
			} else if($type == "chain"){
				$select->from('de_inventory_chain')
					   ->columns(array('id'));
			}
			$resultSet = $this->executeQuery($select);
			$result = count($resultSet->toArray());
			if($result > 0){
				$result = $result;
			} else {
				$result = 0;
			}
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchUserListCounts(){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_users')
				   ->columns(array('user_id'));
			$resultSet = $this->executeQuery($select);
			$result = count($resultSet->toArray());
			if($result > 0){
				$result = $result;
			} else {
				$result = 0;
			}
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchQuotesInvoicesCounts($type){
		try{
			$select = new \Zend\Db\Sql\Select();
			if($type == "quotes"){
				$select->from('de_invoice')
					   ->columns(array('id'));
				$select->where('invoice_number IS NULL AND status !="DELETED"');
			} else if($type == "invioces"){
				$select->from('de_invoice')
					   ->columns(array('id'));
				$select->where('invoice_number IS NOT NULL AND status !="DELETED"');
				//echo $select->toString(); exit;
			}
			$resultSet = $this->executeQuery($select);
			$result = count($resultSet->toArray());
			if($result > 0){
				$result = $result;
			} else {
				$result = 0;
			}
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchOrdersCounts(){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_orders')
				   ->columns(array('id'));
			$resultSet = $this->executeQuery($select);
			$result = count($resultSet->toArray());
			if($result > 0){
				$result = $result;
			} else {
				$result = 0;
			}
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}