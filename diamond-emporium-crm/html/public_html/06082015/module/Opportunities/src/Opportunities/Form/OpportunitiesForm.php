<?php

/**
 * This holds all the zend elements related to the Login form
 * Mandatory field(s) in this page are Email Address and Password
 * @author ranjan
 *
 */
 
namespace Opportunities\Form;

use Zend\Form\Form;
 
class OpportunitiesForm extends Form {
	
	public function __construct($objProductsTable, $objStatesTable, $objLeadTypesTable) {
		try{
			parent::__construct ( null );
			
			$this->setAttributes(array('name' => 'frm_opportunity', 'action' => '', 'method' => 'post'));
			
			$lead_id = new \Zend\Form\Element\Hidden('lead_id');
			$this->add($lead_id);
			
			$customer_mobile = new \Zend\Form\Element\Text('customer_mobile');
			$customer_mobile->setLabel('Customer Lookup');
			$customer_mobile->setAttributes(array('id' => 'mobile', 'class' => 'inputTxt width60p', 'readonly' => 'readonly'));
			$this->add($customer_mobile);
			
			$customer_mobile_data = new \Zend\Form\Element\Hidden('customer_mobile_data');
			$customer_mobile_data->setAttributes(array('id' => 'customer_mobile_data'));
			$this->add($customer_mobile_data);
			
			$opportunity_type = new \Zend\Form\Element\Select('opportunity_type');
			$opportunity_type->setLabel('Opportunity Type');
			$opportunity_type->setAttributes(array('id' => 'opportunity_type', 'options' => array('' => 'Select', 'Phone' => 'Phone', 'Email' => 'Email'), 'class' => 'dropdown'));
			$this->add($opportunity_type);
			
			$lead_source = new \Zend\Form\Element\Select('lead_source');
			$lead_source->setLabel('Lead Source');
			$lead_source->setAttributes(array('id' => 'lead_source', 'options' => array('' => 'Select', 'Web' => 'Web', 'Phone Inquiry' => 'Phone Inquiry', 'Partner Referral' => 'Partner Referral', 'Purchased List' => 'Purchased List', 'Other' => 'Other'), 'class' => 'dropdown'));
			$this->add($lead_source);
			
			$referred_by_name = new \Zend\Form\Element\Text('referred_by_name');
			$referred_by_name->setLabel('Referred by Customer');
			$referred_by_name->setAttributes(array('id' => 'referred_by_name', 'class' => 'inputTxt width60p', 'readonly' => 'readonly'));
			$this->add($referred_by_name);
			
			$referred_by_customer = new \Zend\Form\Element\Hidden('referred_by_customer');
			$referred_by_customer->setAttributes(array('id' => 'referred_by_customer'));
			$this->add($referred_by_customer);
			
			$looking_for = new \Zend\Form\Element\Textarea('looking_for');
			$looking_for->setLabel('What are they looking for?');
			$looking_for->setAttributes(array('id' => 'looking_for'));
			$this->add($looking_for);
			
			$product = new \Zend\Form\Element\Select('product');
			$product->setLabel('Product');
			$product->setAttributes(array('id' => 'product', 'options' => $objProductsTable->fetchSelectOptions(), 'class' => 'width60p dropdown'));
			$this->add($product);
			
			$preferred_contact = new \Zend\Form\Element\Select('preferred_contact');
			$preferred_contact->setLabel('Preferred Method to Contact');
			$preferred_contact->setAttributes(array('id' => 'preferred_contact', 'options' => array('Phone/Email' => 'Phone/Email', 'Phone' => 'Phone', 'Email' => 'Email'), 'class' => 'dropdown'));
			$this->add($preferred_contact);
			
			$progress_of_opportunity = new \Zend\Form\Element\Select('progress_of_opportunity');
			$progress_of_opportunity->setLabel('Progress');
			$progress_of_opportunity->setAttributes(array('id' => 'progress_of_opportunity', 'options' => array('' => 'Select', 10 => 10, 20 => 20, 30 => 30, 40 => 40, 50 => 50, 60 => 60, 70 => 70, 80 => 80, 90 => 90, 100 => 100), 'class' => 'dropdown'));
			$this->add($progress_of_opportunity);
			
			$urgency = new \Zend\Form\Element\Select('urgency');
			$urgency->setLabel('Urgency Scale');
			$urgency->setAttributes(array('id' => 'urgency', 'options' => array('' => 'Select', 'Resuscitation' => 'Resuscitation', 'Emergent' => 'Emergent', 'Urgent' => 'Urgent', 'Less Urgent' => 'Less Urgent', 'Non Urgent' => 'Non Urgent'), 'class' => 'dropdown'));
			$this->add($urgency);
			
			$budget = new \Zend\Form\Element\Text('budget');
			$budget->setLabel('Budget');
			$budget->setAttributes(array('id' => 'budget', 'class' => 'inputTxt', 'maxlength' => 20, 'data-numeric' => 'yes'));
			$this->add($budget);
			
			$rating = new \Zend\Form\Element\Select('rating');
			$rating->setLabel('Rating');
			$rating->setAttributes(array('id' => 'rating', 'options' => array('' => 'Select', 'Hot' => 'Hot', 'Warm' => 'Warm', 'Cold' => 'Cold'), 'class' => 'dropdown'));
			$this->add($rating);
			
			$probability = new \Zend\Form\Element\Select('probability');
			$probability->setLabel('Probability');
			$probability->setAttributes(array('id' => 'probability', 'options' => array('' => 'Select', 10 => 10, 20 => 20, 30 => 30, 40 => 40, 50 => 50, 60 => 60, 70 => 70, 80 => 80, 90 => 90, 100 => 100), 'class' => 'dropdown'));
			$this->add($probability);
						
			$cancel = new \Zend\Form\Element\Button('cancel');
			$cancel->setLabel('Cancel');
			$cancel->setValue('Cancel');
			$cancel->setAttributes(array('id' => 'cancel', 'class' => 'cmnBtn cancelBtn', "onclick" => "cancelButtonProperty('frm_opportunity', 'leadsList');"));
			$this->add($cancel);
			
			$save = new \Zend\Form\Element\Button('save');
			$save->setLabel('Save');
			$save->setValue('Save');
			$save->setAttributes(array('id' => 'saveOpp', 'class' => 'cmnBtn', 'onclick' => 'saveOpportunity($(\'#frm_opportunity\'));'));
			$this->add($save);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}