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
		$tab = $this->params('tab');
        try {
            // Write your code here

            $identity = $sm->get('AuthService')->getIdentity();

            $config = $sm->get('Config');
			
            return array('recordsPerPage' => $config['recordsPerPage'], 'identity' => $identity, 'quotes' => $quotes,
						 'invoices' => $invoices, 'tab' => $tab, 'flashMessages' => $this->flashMessenger()->getMessages());
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
				$quotes['Rows'][$key]['email_date'] = empty($value['email_date']) ? null : date($config['phpDateFormat'], strtotime($value['email_date']));
            }
			echo json_encode($quotes);
			exit;
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
			$customer_id = $params['customer_id'];
			
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			$xero = new \Invoice\Model\Xero($sm);
			if(!empty($keyword)){
				$offset = 0;
			}
			
			$request = $this->getRequest();
			$posts = $request->getPost()->toArray();
			
			$xero = new \Invoice\Model\Xero($sm);
			// Commented because this functionality imlemented using Cron Job every 1 hour, To quick load the invoices and orders
			/*$invoicesFromApi = $xero->getAllInvoicesFromWebSerice();
			
			// Delete invoice - starts			
			$invoiceIds = array();
			foreach($invoicesFromApi->Invoices->Invoice as $invKey => $invValue){
				if($invValue->Status == 'DELETED')
					$invoiceIds[] = "'".$invValue->InvoiceNumber."'";
			}
			
			if(!empty($invoiceIds)){
				$objInvoice = $sm->get('Invoice\Model\InvoiceTable');
				$objInvoice->deleteInvoice($invoiceIds);
			}*/
			// Delete invoice - starts
			
			$invoices = $xero->fetchAllInvoices($limit, $offset, $keyword, $sortdatafield, $sortorder, $customer_id);
						
			foreach ($invoices['Rows'] as $key => $value) {
                
                $invoices['Rows'][$key]['created_date'] = date($config['phpDateFormat'], strtotime($value['created_date']));
                $invoices['Rows'][$key]['email'] = $value['email'];
                //$invoices['Rows'][$key]['payment_mode'] = "";
                //$invoices['Rows'][$key]['date_due'] = date($config['phpDateFormat'], strtotime($value['created_date']));
				$invoices['Rows'][$key]['email_date'] = empty($value['email_date']) ? null : date($config['phpDateFormat'], strtotime($value['email_date']));
				
				/*foreach($invoicesFromApi->Invoices->Invoice as $invKey => $invValue){
					if($invoices['Rows'][$key]['invoice_number'] == $invValue->InvoiceNumber){					
											
						$xero_tax_rate = $invValue->TotalTax * 100 / $invValue->SubTotal;
						$invoices['Rows'][$key]['xero_tax_rate'] = $xero_tax_rate.'%';
						
						$xero_payment_made = !empty($invValue->AmountPaid) ? number_format($invValue->AmountPaid * 100 / $invValue->Total, 2) : $invValue->AmountPaid;
						$invoices['Rows'][$key]['xero_payment_made'] = $xero_payment_made.'%';
						
						$invoices['Rows'][$key]['xero_date_due'] = date($config['phpDateFormat'], strtotime($invValue->DueDate));
					}
				}*/
								
				$invoices['Rows'][$key]['invoice_number'] = $value['invoice_number'];
				$invoices['Rows'][$key]['xero_tax_rate'] = $value['xero_tax_rate'];
				if($value['xero_date_due']!='0000-00-00 00:00:00')
				$invoices['Rows'][$key]['xero_date_due'] = date($config['phpDateFormat'], strtotime($value['xero_date_due']));
				else 
				$invoices['Rows'][$key]['xero_date_due'] ='';
				$invoices['Rows'][$key]['xero_payment_made'] = $value['xero_payment_made'];
				
            }
			
			echo json_encode($invoices);
			exit;
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
			$xero = new \Invoice\Model\Xero($sm);
            // Write your code here
            if ($this->getRequest()->isPost()) {
				
                $post = $this->getRequest()->getPost();
				
                $objInvoice = $sm->get('Invoice\Model\InvoiceTable');                

                if($xero->createQuote($post)){
					$message = 'Quote created successfully';
					$this->flashMessenger()->addMessage(200);
					$this->flashMessenger()->addMessage($message);
				}else{
					$message = 'Unable to create quote';
					$this->flashMessenger()->addMessage(401);
					$this->flashMessenger()->addMessage($message);
				}
				
				return $this->redirect()->toUrl('/invoicequotes');
            }
            $oppId = $this->params('id');
			$oppDataArray['id'] = '';
			$oppDataArray['name'] = '';
			if($oppId > 0){
				$oppModel = $sm->get('Opportunities\Model\OpportunitiesTable');
				$oppData = $oppModel->fetchOpportunityDetails($oppId);
				foreach($oppData as $data){
					$oppDataArray['id'] = $data['id'];
					$oppDataArray['name'] = $data['opportunity_name'];
				}
			}
            $identity = $sm->get('AuthService')->getIdentity();

            $config = $sm->get('Config');
			
			$xeroAccounts = $xero->getAccounts();
			
			$objLookupTable = $sm->get('Inventory\Model\LookupTable');
			$diamondModelOptions = $objLookupTable->fetchDiamondModelOptions();

			return array_merge($diamondModelOptions, array(
				'recordsPerPage' => $config['recordsPerPage'],
				'identity' => $identity,
				'oppDataArray' => $oppDataArray,
				'xeroAccounts' => json_encode($xeroAccounts),
            ));
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
			$xero = new \Invoice\Model\Xero($sm);
			
            // Write your code here
            if ($this->getRequest()->isPost()) {
                $post = $this->getRequest()->getPost();
				
                $objInvoice = $sm->get('Invoice\Model\InvoiceTable'); 
				
				$objOpportunity = $sm->get('Opportunities\Model\OpportunitiesTable'); 
				$opp_id = $post['opp_id']; 
				$oppData = $objOpportunity->fetchOpportunityDetails($opp_id);							
				$post['customer_email'] = $oppData[0]['customer_email'];
				$post['xero_date'] = \De\Service\CommonService::inputToDBDate($post['xero_date']);
				$post['xero_date_due'] = \De\Service\CommonService::inputToDBDate($post['xero_date_due']);
				
                $response = $xero->createInvocie($post);
				$message = $response['code'] == 200 ? 'Invoice added successfully' : $response['oauth_problem_advice'];
				$this->flashMessenger()->addMessage($response['code']);
				$this->flashMessenger()->addMessage($message);
				
				return $this->redirect()->toUrl('/invoicequotes/inv');
            }
            $sm = $this->getServiceLocator();
            $identity = $sm->get('AuthService')->getIdentity();

            $config = $sm->get('Config');
			
			$xeroAccounts = $xero->getAccounts();
			$xeroSalesPersons = $xero->getSalesPersons();
				
			$objLookupTable = $sm->get('Inventory\Model\LookupTable');
			$diamondModelOptions = $objLookupTable->fetchDiamondModelOptions();

			$view = new ViewModel(array_merge($diamondModelOptions, array(
					'recordsPerPage' => $config['recordsPerPage'],
					'identity' => $identity,
					'xeroAccounts' => json_encode($xeroAccounts),
					'xeroSalesPersons' => json_encode($xeroSalesPersons),
			)));
				
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
				$response = $xero->deleteInvoice($id);
				
                if ($response) {
                    $message = $response['code'] == 200 ? 'Invoivce deleted successfully' : 'API authentication failed';
					$this->flashMessenger()->addMessage($response['code']);
					$this->flashMessenger()->addMessage($message);
				}
            }
			
			return $this->redirect()->toUrl('/invoicequotes/inv');
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
                	$message = 'Quote deleted successfully';
					$this->flashMessenger()->addMessage(200);
					$this->flashMessenger()->addMessage($message);
				}else{
					$message = 'Unable to delete quote';
					$this->flashMessenger()->addMessage(401);
					$this->flashMessenger()->addMessage($message);
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
				
				$response = $xero->duplicateInvoice($id);
				if ($response) {
					$message = $response['code'] == 200 ? 'Invoice duplicated successfully' : 'Unable to dupliate invoice';
					$this->flashMessenger()->addMessage($response['code']);
					$this->flashMessenger()->addMessage($message);
				}
            }
			
			return $this->redirect()->toUrl('/invoicequotes/inv');
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
                	$message = 'Quote duplicated successfully';
					$this->flashMessenger()->addMessage(200);
					$this->flashMessenger()->addMessage($message);
				}else{
					$message = 'Unable to duplicate quote';
					$this->flashMessenger()->addMessage(401);
					$this->flashMessenger()->addMessage($message);
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
				$response = $xero->copyToInvoice($id);
				
				if ($response) {
					$message = $response['code'] == 200 ? 'Copied to invoice successfully' : 'Unable to copy to invoice';
					$this->flashMessenger()->addMessage($response['code']);
					$this->flashMessenger()->addMessage($message);
				}
            }
			
			return $this->redirect()->toUrl('/invoicequotes/inv');
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
				$invoice_id = $posts['id'];
				$is_quote = $posts['is_quote'];
				
				$invoiceTable = $sm->get('Invoice\Model\InvoiceTable');
				
				$invoice = $invoiceTable->getInvoiceDetailsById($invoice_id);
				$invoiceItems = $invoiceTable->fetchInvoiceItemsById($invoice_id);
				
				$objXero = new \Invoice\Model\Xero($sm);
				
				if($is_quote == 0)
					$response = $objXero->getInvoicePdf($invoice['invoice_number']);
				
				$tmplParams = array('message' => $posts['message'], 'items' => $invoiceItems, 'invoice' => $invoice, 'config' => $config);	
				
				if($response['code'] == 200 || $is_quote == 1){
					$emailParams['toEmail'] = $invoice['email'];
					$emailParams['toName'] = $invoice['customer_name'];
					$emailParams['subject'] = $posts['subject'];
					$emailParams['message'] = $tmplParams;
					$emailParams['template'] = $is_quote == 0 ? 'invoice.phtml' : 'quotes_template.phtml';
					
					$emailParams['additionalEmails']['cc'][] = array('email' => $config['smtp_details']['ccEmail'], 'name' =>  $config['smtp_details']['ccName']);				
				
					if(isset($posts['copy_email']) && $posts['copy_email'] == 1){
						$emailParams['additionalEmails']['cc'][] = array('email' => $identity['email'], 'name' =>  $identity['first_name'] .' '. $identity['last_name']);
					}
					
					if($is_quote == 0 && (isset($posts['include_pdf']) && $posts['include_pdf'] == 1)){
						$attachments = array(
							array('filename' => $response['filename'], 'content' => $response['content'])
						);
					}
					
					if(isset($posts['attachment_check']) && $posts['attachment_check'] == 1){
						$emailAttachments = json_decode($posts['email_attachment_list']);
						foreach($emailAttachments as $filename){
							$content = file_get_contents($config['documentRoot'] . 'email_attachments/' . $filename);
							$attachments[] = array('filename' => $filename, 'content' => $content);
						}
					}
					
					$response = \De\Service\EmailService::sendEmailWithAttachments($config['smtp_details'], $emailParams, $attachments);
					
					$invoiceTable->updateInvoice($invoice_id, array('email_date' => date('Y-m-d H:i:s')));
					
					$view = new \Zend\View\Renderer\PhpRenderer();
					$resolver = new \Zend\View\Resolver\TemplateMapResolver();
					$templatePath = $config['documentRoot'].'templates/email/';
					
					$resolver->setMap(array(
						'mailTemplate' => $is_quote == 0 ? $templatePath.'invoice.phtml' : $templatePath.'quotes_template.phtml'
					));
					$view->setResolver($resolver);				 	
					$viewModel = new \Zend\View\Model\ViewModel();
					$viewModel->setTemplate('mailTemplate')->setVariables(array('msgholdename' => $emailParams ['toName'], 'data' => $tmplParams));
					$html = $view->render($viewModel);
					
					$insetData = array('invoice_id' => $posts['id'], 'cust_id' => $posts['cust_id'],
									   'subject' => $posts['subject'], 'message' => $posts['message'],
									   'attachments' => $posts['email_attachment_list'], 'email_body' => $html,
									   'created_by' => $identity['user_id'], 'created_date' => date('Y-m-d H:i:s'));
									   
					$insetData['copy_email'] = $config['smtp_details']['ccName'].'<'.$config['smtp_details']['ccEmail'].'>';
								   
					if(isset($posts['copy_email']) && $posts['copy_email'] == 1){
						$insetData['copy_email'] .= ','.$identity['email'].'<'.$identity['first_name'] .' '. $identity['last_name'].'>';
					}
					
					if(isset($posts['include_pdf']) && $posts['include_pdf'] == 1){
						$insetData['pdf_attached'] = 1;
					}
					if(isset($posts['attachment_check']) && $posts['attachment_check'] == 1){
						$insetData['file_attached'] = 1;
					}
					echo $invoiceTable->saveInvoiceEmail($insetData);
					
				}else{
					if($is_quote == 0)
						echo json_encode($response);
					else
						echo json_encode(array('oauth_problem_advice' => 'Unable email quote'));
				}
			}
		
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Render email form to populate in popup
	 */
	public function composeinvoiceemailAction(){
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			
			$config = $sm->get('Config');
			
			$request = $this->getRequest();
			
			$viewRender = $sm->get('ViewRenderer');
			$htmlViewPart = new ViewModel();
			
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$invoice_id = $posts['invoice_id'];
				$is_quote = $posts['is_quote'];
				
				$invoiceTable = $sm->get('Invoice\Model\InvoiceTable');
				
				$invoice = $invoiceTable->getInvoiceDetailsById($invoice_id);
				
				$composeEmailForm = $sm->get('Invoice/Form/ComposeEmailForm');
				$composeEmailForm->setData($invoice);
				$composeEmailForm->get('is_quote')->setValue($is_quote);
				if($is_quote == 1)
					$composeEmailForm->get('send_email')->setAttribute('onclick', 'emailInvoice($(\'#frm_compose_email\'), $(this), \'quote-grid\');');
				
				$htmlViewPart->setTemplate('invoice/index/composeemail')
							 ->setTerminal(true)
							 ->setVariables(array('composeEmailForm' => $composeEmailForm, 'orderFullAttachments' => $orderFullAttachments,
							 					  'invoice' => $invoice, 'config' => $config, 'is_quote' => $is_quote));
			}
		
			$html = $viewRender->render($htmlViewPart);	
			
			return $this->getResponse()->setContent($html);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Send email to customer with attachments
	 */
	public function ajaxgetinvoiceemailAction() {
        try {
			$sm = $this->getServiceLocator();
			$config = $sm->get('Config');
			$params = $this->getRequest()->getQuery()->toArray();
			
			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
			$keyword = $params['keyword'];			
			$sortdatafield = $params['sortdatafield'];
			$sortorder = $params['sortorder'];
			$customer_id = $params['customer_id'];
			
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			if(!empty($keyword)){
				$offset = 0;
			}
						
			$invoiceTable = $sm->get('Invoice/Model/InvoiceTable');
			
			$emails = $invoiceTable->fetchAllEmail($limit, $offset, $sortdatafield, $sortorder, $customer_id, $keyword);
			
			foreach ($emails['Rows'] as $key => $value) {
				$emails['Rows'][$key]['created_date'] = date($config['phpDateFormat'], strtotime($value['created_date']));
				$emails['Rows'][$key]['created_time'] = date('g:i a', strtotime($value['created_date']));
            }
			echo json_encode($emails);
			exit;
        } catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
    }
	
	/**
	 * Generate email conversation between customer and system registeded email in local.php
	 */
	public function viewinvoiceemailAction(){
		try {
			$sm = $this->getServiceLocator();
			$config = $sm->get('Config');
			
			$id = $this->params('id');
						
			$invoiceTable = $sm->get('Invoice/Model/InvoiceTable');
			
			$emailData = $invoiceTable->fetchEmail($id);
			$emailData['to_email'] = $emailData['customer_name'];
			$emailData['to_name'] = $emailData['customer_email'];
			
			// open connection
			$imap = new \De\Service\Imap($config['imap_details']['imapserver'], $config['imap_details']['username'], $config['imap_details']['password'], $config['imap_details']['encryption']);
			
			// stop on error
			if($imap->isConnected()===false)
				die($imap->getError());
			
			// select folder Inbox
			$imap->selectFolder('Inbox');
			//$emailData['customer_email'] = 'nerakpt@gmail.com';
			//$emailData['subject'] = 'Diamond Emporium';
			//$emailsSent = $imap->searchEmails('Subject "'.$emailData['subject'].'" To "'.$emailData['customer_email'].'"');
			$emailsSent = $imap->searchEmails('To "'.$emailData['customer_email'].'"');
			
			$imap->selectFolder('Inbox');
			//$emailsFrom = $imap->searchEmails('Subject "'.$emailData['subject'].'" From "'.$emailData['customer_email'].'"');
			$emailsFrom = $imap->searchEmails('From "'.$emailData['customer_email'].'"');
			
			if(is_array($emailsFrom)){
				$emails = array_merge($emailsSent, $emailsFrom);
				sort($emails);
			}else{
				sort($emailsSent);
				$emails = $emailsSent;
			}
			
			$replyEmailForm = $sm->get('Invoice\Form\ReplyEmailForm');
			$replyEmailForm->get('to_email')->setValue($emailData['customer_email']);
			$replyEmailForm->get('to_name')->setValue($emailData['customer_name']);
			$replyEmailForm->get('subject')->setValue($emailData['subject']);
			
			return array('imap' => $imap, 'emails' => $emails, 'emailData' => $emailData, 'replyEmailForm' => $replyEmailForm);
        } catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
	}
	
	/**
	 * Reply to an email
	 */
	public function replyemailAction(){
		try {
			$sm = $this->getServiceLocator();
			$config = $sm->get('Config');
			$identity = $sm->get('AuthService')->getIdentity();
			
			$request = $this->getRequest();
			
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				
				$emailParams['toEmail'] = $posts['to_email'];
				$emailParams['toName'] = $posts['to_name'];
				$emailParams['subject'] = $posts['subject'];
				$emailParams['message'] = $posts['message'];
				$emailParams['template'] = 'common_template.phtml';
						
				$emailParams['additionalEmails']['cc'][] = array('email' => $config['smtp_details']['ccEmail'], 'name' =>  $config['smtp_details']['ccName']);				
					
				if(isset($posts['copy_email']) && $posts['copy_email'] == 1){
					$emailParams['additionalEmails']['cc'][] = array('email' => $identity['email'], 'name' =>  $identity['first_name'] .' '. $identity['last_name']);
				}
						
				$emailAttachments = json_decode($posts['email_attachment_list']);
				foreach($emailAttachments as $filename){
					$content = file_get_contents($config['documentRoot'] . 'email_attachments/' . $filename);
					$attachments[] = array('filename' => $filename, 'content' => $content);
				}
				
				$response = \De\Service\EmailService::sendEmailWithAttachments($config['smtp_details'], $emailParams, $attachments);
			}
			
			echo 1;
			exit;
        } catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
	}
	
	/**
	 * Edit invoice
	 */
    public function editinvoicequotesAction() {
		
        try {
			$sm = $this->getServiceLocator();
			$xero = new \Invoice\Model\Xero($sm);
			
			$invoice_id = $this->params('id');
			
			$objInvoice = $sm->get('Invoice\Model\InvoiceTable');
			$invoice = $objInvoice->getInvoiceDetailsById($invoice_id);
				
			$invoiceItems = $objInvoice->fetchInvoiceItemsById($invoice_id);
			
			$oppDataArray = array('id' => $invoice['opp_id'], 'name' => $invoice['opp_name']);
			
            // Write your code here
            if ($this->getRequest()->isPost()) {
                $post = $this->getRequest()->getPost();
				
				$objOpportunity = $sm->get('Opportunities\Model\OpportunitiesTable'); 
				$opp_id = $post['opp_id']; 
				$oppData = $objOpportunity->fetchOpportunityDetails($opp_id);							
				$post['customer_email'] = $oppData[0]['customer_email'];
				$post['xero_date'] = \De\Service\CommonService::inputToDBDate($post['xero_date']);
				$post['xero_date_due'] = \De\Service\CommonService::inputToDBDate($post['xero_date_due']);
				
                $response = $xero->updateInvoiceQuote($post['id'], $post);
				
				$message = $response['isUpdated'] == true ? 'Invoice / Quote updated successfully' : 'Unable to update invoice / quote';
				$this->flashMessenger()->addMessage(200);
				$this->flashMessenger()->addMessage($message);
				
				if(empty($post['invoice_id']))
					return $this->redirect()->toUrl('/invoicequotes');
				else
					return $this->redirect()->toUrl('/invoicequotes/inv');
            }
            $sm = $this->getServiceLocator();
            $identity = $sm->get('AuthService')->getIdentity();

            $config = $sm->get('Config');
			
			$xeroAccounts = $xero->getAccounts();
			$xeroSalesPersons = $xero->getSalesPersons();

			$objLookupTable = $sm->get('Inventory\Model\LookupTable');
			$diamondModelOptions = $objLookupTable->fetchDiamondModelOptions();
			
			$invoice['xero_date'] = \De\Service\CommonService::DBToInputDate($invoice['xero_date']);
			$invoice['xero_date_due'] = \De\Service\CommonService::DBToInputDate($invoice['xero_date_due']);

			$view = new ViewModel(array_merge($diamondModelOptions, array('recordsPerPage' => $config['recordsPerPage'], 'identity' => $identity,
										'xeroAccounts' => json_encode($xeroAccounts),
										'xeroSalesPersons' => json_encode($xeroSalesPersons),
										'invoice' => $invoice, 'invoiceItems' => $invoiceItems,
										'oppDataArray' => $oppDataArray, 'tab' => 'inv')));
			$view->setTemplate('invoice/index/editquotes');
			return $view;
        } catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
    }
	
	public function updateinvoiceAction(){
	    $sm = $this->getServiceLocator();
		$xero = new \Invoice\Model\Xero($sm);
		$object = $xero->getAllInvoicesFromWebSerice();					
               		
		foreach($object->Invoices->Invoice as $invoice){	
			
			
		    $InvoiceID = $invoice->InvoiceID;
			$InvoiceNumber = $invoice->InvoiceNumber;
			
			$xero_SubTotal = $invoice->SubTotal;
			$xero_TotalTax =  $invoice->TotalTax;
			$xero_Total =  $invoice->Total;
			
			$xero_AmountDue = $invoice->AmountDue;
			$xero_AmountPaid = $invoice->AmountPaid;
			$xero_AmountCredited = $invoice->AmountCredited;
			 
			$xero_tax_rate = $invoice->TotalTax * 100 / $invoice->SubTotal;			
			$xero_payment_made = !empty($invoice->AmountPaid) ? number_format($invoice->AmountPaid * 100 / $invoice->Total, 2) : $invoice->AmountPaid;			
			 $xero_date_due = date('Y/m/d', strtotime($invoice->DueDate)); //echo "\n";	
			 
			 $invoiceStatus = (array) $invoice->Status;	
			 if($invoiceStatus[0] == 'DELETED')	{			  
			 	$objInvoice = $sm->get('Invoice\Model\InvoiceTable');
				$objInvoice->deleteInvoice(array("'".$InvoiceNumber."'"));
			 
			 }	else { 	
			
			$object = $xero->updateXerofields($InvoiceID, $xero_tax_rate, $xero_payment_made, $xero_date_due, $invoiceStatus[0], $xero_SubTotal, $xero_TotalTax, $xero_Total, $xero_AmountDue, $xero_AmountPaid, $xero_AmountCredited );	
			
			}		
			
		}
	
		exit;
	}
}

