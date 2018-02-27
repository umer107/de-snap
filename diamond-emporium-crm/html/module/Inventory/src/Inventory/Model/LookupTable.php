<?php
namespace Inventory\Model;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

class LookupTable
{
	protected $dbAdapter;
	protected $resultSetPrototype;
	protected $config;
	
	public function __construct($sm)
	{
		$this->dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		$this->resultSetPrototype = new HydratingResultSet();
		$this->config = $sm->get('Config');
	}

	/**
	 * Fetch diamond type options
	 * return an array in key value pair combination
	 */
	public function fetchDiamonTypesOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'diamond_type_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'diamond_type'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['diamond_type'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch color options
	 * return an array in key value pair combination
	 * return an array in key value pair combination
	 */
	public function fetchColorOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'color_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'color'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['color'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch shape options
	 * return an array in key value pair combination
	 */
	public function fetchShapeOptions($jsOptions=false)
	{
		try{
			$table = $this->config["dbPrefix"].'shape_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'shape'));
			$select->order('sort_weight');
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			if($jsOptions){
				array_unshift($result,  array('id' => 0, 'shape' => 'Select'));
				return $result;
			} else {
				$options = array(0 => 'Select');
				foreach($result as $value){
					$options[$value['id']] = $value['shape'];
				}
				return $options;
			}
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch polish options
	 * return an array in key value pair combination
	 * return an array in key value pair combination
	 */
	public function fetchPolishOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'polish_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'polish'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['polish'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch symmetry options
	 * return an array in key value pair combination
	 * return an array in key value pair combination
	 */
	public function fetchSymmetryOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'symmetry_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'symmetry'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['symmetry'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch intensity options
	 * return an array in key value pair combination
	 * return an array in key value pair combination
	 */
	public function fetchIntensityOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'intensity_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'intensity'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['intensity'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}	
	
	/**
	 * Fetch overtone options
	 * return an array in key value pair combination
	 * return an array in key value pair combination
	 */
	public function fetchOvertoneOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'overtone_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'overtone'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['overtone'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}	
	
	/**
	 * Fetch lab options
	 * return an array in key value pair combination
	 * return an array in key value pair combination
	 */
	public function fetchLabOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'lab_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'lab'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['lab'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch inventory status options
	 * return an array in key value pair combination
	 * return an array in key value pair combination
	 */
	public function fetchInventoryStatusOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'inventory_status_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'inventory_status_name'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['inventory_status_name'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch inventory type options
	 * return an array in key value pair combination
	 */
	public function fetchInventoryTypeOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'inventory_type_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'inventory_type'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['inventory_type'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch inventory status reason options
	 * return an array in key value pair combination
	 */
	public function fetchInventoryStatusReasonOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'inventory_status_reason_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'inventory_status_reason', 'inventory_status_id'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			/*$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['inventory_status_reason'];
			}*/
			return json_encode($result);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch trackinf status options
	 * return an array in key value pair combination
	 */
	public function fetchInventoryTrackingStatusOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'inventory_tracking_status_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'inventory_tracking_status'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['inventory_tracking_status'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch tracking reasons options
	 * return an array in key value pair combination
	 */
	public function fetchInventoryTrackingReasonOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'inventory_tracking_reason_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'inventory_tracking_reason', 'tracking_status_id'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			/*$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['inventory_tracking_reason'];
			}*/
			return json_encode($result);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch consign owner options
	 * return an array in key value pair combination
	 */
	public function fetchConsignOwnerOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'users';
			$select = new \Zend\Db\Sql\Select();
			
			$owner_name = new \Zend\Db\Sql\Expression(
				'CONCAT(first_name, \' \', last_name)'
			);
			
			$select->from($table);
			$select->columns(array('user_id', 'owner_name' => $owner_name));
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['user_id']] = $value['owner_name'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch ring type options
	 * return an array in key value pair combination
	 */
	public function fetchRingTypeOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'ring_type';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'ring_type'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['ring_type'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch metal type options
	 * return an array in key value pair combination
	 */
	public function fetchMetalTypeOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'metal_type_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'metal_type'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['metal_type'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch profile options
	 * return an array in key value pair combination
	 */
	public function fetchProfileOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'profile_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'profile'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['profile'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch finish style options
	 * return an array in key value pair combination
	 */
	public function fetchFinishOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'finish_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'finish'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['finish'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch fit options
	 * return an array in key value pair combination
	 */	
	public function fetchFitOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'fit_option_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'fit_option'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['fit_option'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch setting style options
	 * return an array in key value pair combination
	 */	
	public function fetchSettingStyleOptions($jsOptions=false)
	{
		try{
			$table = $this->config["dbPrefix"].'setting_style_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'setting_style'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			if($jsOptions){
				array_unshift($result,  array('id' => 0, 'setting_style' => 'Select'));
				return $result;
			} else {
				$options = array();
				foreach($result as $value){
					$options[$value['id']] = $value['setting_style'];
				}
				return $options;
			}
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch gem type options
	 * return an array in key value pair combination
	 */	
	public function fetchGemTypeOptions($jsOptions=false)
	{
		try{
			$table = $this->config["dbPrefix"].'gem_type_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'gem_type'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			if($jsOptions){
				array_unshift($result,  array('id' => 0, 'gem_type' => 'Select'));
				return $result;
			} else {
				$options = array();
				foreach($result as $value){
					$options[$value['id']] = $value['gem_type'];
				}
				return $options;
			}
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

	/**
	 * Fetch engagement ring type options
	 * return an array in key value pair combination
	 */		
	public function fetchEngagementRingTypeOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'engagementring_type_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'ring_type'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['ring_type'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

	/**
	 * Fetch head setting options
	 * return an array in key value pair combination
	 */		
	public function fetchHeadSettingsOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'head_settings_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'head_title'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['head_title'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

	/**
	 * Fetch claw termination options
	 * return an array in key value pair combination
	 */		
	public function fetchClawTerminationOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'claw_termination_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'claw_title'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['claw_title'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

	/**
	 * Fetch earring style options
	 * return an array in key value pair combination
	 */		
	public function fetchEarringStyleOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'earring_style_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'ring_style'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['ring_style'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

	/**
	 * Fetch pendant style options
	 * return an array in key value pair combination
	 */		
	public function fetchPendantStyleOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'pendant_style_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'ring_style'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['ring_style'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

	/**
	 * Fetch chain style options
	 * return an array in key value pair combination
	 */	
	public function fetchChainStyleOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'inventory_chainstyle_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'chain_style'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['chain_style'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch clarity options
	 * return an array in key value pair combination
	 */		
	public function fetchClarityOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'clarity_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'clarity'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['clarity'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch flurosence options
	 * return an array in key value pair combination
	 */		
	public function fetchFlurosenceOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'flurosence_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'flurosence'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['flurosence'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch user roles, (superadmin and admin) only
	 * return an array in key value pair combination
	 */	
	public function fetchRoleOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'roles';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('role_id', 'role_name'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['role_id']] = $value['role_name'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch job types for Workshop milestone
	 * return an array in key value pair combination
	 */
	public function fetchWorkshopJobTypeOptions(){
		try{
			$table = $this->config["dbPrefix"].'workshop_job_type_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('job_type_id', 'type_name'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			//$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['job_type_id']] = $value['type_name'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch cut options
	 * return an array in key value pair combination
	 * return an array in key value pair combination
	 */
	public function fetchCutOptions()
	{
		try{
			$table = $this->config["dbPrefix"].'cut_lookup';
			$select = new \Zend\Db\Sql\Select();
			$select->from($table);
			$select->columns(array('id', 'cut'));
			
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);
			$result = $tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['cut'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/*
	 * Return options for diamonds ready for model.
	 */
	
	public function fetchDiamondModelOptions() {
		return array(
			'typeOptions' => $this->fetchDiamonTypesOptions(),
			'shapeOptions' => $this->fetchShapeOptions(),
			'inventoryStatusOptions' => $this->fetchInventoryStatusOptions(),
			'colorOptions' => $this->fetchColorOptions(),
			'cutOptions' => $this->fetchCutOptions(),
			'clarityOptions' => $this->fetchClarityOptions(),
		);
	}
}
