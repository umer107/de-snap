<?php
namespace Inventory\Model;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

class EngagementringTable
{
	protected $dbAdapter;
	protected $resultSetPrototype;
	protected $config;
	
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function saveEngagementring($data)
	{
     	try{
			$additionalData = array();
			$additionalData['style'] = $data['style'];
			$additionalData['shape'] = $data['shape'];
			$additionalData['gemtype'] = $data['gemtype'];
			$additionalData['quantity'] = $data['quantity'];
			$additionalData['size'] = $data['size'];
			$additionalData['totalcarat'] = $data['totalcarat'];
			$updateId = $data['engagementRingId'];
			unset($data['style']);
			unset($data['shape']);
			unset($data['gemtype']);
			unset($data['quantity']);
			unset($data['size']);
			unset($data['totalcarat']);
			unset($data['engagementRingId']);
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
						
						$stock_code = \De\Service\CommonService::generateStockCode($id, 'engagementring');
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
			$select->from(array('engage' => 'de_inventory_engagement_rings'))
				   ->columns(array('id', 'cad_code', 'band_width_top', 'band_width_bottom', 'band_thickness_top','band_thickness_bottom', 'halo_width', 'halo_thickness', 'setting_height', 'description', 'invoice', 'price', 'created_date', 'stock_code'))
				   ->join(array('drt' => 'de_engagementring_type_lookup'), 'engage.ring_type = drt.id', array('ring_type'), 'left')
				   ->join(array('dmtl' => 'de_metal_type_lookup'), 'engage.metal_type = dmtl.id', array('metal_type'), 'left')
				   ->join(array('dpl' => 'de_profile_lookup'), 'engage.profile = dpl.id', array('profile'), 'left')
				   ->join(array('dhsl' => 'de_head_settings_lookup'), 'engage.head_settings = dhsl.id', array('head_title'), 'left')
				   ->join(array('dctl' => 'de_claw_termination_lookup'), 'engage.claw_termination = dctl.id', array('claw_title'), 'left')
				   ->join(array('su' => 'de_suppliers'), 'engage.supplier_id=su.id', array('company_name', 'supplier_name' => $supplier_name), 'left')
				   ->join(array('dwc' => 'de_inventory_engagementrings_consign'), 'engage.id=dwc.engagementring_id', array('reserve_time', 'reserve_notes', 'tracking_id'), 'left')
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
						new \Zend\Db\Sql\Predicate\In('engage.stock_code', array($keyword)),
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
				elseif($sortdatafield == 'ring_type')
					$select->order("engage.ring_type $sortorder");
				elseif($sortdatafield == 'cad_code')
					$select->order("engage.cad_code $sortorder");
				elseif($sortdatafield == 'metal_type')
					$select->order("engage.metal_type $sortorder");
				elseif($sortdatafield == 'profile')
					$select->order("engage.profile $sortorder");
				elseif($sortdatafield == 'band_width_top')
					$select->order("engage.band_width_top $sortorder");
				elseif($sortdatafield == 'band_width_bottom')
					$select->order("engage.band_width_bottom $sortorder");
				elseif($sortdatafield == 'band_thickness_top')
					$select->order("engage.band_thickness_top $sortorder");
				elseif($sortdatafield == 'band_thickness_bottom')
					$select->order("engage.band_thickness_bottom $sortorder");
					elseif($sortdatafield == 'halo_width')
					$select->order("engage.halo_width $sortorder");
				elseif($sortdatafield == 'halo_thickness')
					$select->order("engage.halo_thickness $sortorder");
				elseif($sortdatafield == 'head_settings')
					$select->order("engage.head_settings $sortorder");
				elseif($sortdatafield == 'claw_termination')
					$select->order("engage.claw_termination $sortorder");
				elseif($sortdatafield == 'setting_height')
					$select->order("engage.setting_height $sortorder");
				elseif($sortdatafield == 'price')
					$select->order("engage.price $sortorder");
				elseif($sortdatafield == 'owner_name')
					$select->order("u.owner_name $sortorder");
				elseif($sortdatafield == 'stock_code')
					$select->order("engage.stock_code $sortorder");	
				elseif($sortdatafield == 'id')
					$select->order("engage.id DESC");	
			}else{
				$select->order("engage.id DESC");
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
	
	public function listColumns($mode)
	{
		try{
			if($mode == 'engagementring'){
				$result = $this->getEngagementringsColumns();
			} else if($mode == 'ear_ring'){
				//$result = $this->getCustomersColumns();
			}
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function getEngagementringsColumns(){
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
		$list['ring_type'] = 'Ring Type';
		$list['cad_code'] = 'CAD Code';
		$list['metal_type'] = 'Metal Type';
		$list['profile'] = 'Profile';
		$list['band_width_top'] = 'Band Width Top';
		$list['band_width_bottom'] = 'Band Width Bottom';
		$list['band_thickness_top'] = 'Band Thickness Top';
		$list['band_thickness_bottom'] = 'Band Thickness Bottom';
		$list['halo_width'] = 'Halo Width';
		$list['halo_thickness'] = 'Halo Thickness';
		$list['head_title'] = 'Head Settings';
		$list['claw_title'] = 'Claw Termination';
		$list['setting_height'] = 'Setting Height';
		$list['price'] = 'Price';
		$list['owner_name'] = 'Record Owner';
		return $list;
	}
	
	public function insertAdditionalData($engagementringId, $additionalData){
		try{
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_inventory_engagementring_addl_options', $dbAdapter, null, $resultSetPrototype);
			if($engagementringId){
				$tableGateway->delete(array('engage_ring_item_id' => $engagementringId));
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
				if($engagementringId){
					if($data != 0 || $shape != 0 || $gemtype != 0 || $quantity != 0 || $size !=0 || $total_carat != 0){
						$sqlInsert = array("engage_ring_item_id" => $engagementringId, "setting_style_id" => $data, "stone_shape_id" => $shape, "gem_type_id" => $gemtype, "qty" => $quantity, "size" =>  $size, "total_carat" =>  $total_carat);
					$tableGateway->insert($sqlInsert);
					}
				}
			}
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchEngagementringDetails($id)
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
			$select->from(array('engage' => 'de_inventory_engagement_rings'))
				   ->columns(array('id', 'cad_code', 'band_width_top', 'band_width_bottom', 'band_thickness_top', 'band_thickness_bottom', 'description', 'image', 'invoice', 'price', 'ring_type_lookupid' => 'ring_type', 'metal_type_lookupid' => 'metal_type', 'profile_type_lookupid' => 'profile', 'supplier_id', 'halo_width', 'halo_thickness', 'setting_height', 'stock_code'))
				   ->join(array('drt' => 'de_engagementring_type_lookup'), 'engage.ring_type = drt.id', array('ring_type'), 'left')
				   ->join(array('dmtl' => 'de_metal_type_lookup'), 'engage.metal_type = dmtl.id', array('metal_type'), 'left')
				   ->join(array('dpl' => 'de_profile_lookup'), 'engage.profile = dpl.id', array('profile'), 'left')
				   ->join(array('dhsl' => 'de_head_settings_lookup'), 'engage.head_settings = dhsl.id', array('head_title'), 'left')
				   ->join(array('dctl' => 'de_claw_termination_lookup'), 'engage.claw_termination = dctl.id', array('claw_title'), 'left')			   
				   ->join(array('su' => 'de_suppliers'), 'engage.supplier_id=su.id', array('company_name', 'supplier_name' => $supplier_name, 'supplier_email' => 'email', 'supplier_phone' => 'phone'), 'left')
				   ->join(array('dwc' => 'de_inventory_engagementrings_consign'), 'engage.id=dwc.engagementring_id', array('reserve_time', 'reserve_notes', 'tracking_id'), 'left')
				   ->join(array('disl' => 'de_inventory_status_lookup'), 'dwc.inventory_status_id=disl.id', array('inventory_status_name'), 'left')
				   ->join(array('disrl' => 'de_inventory_status_reason_lookup'), 'dwc.inventory_status_reason_id=disrl.id', array('inventory_status_reason'), 'left')
				   ->join(array('ditl' => 'de_inventory_type_lookup'), 'dwc.inventory_type_id=ditl.id', array('inventory_type'), 'left')
				   ->join(array('ditsl' => 'de_inventory_tracking_status_lookup'), 'dwc.inventory_tracking_status_id=ditsl.id', array('inventory_tracking_status'), 'left')
				   ->join(array('ditrl' => 'de_inventory_tracking_reason_lookup'), 'dwc.inventory_tracking_reason_id=ditrl.id', array('inventory_tracking_reason'), 'left')
				   ->join(array('u' => 'de_users'), 'u.user_id = dwc.owner_id', array('owner_name' => $owner_name), 'left')
				   ->where(array('engage.id = ?' => $id));

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
			if($type == "engagementring"){
				$select->from(array('engage' => 'de_inventory_engagementring_addl_options'))
					   ->columns(array('id', 'item_id' => 'engage_ring_item_id', 'setting_style_id', 'stone_shape_id', 'gem_type_id', 'qty', 'size', 'total_carat'))
					   ->join(array('setting' => 'de_setting_style_lookup'), 'engage.setting_style_id = setting.id', array('setting_style'), 'left')
					   ->join(array('shape' => 'de_shape_lookup'), 'engage.stone_shape_id = shape.id', array('shape'), 'left')
					   ->join(array('gem' => 'de_gem_type_lookup'), 'engage.gem_type_id = gem.id', array('gem_type'), 'left')
					   ->where(array('engage.engage_ring_item_id = ?' => $id));
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
			if($type == "engagementring"){
				$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_inventory_engagementring_addl_options', $dbAdapter, null, $resultSetPrototype);
				return $tableGateway->delete(array('id' => $id));
			}
		}catch(\Exception $e){
			echo $e->getMessage (); exit;
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deleteEngagementring($id){
		try{
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_inventory_engagementring_addl_options', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('engage_ring_item_id' => $id));
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_inventory_engagementrings_consign', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('engagementring_id' => $id));
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_inventory_engagement_rings', $dbAdapter, null, $resultSetPrototype);
			return $tableGateway->delete(array('id' => $id));
		}catch(\Exception $e){
			echo $e->getMessage (); exit;
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}