<?php
namespace Suppliers\Model;

use Zend\Db\Sql\Where;

use Zend\Db\TableGateway\TableGateway;
class SuppliersTable
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
			$select->from(array('sup' => 'de_suppliers'))
				   ->columns(array('*'))
				   ->join(array('suptype' => 'de_supplier_type_lookup'), 'sup.supplier_type = suptype.id', array('supplierType' => 'supplier_type_name'), 'left');
			if(!empty($keyword)){
				$where = new \Zend\Db\Sql\Where();
				$where->NEST->addPredicates(array(
					    new \Zend\Db\Sql\Predicate\Like('sup.first_name', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('sup.last_name', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('sup.company_name', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('sup.address', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('sup.phone', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('sup.website', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('sup.mobile', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('sup.email', "%$keyword%")
				    ), 'OR'
				)->UNNEST;
				$select->where($where);
			}

			$counter = $this->executeQuery($select);
			
			$select->limit($limit);
			$select->offset($offset);
			
			if(!empty($sortdatafield) && !empty($sortorder)){
				$select->order("$sortdatafield $sortorder");
			}else{
				$select->order("sup.id DESC");
			}
			
			$data = $this->executeQuery($select);
			$result['TotalRows'] = count($counter);
			$result['Rows'] = $data->toArray();
			foreach($result['Rows'] as $key => $data){
				$services = $this->getSupplierServicesList($data['id']);
				$result['Rows'][$key]['service_name'] = $services;
			}
			
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchSupplierDetails($id)
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('sup' => 'de_suppliers'))
				   ->columns(array('*'))
				   ->join(array('suptype' => 'de_supplier_type_lookup'), 'sup.supplier_type = suptype.id', array('supplierType' => 'supplier_type_name'), 'left')
				   ->where(array('sup.id = ?' => $id));
			//echo $select->getSqlString(); exit;	
			$data = $this->executeQuery($select);
			$result = $data->toArray();
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function saveSuppliers($data, $updateId='')
	{
		try{
			$services = $data['service'];
			$updateId = $data['supplierId'];
			unset($data['service']);
			unset($data['supplierId']);
			if($updateId != ''){
				$this->tableGateway->update($data, array('id' => $updateId));
				$this->insertServices($updateId, $services);
				return $updateId;
			} else {
				$this->tableGateway->insert($data);
				if($this->tableGateway->lastInsertValue){
					$supplierId = $this->tableGateway->lastInsertValue;
					try{
						$this->insertServices($supplierId, $services);
						return $supplierId;
					}catch(\Exception $e){
						\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
					}
					
				} else {
					return false;
				}
			}
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deleteSupplier($where){
		try{
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_supplier_services', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('supplier_id' => $where));
			if(is_array($where))
				return $this->tableGateway->delete($where);
			else
				return $this->tableGateway->delete(array('id' => $where));
		}catch(\Exception $e){
			echo $e->getMessage (); exit;
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function getSupplierTypesLookup(){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('supplier_type_lookup' => 'de_supplier_type_lookup'))
				   ->columns(array('id', 'supplier_type_name'));
			$data = $this->executeQuery($select);
			$result = $data->toArray();
			foreach($result as $resData){
				$finalData[$resData['id']] = $resData['supplier_type_name'];
			}
			return $finalData;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function getServicesTypesLookup(){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('supplier_services_lookup' => 'de_supplier_services_lookup'))
				   ->columns(array('id', 'service_name'));
			$data = $this->executeQuery($select);
			$result = $data->toArray();
			foreach($result as $resData){
				$finalData[$resData['id']] = $resData['service_name'];
			}
			return $finalData;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function insertServices($supplier_id, $services){
		try{
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_supplier_services', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('supplier_id' => $supplier_id));
			foreach($services as $service_id){
				$sqlInsert = array("supplier_id" => $supplier_id, "service_id" => $service_id);
				$tableGateway->insert($sqlInsert);
			}
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function supplierLookup($limit, $offset, $keyword = null, $sortdatafield = null, $sortorder = null)
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_suppliers');
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
	
	public function getSupplierServicesList($supplier_id, $needIndex = false){
		try{
			$resArray = array();
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('serv' => 'de_supplier_services'))
				   ->columns(array('service_id'))
				   ->join(array('servlook' => 'de_supplier_services_lookup'), 'serv.service_id = servlook.id', array('service_name' => 'service_name'), 'left')
				   ->where(array('serv.supplier_id = ?' => $supplier_id));	
			$select->order("serv.service_id ASC");
			$data = $this->executeQuery($select);
			$result = $data->toArray();
			foreach($result as $resData){
				$resArray[] = $resData['service_name'];
			}
			if($needIndex){
				return $result;
			} else {
				if(count($resArray) > 0){
					return implode(", ",$resArray); 
				} else {
					return '';
				}
			}
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}