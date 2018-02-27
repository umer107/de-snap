<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;

class WebsiteLinksTable{

	protected $dbAdapter;
	protected $resultSetPrototype;
	protected $config;

	public function __construct(TableGateway $tableGateway){
		$this->tableGateway = $tableGateway;
	}

	public function fetchAll(){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('wl' => 'de_website_links'));
			$select->columns(array('id', 'category_id', 'website_title', 'website_link', 'is_mail'));
			$select->join(array(
				'dwc' => 'de_website_categories'
				), 'wl.category_id = dwc.id', array(
				'category_name'
				), $select::JOIN_INNER);

			$adapter = $this->tableGateway->getAdapter();
			$statement = $adapter->createStatement();

			$select->prepareStatement($adapter, $statement);
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($statement->execute());

			$records = $resultSet->toArray();
			foreach($records as $record){
				$temp[$record['category_name']][] = $record;
			}
			$result['TotalRows'] = count($resultSet);
			$result['Rows'] = $temp;

			return $result;
		}catch(\Exception $e){
			echo $e->getMessage();
			\De\Log::logApplicationInfo("Caught Exception (While fetching all website links in model class): " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
		}
	}

}
