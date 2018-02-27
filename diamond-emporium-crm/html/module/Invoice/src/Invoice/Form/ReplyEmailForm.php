<?php

/**
 * Compose email form
 */
 
namespace Invoice\Form;

use Zend\Form\Form;
 
class ReplyEmailForm extends Form {

	public function __construct() {
		try{
			parent::__construct ( null );		
						
			$this->setAttributes(array('name' => 'frm_reply_email', 'id' => 'frm_reply_email', 'action' => '/uploadmultiplefile', 'enctype' => 'multipart/form-data', 'method' => 'post'));
			$toName = new \Zend\Form\Element\Hidden('to_name');
			$toName->setAttributes(array('id' => 'to_name'));
			$this->add($toName);
			
			$toEmail = new \Zend\Form\Element\Hidden('to_email');
			$toEmail->setAttributes(array('id' => 'to_email'));
			$this->add($toEmail);
			
			$subject = new \Zend\Form\Element\Text('subject');
			$subject->setLabel('Subject');
			$subject->setAttributes(array('class' => 'inputTxt editViewField', 'placeholder' => 'Subject', 'readonly' => 'readonly'));
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
			
			$copy_email = new \Zend\Form\Element\Checkbox('copy_email');
			$copy_email->setLabel('Send me a copy (System user email)');
			$copy_email->setAttributes(array('class' => ''));
			$this->add($copy_email);
						
			$cancel = new \Zend\Form\Element\Button('email_cancel');
			$cancel->setLabel('Cancel');
			$cancel->setValue('Cancel');
			$cancel->setAttributes(array('id' => 'email_cancel', 'class' => 'cmnBtn cancelBtn', "onclick" => "cancelButtonProperty('frm_reply_email', 'replyEmail');"));
			$this->add($cancel);
			
			$save = new \Zend\Form\Element\Button('send_email');
			$save->setLabel('Send');
			$save->setAttributes(array('id' => 'send_email', 'class' => 'cmnBtn', 'onclick' => 'replyEmail($(\'#frm_reply_email\'), $(this));'));
			$this->add($save);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}