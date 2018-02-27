<?php

/**
 * Compose email form
 */
 
namespace Invoice\Form;

use Zend\Form\Form;
 
class ComposeEmailForm extends Form {

	public function __construct() {
		try{
			parent::__construct ( null );		
						
			$this->setAttributes(array('name' => 'frm_compose_email', 'id' => 'frm_compose_email', 'action' => '/uploadmultiplefile', 'enctype' => 'multipart/form-data', 'method' => 'post'));
			
			$invoice_id = new \Zend\Form\Element\Hidden('id');
			$this->add($invoice_id);
			
			$is_quote = new \Zend\Form\Element\Hidden('is_quote');
			$this->add($is_quote);
			
			$cust_id = new \Zend\Form\Element\Hidden('cust_id');
			$cust_id->setAttributes(array('id' => 'cust_id'));
			$this->add($cust_id);
			
			$subject = new \Zend\Form\Element\Text('subject');
			$subject->setLabel('Subject');
			$subject->setAttributes(array('id' => 'subject', 'class' => 'inputTxt editViewField', 'placeholder' => 'Subject'));
			$this->add($subject);
			
			$comment = new \Zend\Form\Element\Textarea('message');
			$comment->setLabel('Message');
			$comment->setAttributes(array('id' => 'message', 'rows' => 6, 'class' => 'textareaGlobalMaxLength', 'placeholder' => 'Write a comment...'));
			$this->add($comment);
			
			$email_attachments = new \Zend\Form\Element\File('email_attachments');
			$email_attachments->setLabel('Attach File');
			$email_attachments->setAttributes(array('id' => 'email_attachments', 'multiple' => 'multiple', 'onchange' => 'uploadFiles($(this).closest(\'form\'), $(this).siblings(\'input[name=email_attachment_list]\'), \'email_attachments\');'));
			$this->add($email_attachments);
			
			$email_attachment_list = new \Zend\Form\Element\Hidden('email_attachment_list');
			$email_attachment_list->setAttributes(array('id' => 'email_attachment_list'));
			$this->add($email_attachment_list);
			
			$attachment_check = new \Zend\Form\Element\Checkbox('attachment_check');
			$attachment_check->setLabel('Include files as attachments');
			$attachment_check->setAttributes(array('class' => ''));
			$this->add($attachment_check);
			
			$copy_email = new \Zend\Form\Element\Checkbox('copy_email');
			$copy_email->setLabel('Send me a copy (System user email)');
			$copy_email->setAttributes(array('class' => ''));
			$this->add($copy_email);
			
			$include_pdf = new \Zend\Form\Element\Checkbox('include_pdf');
			$include_pdf->setLabel('Include pdf attachment');
			$include_pdf->setAttributes(array('class' => ''));
			$this->add($include_pdf);
						
			$cancel = new \Zend\Form\Element\Button('email_cancel');
			$cancel->setLabel('Cancel');
			$cancel->setValue('Cancel');
			$cancel->setAttributes(array('id' => 'email_cancel', 'class' => 'cmnBtn cancelBtn', "onclick" => "cancelButtonProperty('frm_compose_email', 'composeEmail');"));
			$this->add($cancel);
			
			$save = new \Zend\Form\Element\Button('send_email');
			$save->setLabel('Send');
			$save->setAttributes(array('id' => 'send_email', 'class' => 'cmnBtn', 'onclick' => 'emailInvoice($(\'#frm_compose_email\'), $(this), \'invoice-grid\');'));
			$this->add($save);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}