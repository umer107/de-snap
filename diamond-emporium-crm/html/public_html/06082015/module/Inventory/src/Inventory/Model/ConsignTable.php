<?php

namespace Inventory\Model;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

class ConsignTable {

	protected $dbAdapter;
	protected $resultSetPrototype;
	protected $config;

	public function __construct($sm) {
		$this->dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		$this->resultSetPrototype = new HydratingResultSet();
		$this->config = $sm->get('Config');
	}

	public function saveConsign($data, $type) {
		try {
			if ($type == "diamond") {
				$table = $this->config["dbPrefix"] . 'inventory_diamonds_consign';
			} else if ($type == "weddingring") {
				$table = $this->config["dbPrefix"] . 'inventory_weddingrings_consign';
			} else if ($type == "engagementring") {
				$table = $this->config["dbPrefix"] . 'inventory_engagementrings_consign';
			} else if ($type == "earring") {
				$table = $this->config["dbPrefix"] . 'inventory_earrings_consign';
			} else if ($type == "pendant") {
				$table = $this->config["dbPrefix"] . 'inventory_pendants_consign';
			} else if ($type == "miscellaneous") {
				$table = $this->config["dbPrefix"] . 'inventory_miscellaneous_consign';
			} else if ($type == "chain") {
				$table = $this->config["dbPrefix"] . 'inventory_chain_consign';
			} else if ($type == "job") {
				$table = $this->config["dbPrefix"] . 'order_job_consign';
			}
			$tableGateway = new TableGateway($table, $this->dbAdapter, null, $this->resultSetPrototype);

			$id = (int) $data['id'];
			if (empty($id)) {
				$tableGateway->insert($data);
				return $tableGateway->lastInsertValue;
			} else {
				return $tableGateway->update($data, array('id' => $id));
			}
		} catch (\Exception $e) {
			echo $e->getMessage();
			\De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
		}
	}

	public function validateOwner($user_id, $password) {
		try {
			$tableGateway = new TableGateway($this->config["dbPrefix"] . 'master_password', $this->dbAdapter, null, $this->resultSetPrototype);

			$select = new \Zend\Db\Sql\Select();
			$select->from(array('mp' => $this->config["dbPrefix"] . 'master_password'))
				->columns(array('password'))
				->where(array('mp.password = MD5(?)' => $password));

			$adapter = $tableGateway->getAdapter();
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);

			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($statement->execute());

			return $resultSet->count();
		} catch (\Exception $e) {
			echo $e->getMessage();
			\De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
		}
	}

	public function fetchConsignData($item_id, $type) {
		try {
			$select = new \Zend\Db\Sql\Select();
			$owner_name = new \Zend\Db\Sql\Expression(
				'CONCAT(u.first_name, \' \', u.last_name)'
			);
			if ($type == "diamond") {
				$tableGateway = new TableGateway($this->config["dbPrefix"] . 'inventory_diamonds_consign', $this->dbAdapter, null, $this->resultSetPrototype);
				
				$select->from(array('dc' => 'de_inventory_diamonds_consign'))
					->columns(array('id', 'diamond_id', 'inventory_status_id', 'inventory_type_id', 'inventory_status_reason_id', 'reserve_time', 'reserve_notes', 'inventory_tracking_status_id', 'inventory_tracking_reason_id', 'tracking_id', 'owner_id'))
					->join(array('disl' => 'de_inventory_status_lookup'), 'dc.inventory_status_id = disl.id', array('inventory_status_name'), 'left')
					->join(array('ditl' => 'de_inventory_type_lookup'), 'dc.inventory_type_id = ditl.id', array('inventory_type'), 'left')
					->join(array('disrl' => 'de_inventory_status_reason_lookup'), 'dc.inventory_status_reason_id = disrl.id', array('inventory_status_reason'), 'left')
					->join(array('ditsl' => 'de_inventory_tracking_status_lookup'), 'dc.inventory_tracking_status_id = ditsl.id', array('inventory_tracking_status'), 'left')
					->join(array('ditrl' => 'de_inventory_tracking_reason_lookup'), 'dc.inventory_tracking_reason_id = ditrl.id', array('inventory_tracking_reason'), 'left')
					->join(array('u' => 'de_users'), 'dc.owner_id=u.user_id', array('owner_name' => $owner_name), 'left')
					->where(array('dc.diamond_id  = ?' => $item_id));
			} else if ($type == "weddingring") {
				$tableGateway = new TableGateway($this->config["dbPrefix"] . 'inventory_weddingrings_consign', $this->dbAdapter, null, $this->resultSetPrototype);
				
				$select->from(array('dc' => 'de_inventory_weddingrings_consign'))
					->columns(array('id', 'weddingring_id', 'inventory_status_id', 'inventory_type_id', 'inventory_status_reason_id', 'reserve_time', 'reserve_notes', 'inventory_tracking_status_id', 'inventory_tracking_reason_id', 'tracking_id', 'owner_id'))
					->join(array('disl' => 'de_inventory_status_lookup'), 'dc.inventory_status_id = disl.id', array('inventory_status_name'), 'left')
					->join(array('ditl' => 'de_inventory_type_lookup'), 'dc.inventory_type_id = ditl.id', array('inventory_type'), 'left')
					->join(array('disrl' => 'de_inventory_status_reason_lookup'), 'dc.inventory_status_reason_id = disrl.id', array('inventory_status_reason'), 'left')
					->join(array('ditsl' => 'de_inventory_tracking_status_lookup'), 'dc.inventory_tracking_status_id = ditsl.id', array('inventory_tracking_status'), 'left')
					->join(array('ditrl' => 'de_inventory_tracking_reason_lookup'), 'dc.inventory_tracking_reason_id = ditrl.id', array('inventory_tracking_reason'), 'left')
					->join(array('u' => 'de_users'), 'dc.owner_id=u.user_id', array('owner_name' => $owner_name), 'left')
					->where(array('dc.weddingring_id  = ?' => $item_id));
			} else if ($type == "engagementring") {
				$tableGateway = new TableGateway($this->config["dbPrefix"] . 'inventory_engagementrings_consign', $this->dbAdapter, null, $this->resultSetPrototype);
				
				$select->from(array('dc' => 'de_inventory_engagementrings_consign'))
					->columns(array('id', 'engagementring_id', 'inventory_status_id', 'inventory_type_id', 'inventory_status_reason_id', 'reserve_time', 'reserve_notes', 'inventory_tracking_status_id', 'inventory_tracking_reason_id', 'tracking_id', 'owner_id'))
					->join(array('disl' => 'de_inventory_status_lookup'), 'dc.inventory_status_id = disl.id', array('inventory_status_name'), 'left')
					->join(array('ditl' => 'de_inventory_type_lookup'), 'dc.inventory_type_id = ditl.id', array('inventory_type'), 'left')
					->join(array('disrl' => 'de_inventory_status_reason_lookup'), 'dc.inventory_status_reason_id = disrl.id', array('inventory_status_reason'), 'left')
					->join(array('ditsl' => 'de_inventory_tracking_status_lookup'), 'dc.inventory_tracking_status_id = ditsl.id', array('inventory_tracking_status'), 'left')
					->join(array('ditrl' => 'de_inventory_tracking_reason_lookup'), 'dc.inventory_tracking_reason_id = ditrl.id', array('inventory_tracking_reason'), 'left')
					->join(array('u' => 'de_users'), 'dc.owner_id=u.user_id', array('owner_name' => $owner_name), 'left')
					->where(array('dc.engagementring_id  = ?' => $item_id));
			} else if ($type == "earring") {
				$tableGateway = new TableGateway($this->config["dbPrefix"] . 'inventory_earrings_consign', $this->dbAdapter, null, $this->resultSetPrototype);
				
				$select->from(array('dc' => 'de_inventory_earrings_consign'))
					->columns(array('id', 'earring_id', 'inventory_status_id', 'inventory_type_id', 'inventory_status_reason_id', 'reserve_time', 'reserve_notes', 'inventory_tracking_status_id', 'inventory_tracking_reason_id', 'tracking_id', 'owner_id'))
					->join(array('disl' => 'de_inventory_status_lookup'), 'dc.inventory_status_id = disl.id', array('inventory_status_name'), 'left')
					->join(array('ditl' => 'de_inventory_type_lookup'), 'dc.inventory_type_id = ditl.id', array('inventory_type'), 'left')
					->join(array('disrl' => 'de_inventory_status_reason_lookup'), 'dc.inventory_status_reason_id = disrl.id', array('inventory_status_reason'), 'left')
					->join(array('ditsl' => 'de_inventory_tracking_status_lookup'), 'dc.inventory_tracking_status_id = ditsl.id', array('inventory_tracking_status'), 'left')
					->join(array('ditrl' => 'de_inventory_tracking_reason_lookup'), 'dc.inventory_tracking_reason_id = ditrl.id', array('inventory_tracking_reason'), 'left')
					->join(array('u' => 'de_users'), 'dc.owner_id=u.user_id', array('owner_name' => $owner_name), 'left')
					->where(array('dc.earring_id  = ?' => $item_id));
			} else if ($type == "pendant") {
				$tableGateway = new TableGateway($this->config["dbPrefix"] . 'inventory_pendants_consign', $this->dbAdapter, null, $this->resultSetPrototype);
				
				$select->from(array('dc' => 'de_inventory_pendants_consign'))
					->columns(array('id', 'pendant_id', 'inventory_status_id', 'inventory_type_id', 'inventory_status_reason_id', 'reserve_time', 'reserve_notes', 'inventory_tracking_status_id', 'inventory_tracking_reason_id', 'tracking_id', 'owner_id'))
					->join(array('disl' => 'de_inventory_status_lookup'), 'dc.inventory_status_id = disl.id', array('inventory_status_name'), 'left')
					->join(array('ditl' => 'de_inventory_type_lookup'), 'dc.inventory_type_id = ditl.id', array('inventory_type'), 'left')
					->join(array('disrl' => 'de_inventory_status_reason_lookup'), 'dc.inventory_status_reason_id = disrl.id', array('inventory_status_reason'), 'left')
					->join(array('ditsl' => 'de_inventory_tracking_status_lookup'), 'dc.inventory_tracking_status_id = ditsl.id', array('inventory_tracking_status'), 'left')
					->join(array('ditrl' => 'de_inventory_tracking_reason_lookup'), 'dc.inventory_tracking_reason_id = ditrl.id', array('inventory_tracking_reason'), 'left')
					->join(array('u' => 'de_users'), 'dc.owner_id=u.user_id', array('owner_name' => $owner_name), 'left')
					->where(array('dc.pendant_id  = ?' => $item_id));
			} else if ($type == "miscellaneous") {
				$tableGateway = new TableGateway($this->config["dbPrefix"] . 'inventory_miscellaneous_consign', $this->dbAdapter, null, $this->resultSetPrototype);
				
				$select->from(array('dc' => 'de_inventory_miscellaneous_consign'))
					->columns(array('id', 'miscellaneous_id', 'inventory_status_id', 'inventory_type_id', 'inventory_status_reason_id', 'reserve_time', 'reserve_notes', 'inventory_tracking_status_id', 'inventory_tracking_reason_id', 'tracking_id', 'owner_id'))
					->join(array('disl' => 'de_inventory_status_lookup'), 'dc.inventory_status_id = disl.id', array('inventory_status_name'), 'left')
					->join(array('ditl' => 'de_inventory_type_lookup'), 'dc.inventory_type_id = ditl.id', array('inventory_type'), 'left')
					->join(array('disrl' => 'de_inventory_status_reason_lookup'), 'dc.inventory_status_reason_id = disrl.id', array('inventory_status_reason'), 'left')
					->join(array('ditsl' => 'de_inventory_tracking_status_lookup'), 'dc.inventory_tracking_status_id = ditsl.id', array('inventory_tracking_status'), 'left')
					->join(array('ditrl' => 'de_inventory_tracking_reason_lookup'), 'dc.inventory_tracking_reason_id = ditrl.id', array('inventory_tracking_reason'), 'left')
					->join(array('u' => 'de_users'), 'dc.owner_id=u.user_id', array('owner_name' => $owner_name), 'left')
					->where(array('dc.miscellaneous_id  = ?' => $item_id));
			} else if ($type == "chain") {
				$tableGateway = new TableGateway($this->config["dbPrefix"] . 'inventory_chain_consign', $this->dbAdapter, null, $this->resultSetPrototype);
				
				$select->from(array('dc' => 'de_inventory_chain_consign'))
					->columns(array('id', 'chain_id', 'inventory_status_id', 'inventory_type_id', 'inventory_status_reason_id', 'reserve_time', 'reserve_notes', 'inventory_tracking_status_id', 'inventory_tracking_reason_id', 'tracking_id', 'owner_id'))
					->join(array('disl' => 'de_inventory_status_lookup'), 'dc.inventory_status_id = disl.id', array('inventory_status_name'), 'left')
					->join(array('ditl' => 'de_inventory_type_lookup'), 'dc.inventory_type_id = ditl.id', array('inventory_type'), 'left')
					->join(array('disrl' => 'de_inventory_status_reason_lookup'), 'dc.inventory_status_reason_id = disrl.id', array('inventory_status_reason'), 'left')
					->join(array('ditsl' => 'de_inventory_tracking_status_lookup'), 'dc.inventory_tracking_status_id = ditsl.id', array('inventory_tracking_status'), 'left')
					->join(array('ditrl' => 'de_inventory_tracking_reason_lookup'), 'dc.inventory_tracking_reason_id = ditrl.id', array('inventory_tracking_reason'), 'left')
					->join(array('u' => 'de_users'), 'dc.owner_id=u.user_id', array('owner_name' => $owner_name), 'left')
					->where(array('dc.chain_id  = ?' => $item_id));
			} else if ($type == "job") {
				$tableGateway = new TableGateway($this->config["dbPrefix"] . 'order_job_consign', $this->dbAdapter, null, $this->resultSetPrototype);
				
				$select->from(array('dc' => 'de_order_job_consign'))
					->columns(array('id', 'job_id', 'inventory_status_id', 'inventory_type_id', 'inventory_status_reason_id', 'reserve_time', 'reserve_notes', 'inventory_tracking_status_id', 'inventory_tracking_reason_id', 'tracking_id', 'owner_id'))
					->join(array('disl' => 'de_inventory_status_lookup'), 'dc.inventory_status_id = disl.id', array('inventory_status_name'), 'left')
					->join(array('ditl' => 'de_inventory_type_lookup'), 'dc.inventory_type_id = ditl.id', array('inventory_type'), 'left')
					->join(array('disrl' => 'de_inventory_status_reason_lookup'), 'dc.inventory_status_reason_id = disrl.id', array('inventory_status_reason'), 'left')
					->join(array('ditsl' => 'de_inventory_tracking_status_lookup'), 'dc.inventory_tracking_status_id = ditsl.id', array('inventory_tracking_status'), 'left')
					->join(array('ditrl' => 'de_inventory_tracking_reason_lookup'), 'dc.inventory_tracking_reason_id = ditrl.id', array('inventory_tracking_reason'), 'left')
					->join(array('u' => 'de_users'), 'dc.owner_id=u.user_id', array('owner_name' => $owner_name), 'left')
					->where(array('dc.job_id  = ?' => $item_id));
			}
			$adapter = $tableGateway->getAdapter();
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);

			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($statement->execute());

			return $resultSet->current();
		} catch (\Exception $e) {
			\De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
		}
	}

}
