<?php

/**
 *
 */

namespace Invoice\Model;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

class InvoiceItemTable {

    public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

    
}
