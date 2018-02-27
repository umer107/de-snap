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
			
		}catch(\Exception $e){
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
			$customer_name = new \Zend\Db\Sql\Expression('CONCAT(cust.first_name, \' \', cust.last_name)');
			$xero_date = new \Zend\Db\Sql\Expression('date(xero_date)');
			$xero_date_due = new \Zend\Db\Sql\Expression('date(xero_date_due)');
			$select->from(array('inv' => 'de_invoice'))
				   ->columns(array('id', 'invoice_id', 'invoice_number', 'created_date', 'created_by', 'status', 'xero_date' => $xero_date, 'xero_date_due' => $xero_date_due))
				   ->join(array('opp' => 'de_opportunities'), 'opp.id = inv.opp_id', array('opp_id' => 'id', 'opp_name' => 'opportunity_name'), 'left')
				   ->join(array('cust' => 'de_customers'), new \Zend\Db\Sql\Expression('cust.id = opp.user_id '), array('cust_id' => 'id', 'customer_name' => $customer_name, 'email'), 'left')
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
	 * Update invoice data by invoce id
	 * $invoice_id, primary key
	 * $data, array, data to be saved
	 */
	 public function updateInvoice($invoice_id, $data){
	 	try {
            return $this->tableGateway->update($data, array('id' => $invoice_id));
        } catch (Exception $e) {
		    \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
	 }
	 
	/**
	 * Store invoice email data in db
	 * $insetData, array, data to be inserted
	 */
	 public function saveInvoiceEmail($insetData){
	 	try{
			$tableInvoiceEmail = new TableGateway('de_invoice_email', $this->tableGateway->getAdapter());
		
			return $tableInvoiceEmail->insert($insetData);
		} catch (Exception $e) {
		    \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
	 }

	/**
	 *
	 */	 
	 public function fetchAllEmail($limit, $offset, $sortdatafield = null, $sortorder = null, $customer_id = null, $keyword = null){
	 	try{
			$select = new \Zend\Db\Sql\Select();
						
			$resultSetPrototype = new HydratingResultSet();
			
			$tableGateway = new TableGateway('de_invoice_email', $this->tableGateway->getAdapter(), null, $resultSetPrototype);
			
			$customer_name = new \Zend\Db\Sql\Expression(
				'CONCAT(c.first_name, \' \', c.last_name)'
			);
	
			$select->from(array('ie' => 'de_invoice_email'))
					->columns(array('*'))
					->join(array('c' => 'de_customers'), new \Zend\Db\Sql\Expression('c.id = ie.cust_id'), array('customer_name' => $customer_name, 'customer_email' => 'email'), 'left');
			
			if(!empty($keyword)){
				$where = new \Zend\Db\Sql\Where();
				$where->NEST->addPredicates(array(
						new \Zend\Db\Sql\Predicate\Like('ie.subject', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('ie.message', "%$keyword%"),
					), 'OR'
				)->UNNEST;
				/*$where->addPredicates(array(
					new \Zend\Db\Sql\Predicate\Like('u.first_name', "%$keyword%"),
				));*/
				$select->where($where);
			}
			
			if(!empty($customer_id))
				$select->where(array('c.id = ?' => $customer_id));
			
			if(!empty($sortdatafield) && !empty($sortorder)){
				if($sortdatafield == 'created_date' || $sortdatafield == 'created_time')
					$select->order("ie.created_date $sortorder");
				elseif($sortdatafield == 'subject')
					$select->order("ie.subject $sortorder");
				elseif($sortdatafield == 'id')
					$select->order("ie.id $sortorder");
			} else {
				$select->order('ie.id DESC');
			}
			//echo $select->getSqlString();exit;
			$statement = $this->tableGateway->getAdapter()->createStatement();			
			$select->prepareStatement($this->tableGateway->getAdapter(), $statement);
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($statement->execute());
			
			$select->limit($limit);
			$select->offset($offset);
			
			$statement = $this->tableGateway->getAdapter()->createStatement();
			$select->prepareStatement($this->tableGateway->getAdapter(), $statement);
			$resultSetLimit = new \Zend\Db\ResultSet\ResultSet();
			$resultSetLimit->initialize($statement->execute());
			
			$result['TotalRows'] = count($resultSet);
			$result['Rows'] = $resultSetLimit->toArray();
			
			return $result;
		} catch (Exception $e) {echo $e->getMessage();
		    \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
	 }
	 
	/**
	 * Fetch email details
	 * $id, primary key
	 */
	public function fetchEmail($id){
		try{
			$select = new \Zend\Db\Sql\Select();
						
			$resultSetPrototype = new HydratingResultSet();
			
			$tableGateway = new TableGateway('de_invoice_email', $this->tableGateway->getAdapter(), null, $resultSetPrototype);
			
			$customer_name = new \Zend\Db\Sql\Expression(
				'CONCAT(c.first_name, \' \', c.last_name)'
			);
	
			$select->from(array('ie' => 'de_invoice_email'))
					->columns(array('*'))
					->join(array('c' => 'de_customers'), new \Zend\Db\Sql\Expression('c.id = ie.cust_id'), array('customer_name' => $customer_name, 'customer_email' => 'email'), 'left')
					->where(array('ie.id = ?' => $id));
				
			$statement = $this->tableGateway->getAdapter()->createStatement();			
			$select->prepareStatement($this->tableGateway->getAdapter(), $statement);
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($statement->execute());			
			
			return $resultSet->current();
		} catch (Exception $e) {
		    \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
	}
	
	public function deleteInvoice($invoices){
		try{		
			$select = new \Zend\Db\Sql\Select();
			
			$select->from(array('inv' => 'de_invoice'))
				   ->columns(array('id'))
				   ->where("invoice_number IN (".implode(',', $invoices).")");
							
			//echo $select->getSqlString();exit;
			
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			$invoiceData = $result->toArray();
			$invoiceArr = array();
			foreach($invoiceData as $value){
				$invoiceArr[] = $value['id'];
			}
			
			if(!empty($invoiceArr)){
				$ordersTable = new TableGateway('de_orders', $this->tableGateway->getAdapter());		
				$ordersTable->update(array('invoice_number' => null), "invoice_number IN (".implode(',', $invoices).")");
				
				$invoiceItemTable = new TableGateway('de_invoice_item', $this->tableGateway->getAdapter());		
				$invoiceItemTable->delete("invoice_id IN (".implode(',', $invoiceArr).")");
				
				$this->tableGateway->delete("id IN (".implode(',', $invoiceArr).") AND (invoice_number != '' OR invoice_number != NULL)");
			}
			
		} catch (Exception $e) {
		    \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
	}
	
	public function detachOrderInvoice($invoice_number){
		try{
		
			$select = new \Zend\Db\Sql\Select();
			
			$select->from(array('inv' => 'de_invoice'))
				   ->columns(array('id'))
				   ->where(array('invoice_number' => $invoice_number));
			
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			$invoice = $result->current();			
			
			$ordersTable = new TableGateway('de_orders', $this->tableGateway->getAdapter());		
			$ordersTable->update(array('invoice_number' => null), array('invoice_number' => $invoice_number));
			
			$invoiceItemTable = new TableGateway('de_invoice_item', $this->tableGateway->getAdapter());		
			$invoiceItemTable->delete(array('invoice_id' => $invoice->id));
			
			$this->tableGateway->delete(array('id' => $invoice->id));
			
		} catch (Exception $e) {
		    \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
	}
}
