<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Invoice\Model;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class Xero {

    private $_XRO_APP_TYPE = "Private";
    private $_OAUTH_CALLBACK = "oob";
    private $_useragent = "XeroOAuth-PHP Private";
    private $_XeroOAuth;
    private $_serviceManager;
    private $_objInvoice;

    public function __construct($sm) {

        $this->_serviceManager = $sm;
        $signatures = array(
            'consumer_key' => 'ALVVMPDU7EYEMM4TEK5QAMAGVGWSIJ',
            'shared_secret' => 'VL93HP5RLAHIYWWMOEUXIJGIRY5YPX',
            // API versions
            'core_version' => '2.0',
            'payroll_version' => '1.0',
            'file_version' => '1.0'
        );
        $signatures ['rsa_private_key'] = getcwd() . '/library/Xero/certs/test.pem';
        $signatures ['rsa_public_key'] = getcwd() . '/library/Xero/certs/cacert.pem';

        $this->_XeroOAuth = new \Xero\XeroOAuth(array_merge(array(
                    'application_type' => $this->_XRO_APP_TYPE,
                    'oauth_callback' => $this->_OAUTH_CALLBACK,
                    'user_agent' => $this->_useragent
                        ), $signatures));
        $initialCheck = $this->_XeroOAuth->diagnostics();

        $checkErrors = count($initialCheck);
        if ($checkErrors > 0) {
            // you could handle any config errors here, or keep on truckin if you like to live dangerously
            foreach ($initialCheck as $check) {
                echo 'Error: ' . $check . PHP_EOL;
            }
        } else {
            $session = $this->persistSession(array(
                'oauth_token' => $this->_XeroOAuth->config ['consumer_key'],
                'oauth_token_secret' => $this->_XeroOAuth->config ['shared_secret'],
                'oauth_session_handle' => ''
            ));
            $oauthSession = $this->retrieveSession();

            if (isset($oauthSession ['oauth_token'])) {
                $this->_XeroOAuth->config ['access_token'] = $oauthSession ['oauth_token'];
                $this->_XeroOAuth->config ['access_token_secret'] = $oauthSession ['oauth_token_secret'];
            }
        }
    }

    function persistSession($response) {
        if (isset($response)) {

            $_SESSION['access_token'] = $response['oauth_token'];
            $_SESSION['oauth_token_secret'] = $response['oauth_token_secret'];
            if (isset($response['oauth_session_handle']))
                $_SESSION['session_handle'] = $response['oauth_session_handle'];
        } else {
            return false;
        }
    }

    function retrieveSession() {
        if (isset($_SESSION['access_token'])) {
            $response['oauth_token'] = $_SESSION['access_token'];
            $response['oauth_token_secret'] = $_SESSION['oauth_token_secret'];
            $response['oauth_session_handle'] = $_SESSION['session_handle'];
            return $response;
        } else {
            return false;
        }
    }

    function outputError($XeroOAuth) {
        echo 'Error: ' . $XeroOAuth->response['response'] . PHP_EOL;
        $this->pr($XeroOAuth);
    }

    /**
     * Debug function for printing the content of an object
     *
     * @param mixes $obj
     */
    function pr($obj) {

        if (!$this->is_cli())
            echo '<pre style="word-wrap: break-word">';
        if (is_object($obj))
            print_r($obj);
        elseif (is_array($obj))
            print_r($obj);
        else
            echo $obj;
        if (!$this->is_cli())
            echo '</pre>';
    }

    function is_cli() {
        return (PHP_SAPI == 'cli' && empty($_SERVER['REMOTE_ADDR']));
    }

    public function createInvocie($data) {	
        $xml = $this->generateXml($data);
        //$invoice_id = $this->saveData($data);
        $invoiceTbl = $this->_serviceManager->get('Invoice\Model\InvoiceTable');
        $response = $this->_XeroOAuth->request('PUT', $this->_XeroOAuth->url('Invoices', 'core'), array(), $xml);

        if ($this->_XeroOAuth->response['code'] == 200) {
            $invoice = $this->_XeroOAuth->parseResponse($this->_XeroOAuth->response['response'], $this->_XeroOAuth->response['format']);

            if (count($invoice->Invoices[0]) > 0) {
               // $data = array();
                $data['invoice_id'] = $invoice->Invoices[0]->Invoice->InvoiceID;
                $data['invoice_number'] = $invoice->Invoices[0]->Invoice->InvoiceNumber;

                //$invoiceTbl->tableGateway->update($data, array("id" => $invoice_id));
				$invoice_id = $this->saveData($data);
            }
        } else {
			\De\Log::logApplicationInfo ( "Caught Exception: " . urldecode($response['response']) . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
			//$this->outputError($this->_XeroOAuth);
        }
    }

    public function createQuote($data) {
        try {
            $xml = $this->generateXml($data);
            $invoice_id = $this->saveData($data);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function saveData($data) {	
        try {
			$identity = $this->_serviceManager->get('AuthService')->getIdentity();
			
            $invoiceTbl = $this->_serviceManager->get('Invoice\Model\InvoiceTable');

            $insetData['opp_id'] = $data['opp_id'];
            $insetData['created_date'] = date("Y-m-d H:i:s");
			$insetData['created_by'] = $identity['user_id'];
			$insetData['invoice_id'] = $data['invoice_id'];
			$insetData['invoice_number'] = $data['invoice_number'];			

            $invoiceTbl->tableGateway->insert($insetData);
            $invoice_id = $invoiceTbl->tableGateway->lastInsertValue;
            $this->saveItems($data, $invoice_id);
            return $invoice_id;
        } catch (Exception $e) {
		    \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
    }

    public function saveItems($data, $invoice_id) {
        try {
            $invoiceItemTbl = $this->_serviceManager->get('Invoice\Model\InvoiceItemTable');
            for ($i = 0; $i < count($data['item_id']); $i++) {
                $insert_data['invoice_id'] = $invoice_id;
                $insert_data['item_id'] = $data['item_id'][$i];
                $insert_data['item_type'] = $data['item_type'][$i];
                $insert_data['unit_price'] = str_replace("$", "", $data['sub'][$i]);
                $insert_data['amount'] = str_replace("$", "", $data['total'][$i]);
                //$insert_data['tax'] = $data['item_type'][$i];
                $insert_data['quantity'] = $data['qty'][$i];
                $insert_data['account_code'] = $data['account'][$i];
                $insert_data['discount'] = empty($data['discount'][$i]) ? null : $data['discount'][$i];
                $insert_data['description'] = $data['item_desc'][$i];
                $invoiceItemTbl->tableGateway->insert($insert_data);
            }
        } catch (Exception $e) {
		    \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
    }

    public function generateXml($data) {
        $xml = new \SimpleXMLElement('<Invoices />');
        $invoice = $xml->addChild("Invoice");
        $type = $invoice->addChild("Type", "ACCREC");
        $contact = $invoice->addChild("Contact");
        $contact->addChild("Name", $data['opp_name']);
        $contact->addChild("EmailAddress", "");
        $invoice->addChild("Date", date("Y-m-d h:i"));
        $invoice->addChild("DueDate", date("Y-m-d h:i"));
        $invoice->addChild("LineAmountTypes", 'Exclusive');
        $lineItems = $invoice->addChild("LineItems");
        //   $invoice->addChild("SentToContact", true);


        for ($i = 0; $i < count($data['item_id']); $i++) {
            $item = $lineItems->addChild("LineItem");
            $item->addChild("Description", $data['item_desc'][$i]);
            // $item->addChild("Description",$data['item_desc'][$i]);
            $item->addChild("Quantity", $data['qty'][$i]);
            $linesub = str_replace("$", "", $data['sub'][$i]);
            $item->addChild("UnitAmount", str_replace("$", "", $data['sub'][$i]));
            $item->addChild("AccountCode", 200);
            $subTotal += $linesub * $data['qty'][$i] - ($data['discount'][$i] / 100) * $linesub;
            $item->addChild("DiscountRate", !empty($data['discount'][$i]) ? str_replace("$", "", $data['discount'][$i]) : 0);
        }

        $invoice->addChild("SubTotal", $subTotal);
        $invoice->addChild("TotalTax", '400');
        $invoice->addChild("Total", $subTotal + 400);
        $dom = dom_import_simplexml($xml);
//echo $dom->ownerDocument->saveXML($dom->ownerDocument->documentElement);exit;
        return $dom->ownerDocument->saveXML($dom->ownerDocument->documentElement);

//return $xml->asXML();
    }

    public function getInvoices($search) {
        $select = new \Zend\Db\Sql\Select();
        $sm = $this->_serviceManager;
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        $resultSetPrototype = new HydratingResultSet();
        $config = $sm->get('Config');
        $tableGateway = new TableGateway($config["dbPrefix"] . 'invoice', $dbAdapter, null, $resultSetPrototype);

        $select->from(array('t1' => 'de_invoice'))
                ->join(array('t2' => 'de_opportunities'), new \Zend\Db\Sql\Expression('t2.id = t1.opp_id '), array(), 'left')
                ->join(array('t3' => 'de_customers'), new \Zend\Db\Sql\Expression('t3.id = t2.user_id '), array("first_name", "last_name", "email"), 'left')
                ->where("t1.status != 'DELETED' AND t1.invoice_id != ''")
        ;

        if ($search) {
            $select->where("t1.status != 'DELETED' AND t1.invoice_id != '' and (t1.invoice_number like '%$search%' or t3.first_name like '%$search%' or t3.last_name like '%$search%')");
        }
		$select->order("t1.id DESC");
        $adapter = $tableGateway->getAdapter();
        $statement = $adapter->createStatement();
        $select->prepareStatement($adapter, $statement);

        $result = new \Zend\Db\ResultSet\ResultSet();
        $paginatorAdapter = new DbSelect(
                // our configured select object
                $select,
                // the adapter to run it against
                $adapter,
                // the result set to hydrate
                $result
        );
        $paginator = new Paginator($paginatorAdapter);
        
        return $paginator;
    }
	
	/**
	 * Fetch Invoices
	 * $limit = Number of records to be fetched
	 * $offset = Data fetch should start from
	 * $keyword = optional, Search string
	 * $sortdatafield = optional, sort field
	 * $sortorder = optional, sort order 
	 */
	
    public function fetchAllInvoices($limit, $offset, $keyword = null, $sortdatafield = null, $sortorder = null) {
		try{
			$select = new \Zend\Db\Sql\Select();
			
			$sm = $this->_serviceManager;
			
			$adapter = $sm->get('Zend\Db\Adapter\Adapter');
			
			$resultSetPrototype = new HydratingResultSet();
			
			$config = $sm->get('Config');
			
			$tableGateway = new TableGateway($config["dbPrefix"] . 'invoice', $adapter, null, $resultSetPrototype);
			
			$customer_name = new \Zend\Db\Sql\Expression(
				'CONCAT(t3.first_name, \' \', t3.last_name)'
			);
	
			$select->from(array('t1' => 'de_invoice'))
					->join(array('t2' => 'de_opportunities'), new \Zend\Db\Sql\Expression('t2.id = t1.opp_id '), array("opp_name" => "opportunity_name"), 'left')
					->join(array('t3' => 'de_customers'), new \Zend\Db\Sql\Expression('t3.id = t2.user_id '), array("customer_name" => $customer_name, "email"), 'left')
					->where("t1.status != 'DELETED' AND t1.invoice_id IS NOT NULL");
			
			if(!empty($keyword)){
				$where = new \Zend\Db\Sql\Where();
				$where->NEST->addPredicates(array(
						new \Zend\Db\Sql\Predicate\Like('t1.invoice_number', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('t3.first_name', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('t3.last_name', "%$keyword%"),
					), 'OR'
				)->UNNEST;
				/*$where->addPredicates(array(
					new \Zend\Db\Sql\Predicate\Like('u.first_name', "%$keyword%"),
				));*/
				$select->where($where);
			}
			
			if(!empty($sortdatafield) && !empty($sortorder)){
				if($sortdatafield == 'invoice_number')
					$select->order("t1.invoice_number $sortorder");
				elseif($sortdatafield == 'created_date')
					$select->order("t1.created_date $sortorder");
				elseif($sortdatafield == 'customer_name')
					$select->order("t3.first_name $sortorder");
				elseif($sortdatafield == 'email')
					$select->order("t3.email $sortorder");
			} else {
				$select->order('t1.id DESC');
			}
		
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

    public function getQuotes($search) {
        $select = new \Zend\Db\Sql\Select();
        $sm = $this->_serviceManager;
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        $resultSetPrototype = new HydratingResultSet();
        $config = $sm->get('Config');
        $tableGateway = new TableGateway($config["dbPrefix"] . 'invoice', $dbAdapter, null, $resultSetPrototype);

        $select->from(array('t1' => 'de_invoice'))
                ->join(array('t2' => 'de_opportunities'), new \Zend\Db\Sql\Expression('t2.id = t1.opp_id '), array(), 'left')
                ->join(array('t3' => 'de_customers'), new \Zend\Db\Sql\Expression('t3.id = t2.user_id '), array("first_name", "last_name", "email"), 'left')
                ->where("t1.status != 'DELETED' AND t1.invoice_id IS NULL")
        ;

        if ($search) {
            $select->where("t1.status != 'DELETED' AND t1.invoice_id = '' and (t1.invoice_number like '%$search%' or t3.first_name like '%$search%' or t3.last_name like '%$search%')");
        }
		$select->order("t1.id DESC");
        $adapter = $tableGateway->getAdapter();
        $statement = $adapter->createStatement();
        $select->prepareStatement($adapter, $statement);

        $result = new \Zend\Db\ResultSet\ResultSet();
            $paginatorAdapter = new DbSelect(
                // our configured select object
                $select,
                // the adapter to run it against
                $adapter,
                // the result set to hydrate
                $result
        );
        $paginator = new Paginator($paginatorAdapter);
        
        return $paginator;
    }
	
	/**
	 * Fetch Quotes
	 * $limit = Number of records to be fetched
	 * $offset = Data fetch should start from
	 * $keyword = optional, Search string
	 * $sortdatafield = optional, sort field
	 * $sortorder = optional, sort order 
	 */
	
    public function fetchAllQuotes($limit, $offset, $keyword = null, $sortdatafield = null, $sortorder = null) {
		try{
			$select = new \Zend\Db\Sql\Select();
			
			$sm = $this->_serviceManager;
			
			$adapter = $sm->get('Zend\Db\Adapter\Adapter');
			
			$resultSetPrototype = new HydratingResultSet();
			
			$config = $sm->get('Config');
			
			$customer_name = new \Zend\Db\Sql\Expression(
				'CONCAT(t3.first_name, \' \', t3.last_name)'
			);
			
			$tableGateway = new TableGateway($config["dbPrefix"] . 'invoice', $adapter, null, $resultSetPrototype);
	
			$select->from(array('t1' => 'de_invoice'))
					->join(array('t2' => 'de_opportunities'), new \Zend\Db\Sql\Expression('t2.id = t1.opp_id '), array(), 'left')
					->join(array('t3' => 'de_customers'), new \Zend\Db\Sql\Expression('t3.id = t2.user_id '), array("customer_name" => $customer_name, "email"), 'left')
					->where("t1.status != 'DELETED' AND t1.invoice_id IS NULL");
			
			if(!empty($keyword)){
				$where = new \Zend\Db\Sql\Where();
				$where->NEST->addPredicates(array(
						new \Zend\Db\Sql\Predicate\Like('t1.invoice_number', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('t3.first_name', "%$keyword%"),
						new \Zend\Db\Sql\Predicate\Like('t3.last_name', "%$keyword%"),
					), 'OR'
				)->UNNEST;
				/*$where->addPredicates(array(
					new \Zend\Db\Sql\Predicate\Like('u.first_name', "%$keyword%"),
				));*/
				$select->where($where);
			}
			if(!empty($sortdatafield) && !empty($sortorder)){
				if($sortdatafield == 'created_date')
					$select->order("t1.created_date $sortorder");
				elseif($sortdatafield == 'customer_name')
					$select->order("t3.first_name $sortorder");
				elseif($sortdatafield == 'email')
					$select->order("t3.email $sortorder");
			} else {
				$select->order('t1.id DESC');
			}
		
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

    /**
     * Fetch invoice details from web service
     */
    public function getInvoiceById($invoiceNumber) {
        $response = $this->_XeroOAuth->request('GET', $this->_XeroOAuth->url('Invoice/' . $invoiceNumber, 'core'), array());
		
        if ($this->_XeroOAuth->response['code'] == 200) {
            $invoices = $this->_XeroOAuth->parseResponse($this->_XeroOAuth->response['response'], $this->_XeroOAuth->response['format']);
            return $invoices;
        } else {
            \De\Log::logApplicationInfo ( "Caught Exception: " . urldecode($response['response']) . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
    }
	
	 /**
     * Fetch invoice details from web service
     */
    public function getInvoicePdf($invoiceNumber) {
		$response = $this->_XeroOAuth->request('GET', $this->_XeroOAuth->url('Invoice/' . $invoiceNumber, 'core'), array());
		if ($this->_XeroOAuth->response['code'] == 200) {
			$invoices = $this->_XeroOAuth->parseResponse($this->_XeroOAuth->response['response'], $this->_XeroOAuth->response['format']);
			//echo "There are " . count($invoices->Invoices[0]). " invoices in this Xero organisation, the first one is: </br>";
			//pr($invoices->Invoices[0]->Invoice);
		   
				$response = $this->_XeroOAuth->request('GET', $this->_XeroOAuth->url('Invoice/'.$invoices->Invoices[0]->Invoice->InvoiceID, 'core'), array(), "", 'pdf');
				if ($this->_XeroOAuth->response['code'] == 200) {
					$myFile = $invoices->Invoices[0]->Invoice->InvoiceID.".pdf";
					$fh = fopen($myFile, 'w') or die("can't open file");
					fwrite($fh, $this->_XeroOAuth->response['response']);
					fclose($fh);
					//echo "PDF copy downloaded, check your the directory of this script.</br>";
					
					return array('filename' => $myFile, 'content' => $this->_XeroOAuth->response['response']);
				} else {
					$this->outputError($XeroOAuth);
				}
		   
		} else {
			$this->outputError($XeroOAuth);
		}
	}
        

    /**
     * Fetch all invices from web service
     */
    public function getAllInvoicesFromWebSerice() {
        $response = $this->_XeroOAuth->request('GET', $this->_XeroOAuth->url('Invoices', 'core'), array());
        if ($this->_XeroOAuth->response['code'] == 200) {
            $invoices = $this->_XeroOAuth->parseResponse($this->_XeroOAuth->response['response'], $this->_XeroOAuth->response['format']);
            return $invoices;
        } else {
            \De\Log::logApplicationInfo ( "Caught Exception: " . urldecode($response['response']) . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
    }

	/**
	 * Delete invoice by calling web service and change the invoice status to 'DELETED'
	 * $id - primary key stored in invoice table
	 */
    public function deleteInvoice($id) {
		try{
			$invoiceTbl = $this->_serviceManager->get('Invoice\Model\InvoiceTable');
	
			$invoice_row = $invoiceTbl->getInvoiceById($id);
			$data = array();
			$data['status'] = "DELETED";
	
			$xml = "<Invoice>
						<InvoiceNumber>" . $invoice_row['invoice_number'] . "</InvoiceNumber>
						<Status>DELETED</Status>
					</Invoice>";
	
			$response = $this->_XeroOAuth->request('POST', $this->_XeroOAuth->url('Invoices', 'core'), array(), $xml);
			if ($this->_XeroOAuth->response['code'] == 200) {
				$invoices = $this->_XeroOAuth->parseResponse($this->_XeroOAuth->response['response'], $this->_XeroOAuth->response['format']);
				
				$invoiceTbl->tableGateway->update($data, array("id" => $id)); 
				return true;
			 }
			 
			 return false;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . urldecode($response['response']) . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	/**
	 * Set the quote status to 'DELETED'
	 * $id - primary key stored in invoice table
	 */
	public function deleteQuote($id) {
		try{
			$invoiceTbl = $this->_serviceManager->get('Invoice\Model\InvoiceTable');
			
			$data = array();
			$data['status'] = "DELETED";
	
			return $invoiceTbl->tableGateway->update($data, array("id" => $id));
				
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . urldecode($response['response']) . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }

	/**
	 * Duplicate invoice data
	 * $id = invoice id
	 */
    public function duplicateInvoice($id) {
		try{
			$invoiceTbl = $this->_serviceManager->get('Invoice\Model\InvoiceTable');
	
			$invoiceItems = $invoiceTbl->fetchInvoiceItemsById($id);
			
			$invoiceData = $invoiceTbl->getInvoiceDetailsById($id);
			
			$xml = $this->generateDuplicateXml($invoiceData, $invoiceItems);
			
			$response = $this->_XeroOAuth->request('POST', $this->_XeroOAuth->url('Invoices', 'core'), array(), $xml);
			
			if ($this->_XeroOAuth->response['code'] == 200) {
				$invoice = $this->_XeroOAuth->parseResponse($this->_XeroOAuth->response['response'], $this->_XeroOAuth->response['format']);
				
				if (count($invoice->Invoices[0]) > 0) {
					$data = (array) $invoiceTbl->getInvoiceById($id);
					unset($data['id']);
					$data['invoice_id'] = $invoice->Invoices[0]->Invoice->InvoiceID;
					$data['invoice_number'] = $invoice->Invoices[0]->Invoice->InvoiceNumber;
					
					$invoice_id = $this->saveDuplicateData($data, $invoiceItems);
				}
			}
			return false;  
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . urldecode($response['response']) . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }

	/**
	 * Generate xml to dupliate invoice
	 * $invoiceItems = array of items
	 */
    public function generateDuplicateXml($data, $invoiceItems) {
        $xml = new \SimpleXMLElement('<Invoices />');
        $invoice = $xml->addChild("Invoice");
        $type = $invoice->addChild("Type", "ACCREC");
        $contact = $invoice->addChild("Contact");
        $contact->addChild("Name", $data['opp_name']);
        $contact->addChild("EmailAddress", "");
        $invoice->addChild("Date", date("Y-m-d h:i"));
        $invoice->addChild("DueDate", date("Y-m-d h:i"));
        $invoice->addChild("LineAmountTypes", 'Exclusive');
        $lineItems = $invoice->addChild("LineItems");
        //   $invoice->addChild("SentToContact", true);

        foreach ($invoiceItems as $value) {
            $item = $lineItems->addChild("LineItem");
            $item->addChild("Description", $value['description']);
            $item->addChild("Quantity", $value['quantity']);
            $linesub = str_replace("$", "", $value['unit_price']);
            $item->addChild("UnitAmount", str_replace("$", "", $value['unit_price']));
            $item->addChild("AccountCode", 200);
            $subTotal += $linesub * $value['quantity'] - ($value['discount'] / 100) * $linesub;
            $item->addChild("DiscountRate", !empty($value['discount']) ? str_replace("$", "", $value['discount']) : 0);
        }

        $invoice->addChild("SubTotal", $subTotal);
        $invoice->addChild("TotalTax", '400');
        $invoice->addChild("Total", $subTotal + 400);
        $dom = dom_import_simplexml($xml);
		//echo $dom->ownerDocument->saveXML($dom->ownerDocument->documentElement);exit;
        return $dom->ownerDocument->saveXML($dom->ownerDocument->documentElement);

		//return $xml->asXML();
    }
	
	/**
	 * Insert data in invoice table only
	 * $data = Data to be inserted in the invoice table
	 * $invoiceItems = array of items for the invoice
	 */
    public function saveDuplicateData($data, $invoiceItems) {
        try {
			$identity = $this->_serviceManager->get('AuthService')->getIdentity();
            $invoiceTbl = $this->_serviceManager->get('Invoice\Model\InvoiceTable');

            $data['created_date'] = date("Y-m-d H:i:s");
			$data['created_by'] = $identity['user_id'];
			
            $invoiceTbl->tableGateway->insert($data);
            $invoice_id = $invoiceTbl->tableGateway->lastInsertValue;
            $this->saveDuplicateItems($invoiceItems, $invoice_id);
            return $invoice_id;
        } catch (Exception $e) {
		    \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
    }

	/**
	 * Insert data in invoice items table only
	 * $data = Data to be inserted in the invoice item table
	 * $invoiceItems = array of items for the invoice
	 */
    public function saveDuplicateItems($data, $invoice_id) {
        try {
            $invoiceItemTbl = $this->_serviceManager->get('Invoice\Model\InvoiceItemTable');
            foreach ($data as $value) {
                $insert_data['invoice_id'] = $invoice_id;
                $insert_data['item_id'] = $value['item_id'];
                $insert_data['item_type'] = $value['item_type'];
                $insert_data['unit_price'] = str_replace("$", "", $value['unit_price']);
                $insert_data['amount'] = str_replace("$", "", $value['amount']);
                //$insert_data['tax'] = $data['item_type'][$i];
                $insert_data['quantity'] = $value['quantity'];
                $insert_data['account_code'] = $value['account_code'];
                $insert_data['discount'] = empty($value['discount']) ? null : $value['discount'];
                $insert_data['description'] = $value['description'];
                $invoiceItemTbl->tableGateway->insert($insert_data);
            }
        } catch (Exception $e) {
		    \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
    }

	/**
	 * Duplicate quote data
	 * $id = invoice id, primary key
	 */
    public function duplicateQuote($id) {
		try{
			$invoiceTbl = $this->_serviceManager->get('Invoice\Model\InvoiceTable');
	
			$invoiceItems = $invoiceTbl->fetchInvoiceItemsById($id);
			
			$data = (array) $invoiceTbl->getInvoiceById($id);
			unset($data['id']);
			unset($data['invoice_id']);
			unset($data['invoice_number']);
			
			$invoice_id = $this->saveDuplicateData($data, $invoiceItems);

			return false;  
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . urldecode($response['response']) . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	/**
	 * Convert quote into invoice
	 * $id = invoice id, primary key
	 */
	public function copyToInvoice($id){
		try{
			$invoiceTbl = $this->_serviceManager->get('Invoice\Model\InvoiceTable');
	
			$invoiceItems = $invoiceTbl->fetchInvoiceItemsById($id);
			
			$invoiceData = $invoiceTbl->getInvoiceDetailsById($id);
			
			$xml = $this->generateDuplicateXml($invoiceData, $invoiceItems);
	
			$response = $this->_XeroOAuth->request('POST', $this->_XeroOAuth->url('Invoices', 'core'), array(), $xml);
			
			if ($this->_XeroOAuth->response['code'] == 200) {
				$invoice = $this->_XeroOAuth->parseResponse($this->_XeroOAuth->response['response'], $this->_XeroOAuth->response['format']);
				
				if (count($invoice->Invoices[0]) > 0) {
					$data = array();
					$data['invoice_id'] = $invoice->Invoices[0]->Invoice->InvoiceID;
					$data['invoice_number'] = $invoice->Invoices[0]->Invoice->InvoiceNumber;
					
					return $invoiceTbl->tableGateway->update($data, array('id' => $id));			
				}
			}
			return false;  
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . urldecode($response['response']) . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}
