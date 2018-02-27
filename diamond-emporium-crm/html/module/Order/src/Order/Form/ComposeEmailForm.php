<?php

/**
 * Compose email form
 */
 
namespace Order\Form;

use Zend\Form\Form;
 
class ComposeEmailForm extends Form {

	public function __construct() {
		try{
			parent::__construct ( null );		
						
			$this->setAttributes(array('name' => 'frm_compose_email', 'id' => 'frm_compose_email', 'action' => '/uploadmultiplefile', 'enctype' => 'multipart/form-data', 'method' => 'post'));
			
			$milestone_id = new \Zend\Form\Element\Hidden('milestone_id');
			$this->add($milestone_id);
			
			$milestone_type_id = new \Zend\Form\Element\Hidden('milestone_type_id');
			$this->add($milestone_type_id);
			
			$step = new \Zend\Form\Element\Hidden('step');
			$this->add($step);
			
			$subject = new \Zend\Form\Element\Text('subject');
			$subject->setLabel('Subject');
			$subject->setAttributes(array('class' => 'inputTxt editViewField', 'placeholder' => 'Subject'));
			$this->add($subject);
			
			$comment = new \Zend\Form\Element\Textarea('message');
			$comment->setLabel('Message');
			$comment->setAttributes(array('rows' => 6, 'class' => 'textareaGlobalMaxLength', 'placeholder' => 'Write a comment...'));
			$this->add($comment);
			
			$email_attachments = new \Zend\Form\Element\File('email_attachments');
			$email_attachments->setLabel('Attach File');
			$email_attachments->setAttributes(array('multiple' => 'multiple', 'onchange' => 'uploadFiles($(this).closest(\'form\'), $(this).siblings(\'input[name=email_attachment_list]\'), \'email_attachments\');'));
			$this->add($email_attachments);
			
			$email_attachment_list = new \Zend\Form\Element\Hidden('email_attachment_list');
			$this->add($email_attachment_list);
			
			$attachment_check = new \Zend\Form\Element\Checkbox('attachment_check');
			$attachment_check->setLabel('Include files as attachments');
			$attachment_check->setAttributes(array('class' => ''));
			$this->add($attachment_check);
			
			$copy_email = new \Zend\Form\Element\Checkbox('copy_email');
			$copy_email->setLabel('Send me a copy (System user email)');
			$copy_email->setAttributes(array('class' => ''));
			$this->add($copy_email);
						
			$cancel = new \Zend\Form\Element\Button('email_cancel');
			$cancel->setLabel('Cancel');
			$cancel->setValue('Cancel');
			$cancel->setAttributes(array('class' => 'cmnBtn cancelBtn', "onclick" => "cancelButtonProperty('frm_compose_email', 'composeEmail');"));
			$this->add($cancel);
			
			$save = new \Zend\Form\Element\Button('send_email');
			$save->setLabel('Send');
			$save->setAttributes(array('class' => 'cmnBtn', 'onclick' => 'emailMilestone($(\'#frm_compose_email\'), $(this));'));
			$this->add($save);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}