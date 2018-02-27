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
            // API versions
            'core_version' => '2.0',
            'payroll_version' => '1.0',
            'file_version' => '1.0'
        );
        
        /* Be sure to use different certificats on dev/live */
        $short_host = array_shift(split('\.', $_SERVER['SERVER_NAME']));
        $path = getcwd() . '/library/Xero/certs/' . $short_host . '/';
        
        $signatures['rsa_private_key'] = $path . 'privatekey.pem';
        $signatures['rsa_public_key'] = $path . 'publickey.cer';
        $signatures['consumer_key'] = trim(file_get_contents($path . 'consumer_key'));
        $signatures['shared_secret'] = trim(file_get_contents($path . 'shared_secret'));
        
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
		
		$responseArr = array();
		parse_str($response['response'], $responseArr);
		$responseArr['code'] = $response['code'];
		
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
		
		return $responseArr;
    }

    public function createQuote($data) {
        try {
            //$xml = $this->generateXml($data);
            return $this->saveData($data);
        } catch (Exception $e) {
            \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
    }

    public function saveData($data) {	
        try {
			$identity = $this->_serviceManager->get('AuthService')->getIdentity();
			
            $invoiceTbl = $this->_serviceManager->get('Invoice\Model\InvoiceTable');

            $insetData['opp_id'] = $data['opp_id'];
            $insetData['created_date'] = date("Y-m-d H:i:s");
			$insetData['xero_date'] = $data['xero_date'];
            $insetData['xero_date_due'] = $data['xero_date_due'];
			$insetData['created_by'] = $identity['user_id'];
			$insetData['invoice_id'] = $data['invoice_id'];
			$insetData['invoice_number'] = $data['invoice_number'];			

            $invoiceTbl->tableGateway->insert($insetData);
            $invoice_id = $invoiceTbl->tableGateway->lastInsertValue;
            if($this->saveItems($data, $invoice_id))
            	return $invoice_id;
			else
				return false;
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
                $insert_data['salesperson'] = $data['salesperson'][$i];
                $insert_data['discount'] = empty($data['discount'][$i]) ? null : $data['discount'][$i];
                $insert_data['description'] = $data['item_desc'][$i];
                $invoiceItemTbl->tableGateway->insert($insert_data);
            }
			return true;
        } catch (Exception $e) {
		    \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
    }

    public function generateXml($data) {
        $xml = new \SimpleXMLElement('<Invoices />');
        $invoice = $xml->addChild("Invoice");
        $type = $invoice->addChild("Type", "ACCREC");
        $contact = $invoice->addChild("Contact");
        $contact->addChild("Name", $data['customer_email']); 
        $contact->addChild("EmailAddress", $data['customer_email']);
        $invoice->addChild("Date", $data['xero_date']);
        $invoice->addChild("DueDate", $data['xero_date_due']);
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
            $item->addChild("AccountCode", $data['account'][$i]);
            $tracking = $item->addChild("Tracking");

            /*
             * Tracking categories in Xero are a bit annoying. You'd think that because the
             * TrackingCategoryID is a unique identifier that one could just send that and
             * be done. But you can't. It only works if you send the name of the caterogy
             * and if you're going to do that, might as well just send the option as text.
             */ 
            
            $tracking_category = $tracking->addChild("TrackingCategory");
            $tracking_category->addChild("Name", "Salesperson");
            $tracking_category->addChild("Option", $data['salesperson'][$i]);
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
	
    public function fetchAllInvoices($limit, $offset, $keyword = null, $sortdatafield = null, $sortorder = null, $customer_id = null, $opp_id = null) {
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
	
			$order_created = new \Zend\Db\Sql\Expression(
				"if(exists(select 1 from de_orders where invoice_number = t1.invoice_number) = 1, 'Yes', 'No')"
			);
			
			$select->from(array('t1' => 'de_invoice'))
					->columns(array('id', 'invoice_number', 'created_date', 'email_date', 'xero_tax_rate', 'xero_payment_made', 'xero_date_due', 'order_created' => $order_created))
					->join(array('t2' => 'de_opportunities'), new \Zend\Db\Sql\Expression('t2.id = t1.opp_id'), array("opp_name" => "opportunity_name"), 'left')
					->join(array('t3' => 'de_customers'), new \Zend\Db\Sql\Expression('t3.id = t2.user_id'), array("customer_name" => $customer_name, "email"), 'left')
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
			
			if(!empty($customer_id))
				$select->where(array('t3.id = ?' => $customer_id));
				
			if(!empty($opp_id))
				$select->where(array('t1.opp_id = ?' => $opp_id));
			
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
		}catch(\Exception $e){
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
		
		$responseArr = array();
		parse_str($response['response'], $responseArr);
		$responseArr['code'] = $response['code'];
		
		if ($this->_XeroOAuth->response['code'] == 200) {
			$invoices = $this->_XeroOAuth->parseResponse($this->_XeroOAuth->response['response'], $this->_XeroOAuth->response['format']);
		   
			$response = $this->_XeroOAuth->request('GET', $this->_XeroOAuth->url('Invoice/'.$invoices->Invoices[0]->Invoice->InvoiceID, 'core'), array(), "", 'pdf');
			if ($this->_XeroOAuth->response['code'] == 200) {
				//$myFile = $invoices->Invoices[0]->Invoice->InvoiceID.'.pdf';
				//$fh = fopen($myFile, 'w') or die("can't open file");
				//fwrite($fh, $this->_XeroOAuth->response['response']);
				//fclose($fh);
				//echo "PDF copy downloaded, check your the directory of this script.</br>";
				
				$responseArr['filename'] =  $invoices->Invoices[0]->Invoice->InvoiceID.'.pdf';
				$responseArr['content'] =  $this->_XeroOAuth->response['response'];
			}		   
		}
		
		return $responseArr;
	}
        

    /**
     * Fetch all invices from web service
     */
    public function getAllInvoicesFromWebSerice() {
        $response = $this->_XeroOAuth->request('GET', $this->_XeroOAuth->url('Invoices', 'core'), array('If-Modified-Since' => '2015-11-12T00:00:00'));
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
			
			$responseArr = array('isDeleted' => false);
			$response = $this->_XeroOAuth->request('POST', $this->_XeroOAuth->url('Invoices', 'core'), array(), $xml);
			
			if ($this->_XeroOAuth->response['code'] == 200) {
				$invoices = $this->_XeroOAuth->parseResponse($this->_XeroOAuth->response['response'], $this->_XeroOAuth->response['format']);
				
				if($invoiceTbl->tableGateway->update($data, array("id" => $id)))
					$responseArr['isDeleted'] = true;
					
			}			
			
			parse_str($response['response'], $responseArr);
			$responseArr['code'] = $response['code'];
			 
			return $responseArr;
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
			
			$responseArr = array();
			parse_str($response['response'], $responseArr);
			$responseArr['code'] = $response['code'];
			$responseArr['isDuplicated'] = false;
			
			if ($this->_XeroOAuth->response['code'] == 200) {
				$invoice = $this->_XeroOAuth->parseResponse($this->_XeroOAuth->response['response'], $this->_XeroOAuth->response['format']);
				
				if (count($invoice->Invoices[0]) > 0) {
					$data = (array) $invoiceTbl->getInvoiceById($id);
					unset($data['id']);
					$data['invoice_id'] = $invoice->Invoices[0]->Invoice->InvoiceID;
					$data['invoice_number'] = $invoice->Invoices[0]->Invoice->InvoiceNumber;
					
					if($this->saveDuplicateData($data, $invoiceItems))
						$responseArr['isDuplicated'] = true;
				}
			}
			return $responseArr;  
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
        $contact->addChild("Name", $data['email']);
        $contact->addChild("EmailAddress", $data['email']);
        $invoice->addChild("Date", date("Y-m-d h:i"));
        $invoice->addChild("DueDate", date("Y-m-d h:i"));
        $invoice->addChild("LineAmountTypes", 'Exclusive');
        $lineItems = $invoice->addChild("LineItems");
        // $invoice->addChild("SentToContact", true);

        foreach ($invoiceItems as $value) {
            $item = $lineItems->addChild("LineItem");
            $item->addChild("Description", $value['description']);
            $item->addChild("Quantity", $value['quantity']);
            $linesub = str_replace("$", "", $value['unit_price']);
            $item->addChild("UnitAmount", str_replace("$", "", $value['unit_price']));
			
			if(!empty($data['invoice_number'])){
				$invoice = $this->getInvoiceById($data['invoice_number']);
				$account_code = (string)$invoice->Invoices->Invoice->LineItems->LineItem->AccountCode;
			}else{
				$account_code = $value['account_code'];
			}
			
            $item->addChild("AccountCode", $account_code);
            $subTotal += $linesub * $value['quantity'] - ($value['discount'] / 100) * $linesub;
            $item->addChild("DiscountRate", !empty($value['discount']) ? $value['discount'] : 0);
        }
		
        $invoice->addChild("SubTotal", $subTotal);
        $invoice->addChild("TotalTax", '400');
        $invoice->addChild("Total", $subTotal + 400);
        $dom = dom_import_simplexml($xml);
		
        return $dom->ownerDocument->saveXML($dom->ownerDocument->documentElement);
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
			
			$responseArr = array();
			parse_str($response['response'], $responseArr);
			$responseArr['code'] = $response['code'];
			$responseArr['isCopiedToInvoice'] = false;
			
			if ($this->_XeroOAuth->response['code'] == 200) {
				$invoice = $this->_XeroOAuth->parseResponse($this->_XeroOAuth->response['response'], $this->_XeroOAuth->response['format']);
				
				if (count($invoice->Invoices[0]) > 0) {
					$data = array();
					$data['invoice_id'] = $invoice->Invoices[0]->Invoice->InvoiceID;
					$data['invoice_number'] = $invoice->Invoices[0]->Invoice->InvoiceNumber;
					
					if($invoiceTbl->tableGateway->update($data, array('id' => $id)))
						$responseArr['isCopiedToInvoice'] = true;
				}
			}
			return $responseArr;  
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . urldecode($response['response']) . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch account list from xero api
	 */
	public function getAccounts(){
		try{
			$response = $this->_XeroOAuth->request('GET', $this->_XeroOAuth->url('Accounts', 'core'), array());
			if ($this->_XeroOAuth->response['code'] == 200) {
				$accounts = $this->_XeroOAuth->parseResponse($this->_XeroOAuth->response['response'], $this->_XeroOAuth->response['format']);
				$accountsArray = array();
				foreach($accounts->Accounts->Account as $account){
					$accountsArray[] = array('AccountID' => (string)$account->AccountID,
											 'Code' => (string)$account->Code,
											 'Name' => (string)$account->Name,
											 'Status' => (string)$account->Status);
				}
				return $accountsArray;
			}			
			
			return false;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . urldecode($response['response']) . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Fetch Tracking Options from xero api
	 */
	public function getSalesPersons(){
		try{
			$response = $this->_XeroOAuth->request('GET', $this->_XeroOAuth->url('TrackingCategories', 'core'),
					array('where' => 'Name=="Salesperson"'));
			if ($this->_XeroOAuth->response['code'] == 200) {
				$trackingArray = array();
				$categories = $this->_XeroOAuth->parseResponse($this->_XeroOAuth->response['response'], $this->_XeroOAuth->response['format']);
				foreach($categories->TrackingCategories->TrackingCategory as $category){
					foreach($category->Options->Option as $option) {
						$trackingArray[] = array('TrackingOptionID' => (string)$option->TrackingOptionID,
												 'Name' => (string)$option->Name,
												 'Status' => (string)$option->Status);
					}
				}
				return $trackingArray;
			}			
			
			return false;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . urldecode($response['response']) . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

	/**
	 * Delete all the items from DB
	 * $invoice_id, primry key de_invoice table
	 */
	public function deleteInvoiceQuoteItems($invoice_id){
		try{
			$invoiceItemTbl = $this->_serviceManager->get('Invoice\Model\InvoiceItemTable');
			return $invoiceItemTbl->tableGateway->delete(array('invoice_id' => $invoice_id));
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Update invoice data in database
	 * $invoice_id, primry key de_invoice table
	 * $data, posted data
	 */
	public function updateInvoiceQuote($invoice_id, $data){
		try{
			$identity = $this->_serviceManager->get('AuthService')->getIdentity();
			
			if(!empty($data['invoice_id']))
				$responseArr = $this->updateXero($data);
			
			$responseArr['isUpdated'] = false;
			
			if(empty($data['invoice_id']) || (isset($responseArr) && $responseArr['code'] == 200)){
				$invoiceTbl = $this->_serviceManager->get('Invoice\Model\InvoiceTable');
	
				$updateData['opp_id'] = $data['opp_id'];
				$updateData['xero_date'] = $data['xero_date'];
				$updateData['xero_date_due'] = $data['xero_date_due'];
				$updateData['updated_date'] = date("Y-m-d H:i:s");
				$updateData['updated_by'] = $identity['user_id'];	
				
				if($invoiceTbl->tableGateway->update($updateData, array('id' => $invoice_id))){
					$this->deleteInvoiceQuoteItems($invoice_id);				
					$this->saveItems($data, $invoice_id);
					
					$responseArr['isUpdated'] = true;
				}
			}
                        return $responseArr;
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Update Xero Data to Invoice Table
	 * $invoice_id, primry key de_invoice table
	 * $data, posted data
	 */
	public function updateXerofields($InvoiceID, $xero_tax_rate, $xero_payment_made, $xero_date_due, $invoiceStatus, $xero_SubTotal, $xero_TotalTax, $xero_Total, $xero_AmountDue, $xero_AmountPaid, $xero_AmountCredited){
		try{
			//$identity = $this->_serviceManager->get('AuthService')->getIdentity();
			
				$updateData	 = array();
			
				$invoiceTbl = $this->_serviceManager->get('Invoice\Model\InvoiceTable');			
	
				$updateData['xero_tax_rate'] = $xero_tax_rate;
				$updateData['xero_payment_made'] = $xero_payment_made;
				$updateData['xero_date_due'] = $xero_date_due;	
				$updateData['status'] = $invoiceStatus;	
				
				$updateData['xero_subtotal'] = $xero_SubTotal;
				$updateData['xero_totaltax'] = $xero_TotalTax;
				$updateData['xero_total'] = $xero_Total;
				
				$updateData['xero_amountdue'] = $xero_AmountDue;
				$updateData['xero_amountpaid'] = $xero_AmountPaid;
				$updateData['xero_amountcredited'] = $xero_AmountCredited;
				//print_r($updateData);							
				
				if($invoiceTbl->tableGateway->update($updateData, array('invoice_id' => $InvoiceID))){					
					
					//$responseArr['isUpdated'] = true;
				}
			
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Update invoice data in xero account
	 * $data, post array
	 */
	public function updateXero($data){
		try{	
			$xml = $this->generateXml($data);
			$url = $this->_XeroOAuth->url('Invoices', 'core').'/'.$data['invoice_number'];
			$response = $this->_XeroOAuth->request('POST', $url, array(), $xml);
			
			$responseArr = array();
			parse_str($response['response'], $responseArr);
			$responseArr['code'] = $response['code'];
			
			return $responseArr;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . urldecode($response['response']) . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}

