<?php
namespace Inventory\Model;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

class EarringTable
{
	protected $dbAdapter;
	protected $resultSetPrototype;
	protected $config;
	
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function saveEarring($data)
	{
     	try{
			$additionalData = array();
			$additionalData['style'] = $data['style'];
			$additionalData['shape'] = $data['shape'];
			$additionalData['gemtype'] = $data['gemtype'];
			$additionalData['quantity'] = $data['quantity'];
			$additionalData['size'] = $data['size'];
			$additionalData['totalcarat'] = $data['totalcarat'];
			$updateId = $data['earRingId'];
			unset($data['style']);
			unset($data['shape']);
			unset($data['gemtype']);
			unset($data['quantity']);
			unset($data['size']);
			unset($data['totalcarat']);
			unset($data['earRingId']);
			if($updateId != ''){
				$this->tableGateway->update($data, array('id' => $updateId));
				$this->insertAdditionalData($updateId, $additionalData);
				return $updateId;
			} else {
				$this->tableGateway->insert($data);
				if($this->tableGateway->lastInsertValue){
					$id = $this->tableGateway->lastInsertValue;
					try{
						$this->insertAdditionalData($id, $additionalData);
						
						$stock_code = \De\Service\CommonService::generateStockCode($id, 'earring');
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
			$select->from(array('earring' => 'de_inventory_ear_rings'))
				   ->columns(array('id', 'other_ring_style', 'cad_code', 'band_width', 'band_thickness', 'metal_weight', 'description', 'invoice', 'price', 'created_date', 'stock_code'))
				   ->join(array('drt' => 'de_earring_style_lookup'), 'earring.ring_style = drt.id', array('ring_style'), 'left')
				   ->join(array('dmtl' => 'de_metal_type_lookup'), 'earring.metal_type = dmtl.id', array('metal_type'), 'left')
				   ->join(array('dpl' => 'de_profile_lookup'), 'earring.profile = dpl.id', array('profile'), 'left')
				   ->join(array('su' => 'de_suppliers'), 'earring.supplier_id=su.id', array('company_name', 'supplier_name' => $supplier_name), 'left')
				   ->join(array('dwc' => 'de_inventory_earrings_consign'), 'earring.id=dwc.earring_id', array('reserve_time', 'reserve_notes', 'tracking_id'), 'left')
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
						new \Zend\Db\Sql\Predicate\In('earring.stock_code', array($keyword)),
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
				elseif($sortdatafield == 'ring_style')
					$select->order("earring.ring_style $sortorder");
				elseif($sortdatafield == 'cad_code')
					$select->order("earring.cad_code $sortorder");
				elseif($sortdatafield == 'metal_type')
					$select->order("earring.metal_type $sortorder");
				elseif($sortdatafield == 'profile')
					$select->order("earring.profile $sortorder");
				elseif($sortdatafield == 'metal_weight')
					$select->order("earring.metal_weight $sortorder");
				elseif($sortdatafield == 'band_width')
					$select->order("earring.band_width $sortorder");
				elseif($sortdatafield == 'band_thickness')
					$select->order("earring.band_thickness $sortorder");
				elseif($sortdatafield == 'price')
					$select->order("earring.price $sortorder");
				elseif($sortdatafield == 'owner_name')
					$select->order("u.owner_name $sortorder");
				elseif($sortdatafield == 'stock_code')
					$select->order("earring.stock_code $sortorder");
				elseif($sortdatafield == 'id')
					$select->order("earring.id DESC");
			}else{
				$select->order("earring.id DESC");
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
				if($data['ring_style'] == 'Other'){
					$data['ring_style'] = $data['other_ring_style'];
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
			if($mode == 'earring'){
				$result = $this->getEarringsColumns();
			} else if($mode == 'ear_ring'){
				//$result = $this->getCustomersColumns();
			}
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function getEarringsColumns(){
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
		$list['ring_style'] = 'Style';
		$list['cad_code'] = 'CAD Stock Code';
		$list['metal_type'] = 'Metal Type';
		$list['profile'] = 'Profile';
		$list['metal_weight'] = 'Metal Weight';
		$list['band_width'] = 'Band Width';
		$list['band_thickness'] = 'Band Thickness';
		$list['price'] = 'Price';
		$list['owner_name'] = 'Record Owner';
		return $list;
	}
	
	public function insertAdditionalData($earringId, $additionalData){
		try{
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_inventory_earring_addl_options', $dbAdapter, null, $resultSetPrototype);
			if($earringId){
				$tableGateway->delete(array('earring_ring_item_id' => $earringId));
			}
			foreach($additionalData['style'] as $key => $data){
				$quantity = 0;
				$shape = 0;
				$gemtype = 0;
				$size = 0;
				$total_carat = 0;
				if(isset($additionalData['quantity'][$key]) && !empty($additionalData['quantity'][$key])){ 
					$quantity = $additionalData['quantity'][$key]; 
				}
				if(isset($additionalData['shape'][$key]) && !empty($additionalData['shape'][$key])){ 
					$shape = $additionalData['shape'][$key]; 
				}
				if(isset($additionalData['gemtype'][$key]) && !empty($additionalData['gemtype'][$key])){ 
					$gemtype = $additionalData['gemtype'][$key]; 
				}
				if(isset($additionalData['size'][$key]) && !empty($additionalData['size'][$key])){ 
					$size = $additionalData['size'][$key]; 
				}
				if(isset($additionalData['totalcarat'][$key]) && !empty($additionalData['totalcarat'][$key])){ 
					$total_carat = $additionalData['totalcarat'][$key]; 
				} 
				if($earringId){
					if($data != 0 || $shape != 0 || $gemtype != 0 || $quantity != 0 || $size !=0 || $total_carat != 0){
						$sqlInsert = array("earring_ring_item_id" => $earringId, "setting_style_id" => $data, "stone_shape_id" => $shape, "gem_type_id" => $gemtype, "qty" => $quantity, "size" =>  $size, "total_carat" =>  $total_carat);
					$tableGateway->insert($sqlInsert);
					}
				}
			}
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchEarringDetails($id)
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
			$select->from(array('earring' => 'de_inventory_ear_rings'))
				   ->columns(array('id', 'other_ring_style', 'cad_code', 'band_width', 'band_thickness', 'metal_weight', 'description', 'image', 'invoice', 'price', 'ring_style_lookupid' => 'ring_style', 'metal_type_lookupid' => 'metal_type', 'profile_type_lookupid' => 'profile', 'supplier_id', 'stock_code'))
				   ->join(array('drt' => 'de_earring_style_lookup'), 'earring.ring_style = drt.id', array('ring_style'), 'left')
				   ->join(array('dmtl' => 'de_metal_type_lookup'), 'earring.metal_type = dmtl.id', array('metal_type'), 'left')
				   ->join(array('dpl' => 'de_profile_lookup'), 'earring.profile = dpl.id', array('profile'), 'left')
				   ->join(array('su' => 'de_suppliers'), 'earring.supplier_id=su.id', array('company_name', 'supplier_name' => $supplier_name, 'supplier_email' => 'email', 'supplier_phone' => 'phone'), 'left')
				   ->join(array('dwc' => 'de_inventory_earrings_consign'), 'earring.id=dwc.earring_id', array('reserve_time', 'reserve_notes', 'tracking_id'), 'left')
				   ->join(array('disl' => 'de_inventory_status_lookup'), 'dwc.inventory_status_id=disl.id', array('inventory_status_name'), 'left')
				   ->join(array('disrl' => 'de_inventory_status_reason_lookup'), 'dwc.inventory_status_reason_id=disrl.id', array('inventory_status_reason'), 'left')
				   ->join(array('ditl' => 'de_inventory_type_lookup'), 'dwc.inventory_type_id=ditl.id', array('inventory_type'), 'left')
				   ->join(array('ditsl' => 'de_inventory_tracking_status_lookup'), 'dwc.inventory_tracking_status_id=ditsl.id', array('inventory_tracking_status'), 'left')
				   ->join(array('ditrl' => 'de_inventory_tracking_reason_lookup'), 'dwc.inventory_tracking_reason_id=ditrl.id', array('inventory_tracking_reason'), 'left')
				   ->join(array('u' => 'de_users'), 'u.user_id = dwc.owner_id', array('owner_name' => $owner_name), 'left')
				   ->where(array('earring.id = ?' => $id));

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
	
	public function getAdditionalData($id, $type){
		try{
			$keyword = trim($keyword);
			$select = new \Zend\Db\Sql\Select();
			//echo $type; exit;
			if($type == "earring"){
				$select->from(array('earring' => 'de_inventory_earring_addl_options'))
					   ->columns(array('id', 'item_id' => 'earring_ring_item_id', 'setting_style_id', 'stone_shape_id', 'gem_type_id', 'qty', 'size', 'total_carat'))
					   ->join(array('setting' => 'de_setting_style_lookup'), 'earring.setting_style_id = setting.id', array('setting_style'), 'left')
					   ->join(array('shape' => 'de_shape_lookup'), 'earring.stone_shape_id = shape.id', array('shape'), 'left')
					   ->join(array('gem' => 'de_gem_type_lookup'), 'earring.gem_type_id = gem.id', array('gem_type'), 'left')
					   ->where(array('earring.earring_ring_item_id = ?' => $id));
			}
			$adapter = $this->tableGateway->getAdapter();
			$statement = $adapter->createStatement();
			
			$select->prepareStatement($adapter, $statement);
			
			$resultSetLimit = new \Zend\Db\ResultSet\ResultSet();
			$resultSetLimit->initialize($statement->execute());
			$result = $resultSetLimit->toArray();
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deleteAdditionalRow($id, $type){
		try{
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			if($type == "earring"){
				$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_inventory_earring_addl_options', $dbAdapter, null, $resultSetPrototype);
				return $tableGateway->delete(array('id' => $id));
			}
		}catch(\Exception $e){
			echo $e->getMessage (); exit;
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deleteEarring($id){
		try{
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_inventory_earring_addl_options', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('earring_ring_item_id' => $id));
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_inventory_earrings_consign', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('earring_id' => $id));
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_inventory_ear_rings', $dbAdapter, null, $resultSetPrototype);
			return $tableGateway->delete(array('id' => $id));
		}catch(\Exception $e){
			echo $e->getMessage (); exit;
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}