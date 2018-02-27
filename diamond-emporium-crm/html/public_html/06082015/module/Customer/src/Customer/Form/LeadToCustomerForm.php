<?php

/**
 * This holds all the zend elements related to the Login form
 * Mandatory field(s) in this page are Email Address and Password
 * @author ranjan
 *
 */
 
namespace Customer\Form;

use Zend\Form\Form;
 
class LeadToCustomerForm extends Form {
	
	public function __construct($objServiceManager) {
		try{
			parent::__construct ( null );
			
			$this->setAttributes(array('name' => 'frm_lead_convert', 'action' => '', 'method' => 'post'));
			
			$lead_id = new \Zend\Form\Element\Hidden('lead_id');
			$lead_id->setAttribute('id', 'lead_id');
			$this->add($lead_id);
			
			$customer_id = new \Zend\Form\Element\Hidden('customer_id');
			$customer_id->setAttribute('id', 'customer_id');
			$this->add($customer_id);
			
			$matched_customer_id = new \Zend\Form\Element\Hidden('matched_customer_id');
			$matched_customer_id->setAttribute('id', 'matched_customer_id');
			$this->add($matched_customer_id);
			
			$objUsersTable = $objServiceManager->get('Customer\Model\UsersTable');
			
			$lead_owner = new \Zend\Form\Element\Select('lead_owner');
			$lead_owner->setLabel('Record Owner');
			$lead_owner->setAttributes(array('id' => 'lead_owner', 'options' => $objUsersTable->fetchSelectOptions(), 'class' => 'width60p dropdown1'));
			$this->add($lead_owner);
			
			$customer_name = new \Zend\Form\Element\Text('customer_name');
			$customer_name->setLabel('Customer Account');
			$customer_name->setAttributes(array('id' => 'customer_name', 'class' => 'inputTxt width60p', 'readonly' => 'readonly'));
			$this->add($customer_name);
			
			$opportunity_name = new \Zend\Form\Element\Text('opportunity_name');
			$opportunity_name->setLabel('Opportunity Name');
			$opportunity_name->setAttributes(array('id' => 'opportunity_name', 'class' => 'inputTxt'));
			$this->add($opportunity_name);
			
			$progress_of_opportunity = new \Zend\Form\Element\Select('progress_of_opportunity');
			$progress_of_opportunity->setLabel('Progress');
			$progress_of_opportunity->setAttributes(array('id' => 'progress_of_opportunity', 'options' => array('' => 'Select', 10 => 10, 20 => 20, 30 => 30, 40 => 40, 50 => 50, 60 => 60, 70 => 70, 80 => 80, 90 => 90, 100 => 100), 'class' => 'dropdown1'));
			$this->add($progress_of_opportunity);
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}