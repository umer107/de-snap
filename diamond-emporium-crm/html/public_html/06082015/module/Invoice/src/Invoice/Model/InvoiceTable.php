<?php

/**
 * Class Invoice
 */

namespace Invoice\Model;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

class InvoiceTable {

    public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/**
	 * Fetch invoice items and related info
	 * $invoice_number
	 * $items optional, comma(,) separated item ids
	 */
    public function fetchInvoiceItems($invoice_number, $items = null) {
        try{
			$select = new \Zend\Db\Sql\Select();
			
			$select->from(array('inv' => 'de_invoice'))
				   ->join(array('inv_itm' => 'de_invoice_item'), 'inv_itm.invoice_id = inv.id', array('*'), 'left')
				   ->where(array('inv.invoice_number = ?' => $invoice_number));
			if($items)
				$select->where->in('inv_itm.item_id', explode(',', $items));
				
			//echo $select->getSqlString();exit;
			$select->order("inv.id DESC");
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			$invoice_items = $result->toArray();
			
			return $invoice_items;
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	/**
	 * Fetch invoice items and related info
	 * $id = invoice id
	 */
    public function fetchInvoiceItemsById($id) {
        try{
			$select = new \Zend\Db\Sql\Select();
			
			$select->from(array('inv' => 'de_invoice'))
				   ->join(array('inv_itm' => 'de_invoice_item'), 'inv_itm.invoice_id = inv.id', array('*'), 'left')
				   ->where(array('inv.id = ?' => $id));
				
			//echo $select->getSqlString();exit;
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			$invoice_items = $result->toArray();
			
			return $invoice_items;
			
		}catch(\Exception $e){echo $e->getMessage ();
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
    
    public function getInvoiceById($invoice_id){
		$resultSet = $this->tableGateway->select(array('id' => $invoice_id));
		$row = $resultSet->current();
		if(!$row){
			throw new \Exception('No row found');
		}
		return $row;
	}
	
	/**
	 * Fetch invoice details
	 * $invoice_id = invoice id, primary key
	 */
    public function getInvoiceDetailsById($id){
		 try{
			$select = new \Zend\Db\Sql\Select();
			
			$select->from(array('inv' => 'de_invoice'))
				   ->columns(array('id', 'invoice_id', 'invoice_number', 'created_date', 'created_by', 'status'))
				   ->join(array('opp' => 'de_opportunities'), 'opp.id = inv.opp_id', array('opp_name' => 'opportunity_name'), 'left')
				   ->where(array('inv.id = ?' => $id));
			
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			$invoice = (array)$result->current();
			
			return $invoice;
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 *
	 */
	 public function emailInvoice($invoice_id){
	 	try{
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
}
