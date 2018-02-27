<?php

/**
 * This holds all the zend elements related to the Login form
 * Mandatory field(s) in this page are Email Address and Password
 * @author ranjan
 *
 */
 
namespace Customer\Form;

use Zend\Form\Form;
 
class NewCustomerForm extends Form {
	
	private $objStatesTable;
	
	public function __construct($objServiceManager) {
		try{
			parent::__construct ( null );
			
			$this->objStatesTable = $objServiceManager->get('Customer\Model\StatesTable');
			
			$config = $objServiceManager->get('Config');
			
			$this->setAttributes(array('name' => 'frm_new_customer', 'action' => '', 'method' => 'post'));
			
			$partner_id = new \Zend\Form\Element\Hidden('partner_id');
			$partner_id->setAttribute('id', 'partner_id');
			$this->add($partner_id);
			
			$title = new \Zend\Form\Element\Select('title');
			$title->setLabel('Title');
			$title->setAttributes(array('id' => 'title', 'options' => array('Mr' => 'Mr', 'Ms' => 'Ms', 'Mrs' => 'Mrs', 'Miss' => 'Miss'), 'class' => 'width60p dropdown'));
			$this->add($title);
			
			$first_name = new \Zend\Form\Element\Text('first_name');
			$first_name->setLabel('First Name');
			$first_name->setAttributes(array('id' => 'first_name', 'class' => 'inputTxt', 'maxlength' => 64));
			$this->add($first_name);
			
			$last_name = new \Zend\Form\Element\Text('last_name');
			$last_name->setLabel('Last Name');
			$last_name->setAttributes(array('id' => 'last_name', 'class' => 'inputTxt', 'maxlength' => 64));
			$this->add($last_name);
			
			$mobile = new \Zend\Form\Element\Text('mobile');
			$mobile->setLabel('Mobile');
			$mobile->setAttributes(array('id' => 'customer_mobile', 'class' => 'inputTxt width60p', 'maxlength' => 10));
			$this->add($mobile);
			
			$email = new \Zend\Form\Element\Text('email');
			$email->setLabel('Email');
			$email->setAttributes(array('id' => 'customer_email', 'class' => 'inputTxt width60p', 'maxlength' => 128));
			$this->add($email);
			
			$address1 = new \Zend\Form\Element\Text('address1');
			$address1->setLabel('Address Line 1');
			$address1->setAttributes(array('id' => 'customer_address1', 'class' => 'inputTxt'));
			$this->add($address1);
			
			$postcode = new \Zend\Form\Element\Text('postcode');
			$postcode->setLabel('Postcode');
			$postcode->setAttributes(array('id' => 'customer_postcode', 'class' => 'inputTxt', 'maxlength' => 6, 'data-numeric' => 'yes'));
			$this->add($postcode);
			
			$country_id = new \Zend\Form\Element\Text('country_id');
			$country_id->setLabel('Country');
			$country_id->setAttributes(array('id' => 'customer_country_id', 'class' => 'inputTxt', 'value' => 'Australia', 'readonly' => 'readonly'));
			$this->add($country_id);
			
			$state = new \Zend\Form\Element\Select('state_id');
			$state->setLabel('State');
			$state->setAttributes(array('id' => 'customer_state_id', 'options' => $this->objStatesTable->fetchSelectOptions(), 'class' => 'dropdown width100p editViewField'));
			$this->add($state);
			
			$partner_name = new \Zend\Form\Element\Text('partner_name');
			$partner_name->setLabel('Partner');
			$partner_name->setAttributes(array('id' => 'partner_name', 'class' => 'inputTxt width60p', 'readonly' => 'readonly'));
			$this->add($partner_name);
			
			$cancel = new \Zend\Form\Element\Button('cancel');
			$cancel->setLabel('Cancel');
			$cancel->setValue('Cancel');
			$cancel->setAttributes(array('id' => 'cancel', 'class' => 'cmnBtn cancelBtn', "onclick" => "cancelButtonProperty('frm_new_customer', 'new_customer_form');"));
			$this->add($cancel);
			
			$save = new \Zend\Form\Element\Button('save');
			$save->setLabel('Save');
			$save->setValue('Save');
			//$save->setAttributes(array('id' => 'create_customer_btn', 'class' => 'cmnBtn', 'onclick' => 'createNewCustomer($(\'#frm_customer\'), '.$config['recordsPerPage'].');'));
			$save->setAttributes(array('id' => 'create_customer_btn', 'class' => 'cmnBtn', 'onclick' => 'createNewCustomer($(\'#frm_new_customer\'));'));
			$this->add($save);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}