<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;

class WebsiteCategoryTable{

	protected $dbAdapter;
	protected $resultSetPrototype;
	protected $config;

	public function __construct(TableGateway $tableGateway){
		$this->tableGateway = $tableGateway;
	}

}
