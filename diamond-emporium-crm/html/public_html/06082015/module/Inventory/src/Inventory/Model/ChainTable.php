<?php
namespace Inventory\Model;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

class ChainTable
{
	protected $dbAdapter;
	protected $resultSetPrototype;
	protected $config;
	
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function saveChain($data)
	{
     	try{
			$updateId = $data['chainRingId'];
			unset($data['chainRingId']);
			if($updateId != ''){
				$this->tableGateway->update($data, array('id' => $updateId));
				return $updateId;
			} else {
				$this->tableGateway->insert($data);
				if($this->tableGateway->lastInsertValue){
					$id = $this->tableGateway->lastInsertValue;
					try{						
						$stock_code = \De\Service\CommonService::generateStockCode($id, 'chain');
						$updateData = array('stock_code' => $stock_code);
						
						$this->tableGateway->update($updateData, array('id' => $id));
						return $id;
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
	 
	public function fetchAll($limit, $offset, $keyword = null,  $sortdatafield = null, $sortorder = null, $filter = null)
	{
		try{
			$keyword = trim($keyword);
			$select = new \Zend\Db\Sql\Select();
			$supplier_name = new \Zend\Db\Sql\Expression(
				'CONCAT(su.first_name, \' \', su.last_name)'
			);
			$owner_name = new \Zend\Db\Sql\Expression(
				'CONCAT(u.first_name, \' \', u.last_name)'
			);
			$select->from(array('chain' => 'de_inventory_chain'))
				   ->columns(array('id', 'other_style', 'length', 'thickness', 'metal_weight', 'description', 'image', 'invoice', 'price', 'created_date', 'stock_code'))
				   ->join(array('drt' => 'de_inventory_chainstyle_lookup'), 'chain.style = drt.id', array('style' => 'chain_style'), 'left')
				   ->join(array('dmtl' => 'de_metal_type_lookup'), 'chain.metal_type = dmtl.id', array('metal_type'), 'left')
				   ->join(array('su' => 'de_suppliers'), 'chain.supplier_id=su.id', array('company_name', 'supplier_name' => $supplier_name), 'left')
				   ->join(array('dwc' => 'de_inventory_chain_consign'), 'chain.id=dwc.chain_id', array('reserve_time', 'reserve_notes', 'tracking_id'), 'left')
				   ->join(array('disl' => 'de_inventory_status_lookup'), 'dwc.inventory_status_id=disl.id', array('inventory_status_name'), 'left')
				   ->join(array('disrl' => 'de_inventory_status_reason_lookup'), 'dwc.inventory_status_reason_id=disrl.id', array('inventory_status_reason'), 'left')
				   ->join(array('ditl' => 'de_inventory_type_lookup'), 'dwc.inventory_type_id=ditl.id', array('inventory_type'), 'left')
				   ->join(array('ditsl' => 'de_inventory_tracking_status_lookup'), 'dwc.inventory_tracking_status_id=ditsl.id', array('inventory_tracking_status'), 'left')
				   ->join(array('ditrl' => 'de_inventory_tracking_reason_lookup'), 'dwc.inventory_tracking_reason_id=ditrl.id', array('inventory_tracking_reason'), 'left')
				   ->join(array('u' => 'de_users'), 'u.user_id = dwc.owner_id', array('owner_name' => $owner_name), 'left');
			if(!empty($keyword)){
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
			}
			
			if(!empty($sortdatafield) && !empty($sortorder)){
				if($sortdatafield == 'inventory_status_name')
					$select->order("disl.inventory_status_name $sortorder");
				elseif($sortdatafield == 'inventory_status_reason')
					$select->order("disrl.inventory_status_reason $sortorder");
				elseif($sortdatafield == 'reserve_time')
					$select->order("dwc.reserve_time $sortorder");
				elseif($sortdatafield == 'reserve_notes')
					$select->order("dwc.reserve_notes $sortorder");
				elseif($sortdatafield == 'reserve_notes')
					$select->order("dwc.reserve_notes $sortorder");
				elseif($sortdatafield == 'inventory_type')
					$select->order("ditl.inventory_type $sortorder");
				elseif($sortdatafield == 'inventory_tracking_status')
					$select->order("ditsl.inventory_tracking_status $sortorder");
				elseif($sortdatafield == 'inventory_tracking_reason')
					$select->order("ditrl.inventory_tracking_reason $sortorder");
				elseif($sortdatafield == 'tracking_id')
					$select->order("dwc.tracking_id $sortorder");
				elseif($sortdatafield == 'supplier_name')
					$select->order("su.supplier_name $sortorder");
				elseif($sortdatafield == 'style')
					$select->order("chain.style $sortorder");
				elseif($sortdatafield == 'length')
					$select->order("chain.length $sortorder");
				elseif($sortdatafield == 'thickness')
					$select->order("chain.thickness $sortorder");
				elseif($sortdatafield == 'metal_type')
					$select->order("chain.metal_type $sortorder");
				elseif($sortdatafield == 'metal_weight')
					$select->order("chain.metal_weight $sortorder");
				elseif($sortdatafield == 'price')
					$select->order("chain.price $sortorder");
				elseif($sortdatafield == 'owner_name')
					$select->order("u.owner_name $sortorder");
				elseif($sortdatafield == 'stock_code')
					$select->order("chain.stock_code $sortorder");
				elseif($sortdatafield == 'id')
					$select->order("chain.id DESC");
			}else{
				$select->order("chain.id DESC");
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
			$queryData = $resultSetLimit->toArray();
			$finalData = array();
			foreach($queryData as $key => $data){
				if($data['style'] == "Other"){
					$data['style'] = $data['other_style'];
				}
				$finalData[$key] = $data;
			}
			$result['Rows'] = $finalData;
			return $result;
		}catch(\Exception $e){echo $e->getMessage ();
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function listColumns($mode)
	{
		try{
			if($mode == 'chain'){
				$result = $this->getChainColumns();
			}
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function getChainColumns(){
		$list = array();
		$list['consign_button'] = 'Consignment';
		$list['inventory_status_name'] = 'Inventory Status';
		$list['inventory_status_reason'] = 'Reason';
		$list['reserve_time'] = 'Reserve Time';
		$list['reserve_notes'] = 'Reserve Note';
		$list['inventory_type'] = 'Inventory Type';
		$list['inventory_tracking_status'] = 'Tracking';
		$list['inventory_tracking_reason'] = 'Tracking Reason';
		$list['tracking_id'] = 'Tracking ID';
		
		$list['stock_code'] = 'Stock Code';
		$list['supplier_name'] = 'Supplier Name';
		$list['style'] = 'Style';
		$list['length'] = 'Length';
		$list['thickness'] = 'Thickness';
		$list['metal_type'] = 'Metal Type';
		$list['metal_weight'] = 'Metal Weight';
		$list['price'] = 'Price';
		$list['owner_name'] = 'Record Owner';
		
		return $list;
	}
	
	public function fetchChainDetails($id)
	{
		try{
			$keyword = trim($keyword);
			$select = new \Zend\Db\Sql\Select();
			$supplier_name = new \Zend\Db\Sql\Expression(
				'CONCAT(su.first_name, \' \', su.last_name)'
			);
			$owner_name = new \Zend\Db\Sql\Expression(
				'CONCAT(u.first_name, \' \', u.last_name)'
			);
			$select->from(array('chain' => 'de_inventory_chain'))
				   ->columns(array('id', 'other_style', 'length', 'thickness', 'metal_weight', 'description', 'image', 'invoice', 'price', 'supplier_id', 'created_date', 'stock_code'))
				   ->join(array('drt' => 'de_inventory_chainstyle_lookup'), 'chain.style = drt.id', array('style' => 'chain_style'), 'left')
				   ->join(array('dmtl' => 'de_metal_type_lookup'), 'chain.metal_type = dmtl.id', array('metal_type'), 'left')
				   ->join(array('su' => 'de_suppliers'), 'chain.supplier_id=su.id', array('company_name', 'supplier_name' => $supplier_name, 'supplier_email' => 'email', 'supplier_phone' => 'phone'), 'left')
				   ->join(array('dwc' => 'de_inventory_chain_consign'), 'chain.id=dwc.chain_id', array('reserve_time', 'reserve_notes', 'tracking_id'), 'left')
				   ->join(array('disl' => 'de_inventory_status_lookup'), 'dwc.inventory_status_id=disl.id', array('inventory_status_name'), 'left')
				   ->join(array('disrl' => 'de_inventory_status_reason_lookup'), 'dwc.inventory_status_reason_id=disrl.id', array('inventory_status_reason'), 'left')
				   ->join(array('ditl' => 'de_inventory_type_lookup'), 'dwc.inventory_type_id=ditl.id', array('inventory_type'), 'left')
				   ->join(array('ditsl' => 'de_inventory_tracking_status_lookup'), 'dwc.inventory_tracking_status_id=ditsl.id', array('inventory_tracking_status'), 'left')
				   ->join(array('ditrl' => 'de_inventory_tracking_reason_lookup'), 'dwc.inventory_tracking_reason_id=ditrl.id', array('inventory_tracking_reason'), 'left')
				   ->join(array('u' => 'de_users'), 'u.user_id = dwc.owner_id', array('owner_name' => $owner_name), 'left')
				   ->where(array('chain.id = ?' => $id));

			$adapter = $this->tableGateway->getAdapter();
			$statement = $adapter->createStatement();
			
			$select->prepareStatement($adapter, $statement);
			
			$resultSetLimit = new \Zend\Db\ResultSet\ResultSet();
			$resultSetLimit->initialize($statement->execute());
			$result = $resultSetLimit->current();
			return $result;
		}catch(\Exception $e){echo $e->getMessage ();
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deleteChain($id){
		try{
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_inventory_chain_consign', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('chain_id' => $id));
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_inventory_chain', $dbAdapter, null, $resultSetPrototype);
			return $tableGateway->delete(array('id' => $id));
		}catch(\Exception $e){
			echo $e->getMessage (); exit;
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}