<?php

/**
 * Form to create new order
 */
 
namespace Order\Form;

use Zend\Form\Form;
 
class OrderForm extends Form {

	public function __construct() {
		try{
			parent::__construct ( null );		
						
			$this->setAttributes(array('name' => 'frm_order', 'id' => 'frm_order', 'action' => '/uploadmultiplefile', 'enctype' => 'multipart/form-data', 'method' => 'post'));
			
			$id = new \Zend\Form\Element\Hidden('id');
			$id->setAttributes(array('id' => 'order_id'));
			$this->add($id);
			
			$cust_id = new \Zend\Form\Element\Hidden('cust_id');
			$cust_id->setAttributes(array('id' => 'cust_id'));
			$this->add($cust_id);
			
			$order_attachment = new \Zend\Form\Element\Hidden('multipleimagesHidden');
			$order_attachment->setAttributes(array('id' => 'multipleimagesHidden'));
			$this->add($order_attachment);
			
			$opp_id = new \Zend\Form\Element\Hidden('opp_id');
			$opp_id->setAttributes(array('id' => 'opp_id'));
			$this->add($opp_id);
			
			$exp_delivery_date = new \Zend\Form\Element\Text('exp_delivery_date');
			$exp_delivery_date->setLabel('Expected Delivery Date');
			$exp_delivery_date->setAttributes(array('id' => 'exp_delivery_date', 'class' => 'inputTxt', 'readonly' => 'readonly', 'placeholder' => 'DD/MM/YYYY'));
			$this->add($exp_delivery_date);
			
			$opp_name = new \Zend\Form\Element\Text('opp_name');
			$opp_name->setLabel('Select Opportunity');
			$opp_name->setAttributes(array('id' => 'opp_name', 'readonly' => 'readonly', 'class' => 'inputTxt width60p'));
			$this->add($opp_name);
			
			$invoice_number = new \Zend\Form\Element\Text('invoice_number');
			$invoice_number->setLabel('Attach an invoice/quote to an order');
			$invoice_number->setAttributes(array('id' => 'invoice_number', 'readonly' => 'readonly', 'class' => 'inputTxt width60p'));
			$this->add($invoice_number);
			
			$comment = new \Zend\Form\Element\Textarea('comment');
			$comment->setLabel('Comment');
			$comment->setAttributes(array('id' => 'comment', 'rows' => 6, 'class' => 'textareaGlobalMaxLength', 'placeholder' => 'Write a comment...'));
			$this->add($comment);
			
			$order_file = new \Zend\Form\Element\File('order_file');
			$order_file->setLabel('Attach File');
			$order_file->setAttributes(array('id' => 'order_file', 'multiple' => 'multiple', 'onchange' => 'uploadImages($(this).closest(\'form\'), $(this).siblings(\'input[name=multipleimagesHidden]\'), \'order_attachment\');'));
			$this->add($order_file);
			
			$special_request = new \Zend\Form\Element\Text('special_request');
			$special_request->setLabel('Special Request');
			$special_request->setAttributes(array('class' => 'inputTxt width60p', 'placeholder' => 'Special Request'));
			$this->add($special_request);
						
			$cancel = new \Zend\Form\Element\Button('cancel');
			$cancel->setLabel('Cancel');
			$cancel->setValue('Cancel');
			$cancel->setAttributes(array('id' => 'order_cancel', 'class' => 'cmnBtn cancelBtn', "onclick" => "cancelButtonProperty('frm_order', 'createOrder');"));
			$this->add($cancel);
			
			$save = new \Zend\Form\Element\Button('order_save');
			$save->setLabel('Create');
			$save->setAttributes(array('id' => 'order_save', 'class' => 'cmnBtn', 'onclick' => 'createOrder($(\'#frm_order\'));'));
			$this->add($save);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}