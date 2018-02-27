<?php

/**
 * Zend Framework 2
 *
 * @controller Invoice::Index
 */

namespace Invoice\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController {

    public function indexAction() {
        $sm = $this->getServiceLocator();
        $xero = new \Invoice\Model\Xero($sm);

        $quotes = $xero->getQuotes($this->params()->fromQuery("search", false));
        $quotes->setItemCountPerPage($config['recordsPerPage']);
        $quotes->setCurrentPageNumber($this->params()->fromQuery("q-page", 1));
        $invoices = $xero->getInvoices($this->params()->fromQuery("search-in", false));
        $invoices->setItemCountPerPage(1);
        $invoices->setCurrentPageNumber($this->params()->fromQuery("page", 1));
        try {
            // Write your code here


            $identity = $sm->get('AuthService')->getIdentity();

            $config = $sm->get('Config');

            return array('recordsPerPage' => $config['recordsPerPage'], 'identity' => $identity, "quotes" => $quotes, "invoices" => $invoices);
        } catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
    }

    public function ajaxQuoteAction() {
		try {
			$sm = $this->getServiceLocator();
			$config = $sm->get('Config');
			$params = $this->getRequest()->getQuery()->toArray();
			
			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
			$keyword = $params['keyword'];			
			$sortdatafield = $params['sortdatafield'];
			$sortorder = $params['sortorder'];
			
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			$xero = new \Invoice\Model\Xero($sm);
			if(!empty($keyword)){
				$offset = 0;
			}
			
			$request = $this->getRequest();
			$posts = $request->getPost()->toArray();
			
			$xero = new \Invoice\Model\Xero($sm);
			
			$quotes = $xero->fetchAllQuotes($limit, $offset, $keyword, $sortdatafield, $sortorder);
			
			foreach ($quotes['Rows'] as $key => $value) {
                
                $quotes['Rows'][$key]['created_date'] = date("d/m/y", strtotime($value['created_date']));
                $quotes['Rows'][$key]['email'] = $value['email'];
                $quotes['Rows'][$key]['payment_mode'] = "";
                $quotes['Rows'][$key]['date_due'] = date("d/m/y", strtotime($value['created_date']));
                //$quotes['Rows'][$key]['send_email'] = '<a class="cmnBtn vm marT0" href="javascript:;">Send</a> &nbsp; <span class="emailSentIcon"></span> &nbsp; 12/12/14';
                /*$quotes['Rows'][$key]["options"] = '<div class="editDrop marB0 vm quoteOptions"> <span class="editTaskBtn"></span>
                        <ul style="display: none;" class="showWithAimat">
                          <li><a href="/invoice/delete/'.$value['id'].'">Delete</a></li>
                          <li><a href="javascript:;">Duplicate</a></li>
                          <li><a href="javascript:;">Copy Items to Invoice</a></li>
                          <li><a href="javascript:;">Create Order</a></li>
                        </ul>
                      </div>';*/
            }
			echo json_encode($quotes);
			exit;

			/*$sm = $this->getServiceLocator();
			$xero = new \Invoice\Model\Xero($sm);
        
            $quotes = $xero->getQuotes($this->params()->fromQuery("keyword", false));
            $quotes->setItemCountPerPage($this->params()->fromQuery("pagesize",10));
			$quotes->setCurrentPageNumber($this->params()->fromQuery("pagenum", 1));
            $results = array();
            $rows = array();
            $key=0;
            foreach ($quotes as $quote) {
                
                $results['created'] = date("d/m/y", strtotime($quote->created_date));
                $results['customer'] = $quote->first_name . " " . $quote->first_name;
                $results['email'] = $quote->email;
                $results['payment_mode'] = "";
                $results['date_due'] = date("d/m/y", strtotime($quote->created_date));
                $results['send_email'] = '<a class="cmnBtn vm marT0" href="javascript:;">Send</a> &nbsp; <span class="emailSentIcon"></span> &nbsp; 12/12/14';
                $results["options"] = '<div class="editDrop marB0 vm quoteOptions"> <span class="editTaskBtn"></span>
                        <ul style="display: none;" class="showWithAimat">
                          <li><a href="/invoice/delete/<?php echo $invoice->id?>">Delete</a></li>
                          <li><a href="javascript:;">Duplicate</a></li>
                          <li><a href="javascript:;">Copy Items to Invoice</a></li>
                          <li><a href="javascript:;">Create Order</a></li>
                        </ul>
                      </div>';
                $data['rows'][$key] = $results;
                $key++;
                
            }
			$data["total"]  = $quotes->getTotalItemCount();
			$data["page"]= $this->params()->fromQuery("pagenum", 1);
			$data[ "records"] = count($data['rows']);
			echo json_encode($data);
            exit;*/
        } catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
    }

     public function ajaxInvoiceAction() {
        try {
			$sm = $this->getServiceLocator();
			$config = $sm->get('Config');
			$params = $this->getRequest()->getQuery()->toArray();
			
			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
			$keyword = $params['keyword'];			
			$sortdatafield = $params['sortdatafield'];
			$sortorder = $params['sortorder'];
			
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			$xero = new \Invoice\Model\Xero($sm);
			if(!empty($keyword)){
				$offset = 0;
			}
			
			$request = $this->getRequest();
			$posts = $request->getPost()->toArray();
			
			$xero = new \Invoice\Model\Xero($sm);
			
			$invoices = $xero->fetchAllInvoices($limit, $offset, $keyword, $sortdatafield, $sortorder);
			
			foreach ($invoices['Rows'] as $key => $value) {
                
                $invoices['Rows'][$key]['created_date'] = date("d/m/y", strtotime($value['created_date']));
                $invoices['Rows'][$key]['email'] = $value['email'];
                $invoices['Rows'][$key]['payment_mode'] = "";
                $invoices['Rows'][$key]['date_due'] = date("d/m/y", strtotime($value['created_date']));
                //$invoices['Rows'][$key]['send_email'] = '<a class="cmnBtn vm marT0" href="javascript:;">Send</a> &nbsp; <span class="emailSentIcon"></span> &nbsp; 12/12/14';
               /* $invoices['Rows'][$key]["options"] = '<div class="editDrop marB0 vm quoteOptions"> <span class="editTaskBtn"></span>
                        <ul style="display: none;" class="showWithAimat">
                          <li><a href="/invoice/delete/'.$value['id'].'">Delete</a></li>
                          <li><a href="javascript:;">Duplicate</a></li>
                          <li><a href="javascript:;">Copy Items to Invoice</a></li>
                          <li><a href="javascript:;">Create Order</a></li>
                        </ul>
                      </div>';*/
            }
			echo json_encode($invoices);
			exit;
			
			/*$sm = $this->getServiceLocator();
        	$xero = new \Invoice\Model\Xero($sm);
		
            $quotes = $xero->getInvoices($this->params()->fromQuery("keyword", false));
            $quotes->setItemCountPerPage($this->params()->fromQuery("pagesize",10));
			$quotes->setCurrentPageNumber($this->params()->fromQuery("pagenum", 1));
            $results = array();
            $rows = array();
            $key=0;
            foreach ($quotes as $quote) {
                
                $results['created'] = date("d/m/y", strtotime($quote->created_date));
                $results['customer'] = $quote->first_name . " " . $quote->first_name;
                $results['email'] = $quote->email;
                $results['payment_mode'] = "";
                $results['date_due'] = date("d/m/y", strtotime($quote->created_date));
                $results['send_email'] = '<a class="cmnBtn vm marT0" href="javascript:;">Send</a> &nbsp; <span class="emailSentIcon"></span> &nbsp; 12/12/14';
                $results["options"] = '<div class="editDrop marB0 vm quoteOptions"> <span class="editTaskBtn"></span>
                        <ul style="display: none;" class="showWithAimat">
                          <li><a href="/invoice/delete/<?php echo $invoice->id?>">Delete</a></li>
                          <li><a href="javascript:;">Duplicate</a></li>
                          <li><a href="javascript:;">Copy Items to Invoice</a></li>
                          <li><a href="javascript:;">Create Order</a></li>
                        </ul>
                      </div>';
                $data['rows'][$key] = $results;
                $key++;
                
            }
            $data["total"]  = $quotes->getTotalItemCount();
			$data["page"]= $this->params()->fromQuery("pagenum", 1);
			$data[ "records"] = count($data['rows']);
			echo json_encode($data);
            exit;*/
        } catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
    }
	
	/**
	 * Creates quote
	 */
    
    public function newquotesAction() {
		
        try {
			$sm = $this->getServiceLocator();
			
            // Write your code here
            if ($this->getRequest()->isPost()) {
                $post = $this->getRequest()->getPost();

                $objInvoice = $sm->get('Invoice\Model\InvoiceTable');
                $xero = new \Invoice\Model\Xero($sm);

                $xero->createQuote($post);
				
				return $this->redirect()->toUrl('/invoicequotes');
            }
            
            $identity = $sm->get('AuthService')->getIdentity();

            $config = $sm->get('Config');
			
            return array('recordsPerPage' => $config['recordsPerPage'], 'identity' => $identity);
        } catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
    }
	
	/**
	 * Creates invoice
	 */
    public function newinvoiceAction() {
		
        try {
			$sm = $this->getServiceLocator();
			
            // Write your code here
            if ($this->getRequest()->isPost()) {
                $post = $this->getRequest()->getPost();

                $objInvoice = $sm->get('Invoice\Model\InvoiceTable');
                $xero = new \Invoice\Model\Xero($sm);

                $xero->createInvocie($post);
				
				return $this->redirect()->toUrl('/invoicequotes');
            }
            $sm = $this->getServiceLocator();
            $identity = $sm->get('AuthService')->getIdentity();

            $config = $sm->get('Config');
			
			$view = new ViewModel(array('recordsPerPage' => $config['recordsPerPage'], 'identity' => $identity));
			$view->setTemplate('invoice/index/newquotes');
			return $view;
        } catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
    }

	/**
	 * Delete invoice
	 */
    public function deleteinvoiceAction() {
        $id = $this->params('id');
		$sm = $this->getServiceLocator();
		
        try {
            if ($id) {
                $xero = new \Invoice\Model\Xero($sm);
                if ($xero->deleteInvoice($id)) {
                    $this->flashMessenger()->addMessage('Invoice deleted successfully');
                }
            }
			
			return $this->redirect()->toUrl('/invoicequotes');
        } catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
    }
	
	/**
	 * Delete quote
	 */
	public function deletequoteAction(){
		$id = $this->params('id');
		$sm = $this->getServiceLocator();
		
		try {
            if ($id) {
                $xero = new \Invoice\Model\Xero($sm);
                if ($xero->deleteQuote($id)) {
                    $this->flashMessenger()->addMessage('Quote deleted successfully');
                }
            }
			
			return $this->redirect()->toUrl('/invoicequotes');
        } catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
	}
	
	/**
	 * Duplicate invoice
	 */
	public function duplicateinvoiceAction(){
		$id = $this->params('id');
		$sm = $this->getServiceLocator();
		
		try {
            if ($id) {
                $xero = new \Invoice\Model\Xero($sm);
                if ($xero->duplicateInvoice($id)) {
                    $this->flashMessenger()->addMessage('Invoice duplicated successfully');
                }
            }
			
			return $this->redirect()->toUrl('/invoicequotes');
        } catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
	}
	
	
	/**
	 * Duplicate quote
	 */
	public function duplicatequoteAction(){
		$id = $this->params('id');
		$sm = $this->getServiceLocator();
		
		try {
            if ($id) {
                $xero = new \Invoice\Model\Xero($sm);
                if ($xero->duplicateQuote($id)) {
                    $this->flashMessenger()->addMessage('Invoice duplicated successfully');
                }
            }
			
			return $this->redirect()->toUrl('/invoicequotes');
        } catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
	}
	
	/**
	 * Convert quote into invoice
	 */
	public function copytoinvoiceAction(){
		$id = $this->params('id');
		$sm = $this->getServiceLocator();
		
		try {
            if ($id) {
                $xero = new \Invoice\Model\Xero($sm);
                if ($xero->copyToInvoice($id)) {
                    $this->flashMessenger()->addMessage('Invoice deleted successfully');
                }
            }
			
			return $this->redirect()->toUrl('/invoicequotes');
        } catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
	}

	/**
	 * Email invoice pdf to customer
	 */
	 public function emailinvoiceAction(){
	 	try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			
			if($request->isPost()){
			
				$posts = $request->getPost()->toArray();
				$invoice_id = $posts['invoice_id'];
				
				$invoiceTable = $sm->get('Invoice\Model\InvoiceTable');
				
				$invoice = $invoiceTable->getInvoiceDetailsById($invoice_id);
				
				$objXero = new \Invoice\Model\Xero($sm);
				$content = $objXero->getInvoicePdf($invoice['invoice_number']);	
				
				$emailParams['toEmail'] = 'mailat.ranjan@gmail.com';$invoice['customer_name'];
				$emailParams['toName'] = 'Ranjan';$invoice['email'];
				$emailParams['subject'] = 'Invoice';
				$emailParams['template'] = 'invoice.phtml';
				
				$attachments = array(
					array('filename' => $content['filename'], 'content' => $content['content'])
				);
				$response = \De\Service\EmailService::sendEmailWithAttachments($config['smtp_details'], $emailParams, $attachments);
				print_r($response);exit;
				echo $invoiceTable->updateInvoice($invoice_id, array('email_date' => date('Y-m-d H:i:s')));
			}
		
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
}
