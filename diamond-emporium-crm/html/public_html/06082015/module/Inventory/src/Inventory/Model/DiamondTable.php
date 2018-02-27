<?php
namespace Inventory\Model;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

class DiamondTable
{
	protected $dbAdapter;
	protected $resultSetPrototype;
	protected $config;
	
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function saveDiamond($data)
	{
     	try{
			//$id = (int) $data['id'];
			$updateId = $data['DiamondId'];
			unset($data['DiamondId']);
			if (empty($updateId)) {
				$this->tableGateway->insert($data);
				
				$id = $this->tableGateway->lastInsertValue;
				$stock_code = \De\Service\CommonService::generateStockCode($id, 'diamond');
				$updateData = array('DiamondId' => $id, 'stock_code' => $stock_code);
				
				return $this->saveDiamond($updateData);
			} else {
				return $this->tableGateway->update($data, array('id' => $updateId));
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
			$select->from(array('d' => 'de_inventory_diamonds'))
				   ->columns(array('id', 'cert_no', 'cert_url', 'video_url', 'cut', 'carat', 'depth', 'diamond_table' => 'table', 'flurosence', 'measurement', 'description', 'price', 'created_date', 'stock_code'))
				   ->join(array('dt' => 'de_diamond_type_lookup'), 'dt.id = d.diamond_type', array('diamond_type'), 'left')
				   ->join(array('c' => 'de_color_lookup'), 'c.id = d.color', array('color'), 'left')
				   ->join(array('s' => 'de_shape_lookup'), 's.id = d.shape', array('shape'), 'left')
				   ->join(array('p' => 'de_polish_lookup'), 'p.id = d.polish', array('polish'), 'left')
				   ->join(array('sy' => 'de_symmetry_lookup'), 'sy.id = d.symmetry', array('symmetry'), 'left')
				   ->join(array('i' => 'de_intensity_lookup'), 'i.id = d.intensity', array('intensity'), 'left')
				   ->join(array('o' => 'de_overtone_lookup'), 'o.id = d.overtone', array('overtone'), 'left')
				   ->join(array('l' => 'de_lab_lookup'), 'l.id = d.lab', array('lab'), 'left')
				   ->join(array('cl' => 'de_clarity_lookup'), 'cl.id = d.clarity', array('clarity'), 'left')
				   ->join(array('su' => 'de_suppliers'), 'su.id = d.supplier_id', array('company_name', 'supplier_name' => $supplier_name), 'left')
				   ->join(array('dc' => 'de_inventory_diamonds_consign'), 'dc.diamond_id = d.id', array('reserve_time', 'reserve_notes', 'tracking_id'), 'left')
				   ->join(array('isl' => 'de_inventory_status_lookup'), 'isl.id = dc.inventory_status_id', array('inventory_status_name'), 'left')
				   ->join(array('isrl' => 'de_inventory_status_reason_lookup'), 'isrl.id = dc.inventory_status_reason_id', array('inventory_status_reason'), 'left')
				   ->join(array('itl' => 'de_inventory_type_lookup'), 'itl.id = dc.inventory_type_id', array('inventory_type'), 'left')
				   ->join(array('itsl' => 'de_inventory_tracking_status_lookup'), 'itsl.id = dc.inventory_tracking_status_id', array('inventory_tracking_status'), 'left')
				   ->join(array('itrl' => 'de_inventory_tracking_reason_lookup'), 'itrl.id = dc.inventory_tracking_reason_id', array('inventory_tracking_reason'), 'left')
				   ->join(array('u' => 'de_users'), 'u.user_id = dc.owner_id', array('owner_name' => $owner_name), 'left');
			if(!empty($keyword)){
				$where = new \Zend\Db\Sql\Where();
				$where->NEST->addPredicates(array(
					    new \Zend\Db\Sql\Predicate\Like('su.first_name', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('su.last_name', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('u.first_name', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('u.last_name', "%$keyword%"),
					    new \Zend\Db\Sql\Predicate\Like('d.cert_no', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\In('dc.tracking_id', array($keyword)),
						new \Zend\Db\Sql\Predicate\In('d.stock_code', array($keyword)),
				    ), 'OR'
				)->UNNEST;
				/*$where->addPredicates(array(
					new \Zend\Db\Sql\Predicate\Like('u.first_name', "%$keyword%"),
				));*/
				$select->where($where);
			}
			
			if(!empty($filter['filter_shape']))
				$select->where(array('d.shape = ?' => $filter['filter_shape']));
			/*if(!empty($filter['filter_carat']))
				$select->where(array('d.carat = ?' => $filter['filter_carat']));*/
				
			if(!empty($filter['filter_carat_from']) && empty($filter['filter_carat_to']))
				$select->where(array('d.carat >= ?' => $filter['filter_carat_from']));
			elseif(!empty($filter['filter_carat_from']) && !empty($filter['filter_carat_to']))
				$select->where('d.carat >= ' . $filter['filter_carat_from'] . ' AND d.carat <= ' . $filter['filter_carat_to']);
			elseif(empty($filter['filter_carat_from']) && !empty($filter['filter_carat_to']))
				$select->where(array('d.carat <= ?' => $filter['filter_carat_to']));
				
			if(!empty($filter['filter_color']))
				$select->where(array('d.color = ?' => $filter['filter_color']));
			if(!empty($filter['filter_cut']))
				$select->where(array('d.cut = ?' => $filter['filter_cut']));
			if(!empty($filter['filter_clarity']))
				$select->where(array('d.clarity = ?' => $filter['filter_clarity']));
			if(!empty($filter['filter_inventory_status']))
				$select->where(array('dc.inventory_status_id = ?' => $filter['filter_inventory_status']));
			
			if(!empty($sortdatafield) && !empty($sortorder)){
				if($sortdatafield == 'inventory_status_name')
					$select->order("isl.inventory_status_name $sortorder");
				elseif($sortdatafield == 'inventory_status_reason')
					$select->order("isrl.inventory_status_reason $sortorder");
				elseif($sortdatafield == 'reserve_time')
					$select->order("dc.reserve_time $sortorder");
				elseif($sortdatafield == 'reserve_notes')
					$select->order("dc.reserve_notes $sortorder");
				elseif($sortdatafield == 'reserve_notes')
					$select->order("dc.reserve_notes $sortorder");
				elseif($sortdatafield == 'inventory_type')
					$select->order("itl.inventory_type $sortorder");
				elseif($sortdatafield == 'inventory_tracking_status')
					$select->order("itsl.inventory_tracking_status $sortorder");
				elseif($sortdatafield == 'inventory_tracking_reason')
					$select->order("itrl.inventory_tracking_reason $sortorder");
				elseif($sortdatafield == 'tracking_id')
					$select->order("dc.tracking_id $sortorder");
				elseif($sortdatafield == 'supplier_name')
					$select->order("su.supplier_name $sortorder");
				elseif($sortdatafield == 'cert_no')
					$select->order("d.cert_no $sortorder");
				elseif($sortdatafield == 'shape')
					$select->order("s.shape $sortorder");
				elseif($sortdatafield == 'color')
					$select->order("c.color $sortorder");
				elseif($sortdatafield == 'measurement')
					$select->order("d.measurement $sortorder");
				elseif($sortdatafield == 'cut')
					$select->order("d.cut $sortorder");
				elseif($sortdatafield == 'polish')
					$select->order("p.polish $sortorder");
				elseif($sortdatafield == 'symmetry')
					$select->order("sy.symmetry $sortorder");
				elseif($sortdatafield == 'flurosence')
					$select->order("d.flurosence $sortorder");
				elseif($sortdatafield == 'price')
					$select->order("d.price $sortorder");
				elseif($sortdatafield == 'owner_name')
					$select->order("u.owner_name $sortorder");
				elseif($sortdatafield == 'stock_code')
					$select->order("d.stock_code $sortorder");
				elseif($sortdatafield == 'id')
					$select->order("d.id DESC");
			}else{
				$select->order("d.id DESC");
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
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function listColumns($mode)
	{
		try{
			if($mode == 'diamond'){
				$result = $this->getDiamondsColumns();
			} else if($mode == 'ear_ring'){
				//$result = $this->getCustomersColumns();
			}
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function getDiamondsColumns(){
		$list = array();
		$list['consign_button'] = 'Consignment';
		$list['inventory_status_name'] = 'Inventory Status';
		$list['inventory_status_reason'] = 'Reason';
		$list['reserve_time'] = 'Reserve Time';
		$list['reserve_notes'] = 'Reserve Note';
		$list['inventory_type'] = 'Inventory Type';
		//$list['consign_button'] = 'Consignee Responsible';
		$list['inventory_tracking_status'] = 'Tracking';
		$list['inventory_tracking_reason'] = 'Tracking Reason';
		$list['tracking_id'] = 'Tracking ID';
		
		$list['stock_code'] = 'Stock Code';
		$list['supplier_name'] = 'Supplier Name';
		$list['cert_no'] = 'Certificate Number';
		$list['shape'] = 'Shape';
		$list['color'] = 'Color';
		$list['measurement'] = 'Measurement';
		$list['cut'] = 'Cut';
		$list['carat'] = 'Carat';
		$list['clarity'] = 'Clarity';
		$list['polish'] = 'Polish';
		$list['symmetry'] = 'Symmetry';
		$list['owner_name'] = 'Owner Name';
		return $list;
	}
	
	public function fetchDiamondDetails($id)
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$supplier_name = new \Zend\Db\Sql\Expression(
				'CONCAT(su.first_name, \' \', su.last_name)'
			);
			$owner_name = new \Zend\Db\Sql\Expression(
				'CONCAT(u.first_name, \' \', u.last_name)'
			);
			$select->from(array('d' => 'de_inventory_diamonds'))
				   ->columns(array('id', 'cert_no', 'cert_url', 'video_url', 'cut', 'carat', 'depth', 'diamond_table' => 'table', 'flurosence', 'measurement', 'image', 'invoice', 'description', 'price', 'created_date', 'white_type', 'stock_code'))
				   ->join(array('dt' => 'de_diamond_type_lookup'), 'dt.id = d.diamond_type', array('diamond_type'), 'left')
				   ->join(array('c' => 'de_color_lookup'), 'c.id = d.color', array('color'), 'left')
				   ->join(array('s' => 'de_shape_lookup'), 's.id = d.shape', array('shape'), 'left')
				   ->join(array('p' => 'de_polish_lookup'), 'p.id = d.polish', array('polish'), 'left')
				   ->join(array('sy' => 'de_symmetry_lookup'), 'sy.id = d.symmetry', array('symmetry'), 'left')
				   ->join(array('i' => 'de_intensity_lookup'), 'i.id = d.intensity', array('intensity'), 'left')
				   ->join(array('o' => 'de_overtone_lookup'), 'o.id = d.overtone', array('overtone'), 'left')
				   ->join(array('l' => 'de_lab_lookup'), 'l.id = d.lab', array('lab'), 'left')
				   ->join(array('cl' => 'de_clarity_lookup'), 'cl.id = d.clarity', array('clarity'), 'left')
				   ->join(array('su' => 'de_suppliers'), 'su.id = d.supplier_id', array('company_name', 'supplier_name' => $supplier_name, 'supplier_email' => 'email', 'supplier_phone' => 'phone'), 'left')
				   ->join(array('dc' => 'de_inventory_diamonds_consign'), 'dc.diamond_id = d.id', array('reserve_time', 'reserve_notes', 'tracking_id'), 'left')
				   ->join(array('isl' => 'de_inventory_status_lookup'), 'isl.id = dc.inventory_status_id', array('inventory_status_name'), 'left')
				   ->join(array('isrl' => 'de_inventory_status_reason_lookup'), 'isrl.id = dc.inventory_status_reason_id', array('inventory_status_reason'), 'left')
				   ->join(array('itl' => 'de_inventory_type_lookup'), 'itl.id = dc.inventory_type_id', array('inventory_type'), 'left')
				   ->join(array('itsl' => 'de_inventory_tracking_status_lookup'), 'itsl.id = dc.inventory_tracking_status_id', array('inventory_tracking_status'), 'left')
				   ->join(array('itrl' => 'de_inventory_tracking_reason_lookup'), 'itrl.id = dc.inventory_tracking_reason_id', array('inventory_tracking_reason'), 'left')
				   ->join(array('u' => 'de_users'), 'u.user_id = dc.owner_id', array('owner_name' => $owner_name), 'left')
				   ->where(array('d.id = ?' => $id));

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
	
	public function deleteDiamond($id){
		try{
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_inventory_diamonds_consign', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('diamond_id' => $id));
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_inventory_diamonds', $dbAdapter, null, $resultSetPrototype);
			return $tableGateway->delete(array('id' => $id));
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

}