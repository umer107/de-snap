<?php

/**
 * This holds all the zend elements related to the Login form
 * Mandatory field(s) in this page are Email Address and Password
 * @author ranjan
 *
 */
 
namespace Customer\Form;

use Zend\Form\Form;
 
class LeadForm extends Form {

	public function __construct($objServiceManager) {
		try{
			parent::__construct ( null );
			
			$objProductsTable = $objServiceManager->get('Customer\Model\ProductsTable');			
			$objStatesTable = $objServiceManager->get('Customer\Model\StatesTable');			
			$objLeadTypesTable = $objServiceManager->get('Customer\Model\LeadTypesTable');
			$objUsersTable = $objServiceManager->get('Customer\Model\UsersTable');
			
			$this->setAttributes(array('name' => 'frm_lead', 'action' => '', 'method' => 'post'));
			
			$lead_id = new \Zend\Form\Element\Hidden('lead_id');
			$this->add($lead_id);
			
			$lead_owner = new \Zend\Form\Element\Select('lead_owner');
			$lead_owner->setLabel('Lead Owner');
			$lead_owner->setAttributes(array('id' => 'lead_owner', 'options' => $objUsersTable->fetchSelectOptions(), 'class' => 'width60p dropdown'));
			$this->add($lead_owner);
			
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
			$mobile->setAttributes(array('id' => 'mobile', 'class' => 'inputTxt width60p', 'maxlength' => 10));
			$this->add($mobile);
			
			$email = new \Zend\Form\Element\Text('email');
			$email->setLabel('Email');
			$email->setAttributes(array('id' => 'email', 'class' => 'inputTxt width60p', 'maxlength' => 128));
			$this->add($email);
			
			$product = new \Zend\Form\Element\Select('product');
			$product->setLabel('Product');
			$product->setAttributes(array('id' => 'product', 'options' => $objProductsTable->fetchSelectOptions(), 'class' => 'width60p dropdown'));
			$this->add($product);
			
			$looking_for = new \Zend\Form\Element\Textarea('looking_for');
			$looking_for->setLabel('What are they looking for?');
			$looking_for->setAttributes(array('id' => 'looking_for', 'rows' => 6));
			$this->add($looking_for);
			
			$budget = new \Zend\Form\Element\Text('budget');
			$budget->setLabel('Budget');
			$budget->setAttributes(array('id' => 'budget', 'class' => 'inputTxt', 'maxlength' => 20, 'data-numeric' => 'yes'));
			$this->add($budget);
			
			$lead_source = new \Zend\Form\Element\Select('lead_source');
			$lead_source->setLabel('Lead Source');
			$lead_source->setAttributes(array('id' => 'lead_source', 'options' => array(0 => 'Select', 'Phone' => 'Phone', 'Web' => 'Web', 'Partner Referrel' => 'Partner Referrel', 'Purchased List' => 'Purchased List', 'Other' => 'Other'), 'class' => 'width60p dropdown'));
			$this->add($lead_source);
			
			$reference_product = new \Zend\Form\Element\Text('reference_product');
			$reference_product->setLabel('Reference Product');
			$reference_product->setAttributes(array('id' => 'reference_product', 'class' => 'inputTxt', 'maxlength' => 128));
			$this->add($reference_product);
			
			$referred_by_name = new \Zend\Form\Element\Text('referred_by_name');
			$referred_by_name->setLabel('Referred by Customer');
			$referred_by_name->setAttributes(array('id' => 'referred_by_name', 'class' => 'inputTxt width60p', 'readonly' => 'readonly'));
			$this->add($referred_by_name);
			
			$referred_by_customer = new \Zend\Form\Element\Hidden('referred_by_customer');
			$referred_by_customer->setAttributes(array('id' => 'referred_by_customer'));
			$this->add($referred_by_customer);
			
			$state = new \Zend\Form\Element\Select('state');
			$state->setLabel('State');
			$state->setAttributes(array('id' => 'state', 'options' => $objStatesTable->fetchSelectOptions(), 'class' => 'width60p dropdown'));
			$this->add($state);
			
			$lead_type = new \Zend\Form\Element\Select('lead_type');
			$lead_type->setLabel('Lead Type');
			$lead_type->setAttributes(array('id' => 'lead_type', 'options' => $objLeadTypesTable->fetchSelectOptions(), 'class' => 'dropdown'));
			$this->add($lead_type);
			
			$preferred_contact = new \Zend\Form\Element\Select('preferred_contact');
			$preferred_contact->setLabel('Preferred Method of Contact');
			$preferred_contact->setAttributes(array('id' => 'preferred_contact', 'options' => array('Phone/Email' => 'Phone/Email', 'Phone' => 'Phone', 'Email' => 'Email'), 'class' => 'dropdown'));
			$this->add($preferred_contact);
			
			$priority = new \Zend\Form\Element\Text('priority');
			$priority->setLabel('Special Instructions');
			$priority->setAttributes(array('id' => 'priority', 'class' => 'inputTxt', 'maxlength' => 200));
			$this->add($priority);
			
			$cancel = new \Zend\Form\Element\Button('cancel');
			$cancel->setLabel('Cancel');
			$cancel->setValue('Cancel');
			$cancel->setAttributes(array('id' => 'cancel', 'class' => 'cmnBtn cancelBtn', "onclick" => "cancelButtonProperty('frm_lead', 'leadsList');"));
			$this->add($cancel);
			
			$save = new \Zend\Form\Element\Button('save');
			$save->setLabel('Save');
			$save->setValue('Save');
			$save->setAttributes(array('id' => 'new_lead_btn', 'class' => 'cmnBtn', 'onclick' => 'saveLead($(\'#frm_lead\'));'));
			$this->add($save);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}