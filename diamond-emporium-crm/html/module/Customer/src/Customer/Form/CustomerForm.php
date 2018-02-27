<?php

/**
 * This holds all the zend elements related to the Login form
 * Mandatory field(s) in this page are Email Address and Password
 * @author ranjan
 *
 */
 
namespace Customer\Form;

use Zend\Form\Form;
 
class CustomerForm extends Form {
	
	private $objServiceManager;
	private $objStatesTable;
	private $objEthnicityTable;
	private $objProfessionsTable;
	private $objRingFingerTable;
	private $objRingSizeTable;
	
	public function __construct($objServiceManager) {
		try{
			parent::__construct ( null );
			
			$config = $objServiceManager->get('Config');
			
			$this->objServiceManager = $objServiceManager;
			$this->objStatesTable = $objServiceManager->get('Customer\Model\StatesTable');
			$this->objEthnicityTable = $objServiceManager->get('Customer\Model\EthnicityTable');
			$this->objProfessionsTable = $objServiceManager->get('Customer\Model\ProfessionsTable');
			$this->objRingFingerTable = $objServiceManager->get('Customer\Model\RingFingerTable');
			$this->objRingSizeTable = $objServiceManager->get('Customer\Model\RingSizeTable');
			
			$this->setAttributes(array('name' => 'frm_customer', 'action' => '', 'method' => 'post'));
			
			$id = new \Zend\Form\Element\Hidden('id');
			$id->setAttribute('id', 'customer_id');
			$this->add($id);
			
			$title = new \Zend\Form\Element\Select('title');
			$title->setLabel('Title');
			$title->setAttributes(array('id' => 'title', 'options' => array_merge(array('' => 'Select'), $config['titles']), 'class' => 'dropdown width100p editViewField'));
			$this->add($title);
			
			$gender = new \Zend\Form\Element\Select('gender');
			$gender->setLabel('Gender');
			$gender->setAttributes(array('id' => 'gender', 'options' => array_merge(array('' => 'Select'), $config['genders']), 'class' => 'dropdown width100p editViewField'));
			$this->add($gender);
			
			$engagement_ring_size_left = new \Zend\Form\Element\Select('engagement_ring_size_left');
			$engagement_ring_size_left->setLabel('Engagement Ring');
			$engagement_ring_size_left->setAttributes(array('id' => 'engagement_ring_size_left', 'options' => $this->objRingSizeTable->fetchSelectOptions('left'), 'class' => 'dropdown width100p editViewField'));
			$this->add($engagement_ring_size_left);
			
			$engagement_ring_size_right = new \Zend\Form\Element\Select('engagement_ring_size_right');
			$engagement_ring_size_right->setAttributes(array('id' => 'engagement_ring_size_right', 'options' => $this->objRingSizeTable->fetchSelectOptions('right'), 'class' => 'dropdown width100p editViewField'));
			$this->add($engagement_ring_size_right);
			
			$dress_ring_finger = new \Zend\Form\Element\Select('dress_ring_finger');
			$dress_ring_finger->setLabel('Dress Ring');
			$dress_ring_finger->setAttributes(array('id' => 'dress_ring_finger', 'options' => $this->objRingFingerTable->fetchSelectOptions(), 'class' => 'dropdown width100p editViewField'));
			$this->add($dress_ring_finger);
			
			$dress_ring_size = new \Zend\Form\Element\Select('dress_ring_size');
			$dress_ring_size->setAttributes(array('id' => 'dress_ring_size', 'options' => $this->objRingSizeTable->fetchSelectOptions(), 'class' => 'dropdown width100p editViewField'));
			$this->add($dress_ring_size);
			
			$wedding_anniversary_date = new \Zend\Form\Element\Text('wedding_anniversary_date');
			$wedding_anniversary_date->setLabel('Wedding Anniversary');
			$wedding_anniversary_date->setAttributes(array('id' => 'wedding_anniversary_date', 'class' => 'datepickerInput editViewField', 'readonly' => 'readonly', 'placeholder' => 'DD/MM/YYYY'));
			$this->add($wedding_anniversary_date);
			
			$engagement_anniversary_date = new \Zend\Form\Element\Text('engagement_anniversary_date');
			$engagement_anniversary_date->setLabel('Engagement Anniversary');
			$engagement_anniversary_date->setAttributes(array('id' => 'engagement_anniversary_date', 'class' => 'datepickerInput editViewField', 'readonly' => 'readonly', 'placeholder' => 'DD/MM/YYYY'));
			$this->add($engagement_anniversary_date);
			
			$date_of_birth = new \Zend\Form\Element\Text('date_of_birth');
			$date_of_birth->setLabel('Date Of Birth');
			$date_of_birth->setAttributes(array('id' => 'date_of_birth', 'class' => 'datepickerInput editViewField', 'readonly' => 'readonly', 'placeholder' => 'DD/MM/YYYY'));
			$this->add($date_of_birth);
			
			$profession = new \Zend\Form\Element\Select('profession');
			$profession->setLabel('Profession');
			$profession->setAttributes(array('id' => 'profession', 'options' => $this->objProfessionsTable->fetchSelectOptions(), 'class' => 'dropdown width100p editViewField'));
			$this->add($profession);
			
			$ethnicity = new \Zend\Form\Element\Select('ethnicity');
			$ethnicity->setLabel('Ethnicity');
			$ethnicity->setAttributes(array('id' => 'ethnicity', 'options' => $this->objEthnicityTable->fetchSelectOptions(), 'class' => 'dropdown width100p editViewField'));
			$this->add($ethnicity);
			
			$address1 = new \Zend\Form\Element\Text('address1');
			$address1->setLabel('Address1');
			$address1->setAttributes(array('id' => 'address1', 'class' => 'inputTxt editViewField'));
			$this->add($address1);
			
			$address2 = new \Zend\Form\Element\Text('address2');
			$address2->setLabel('Address2');
			$address2->setAttributes(array('id' => 'address2', 'class' => 'inputTxt editViewField'));
			$this->add($address2);
			
			$state = new \Zend\Form\Element\Select('state_id');
			$state->setLabel('State');
			$state->setAttributes(array('id' => 'state_id', 'options' => $this->objStatesTable->fetchSelectOptions(), 'class' => 'dropdown width100p editViewField'));
			$this->add($state);
			
			$postcode = new \Zend\Form\Element\Text('postcode');
			$postcode->setLabel('Postcode');
			$postcode->setAttributes(array('id' => 'postcode', 'class' => 'inputTxt editViewField', 'maxlength' => 6, 'data-numeric' => 'yes'));
			$this->add($postcode);
			
			$country = new \Zend\Form\Element\Text('country_id');
			$country->setLabel('Country');
			$country->setAttributes(array('id' => 'country_id', 'class' => 'inputTxt editViewField', 'value' => 'Australia', 'readonly' => 'readonly'));
			$this->add($country);
			
			$facebook = new \Zend\Form\Element\Text('facebook');
			$facebook->setLabel('Facebook');
			$facebook->setAttributes(array('id' => 'facebook', 'class' => 'inputTxt editViewField', 'placeholder' => 'Facebook Name'));
			$this->add($facebook);
			
			$twitter = new \Zend\Form\Element\Text('instagram');
			$twitter->setLabel('Instagram');
			$twitter->setAttributes(array('id' => 'instagram', 'class' => 'inputTxt editViewField', 'placeholder' => 'Instagram Name'));
			$this->add($twitter);
			
			$facebook = new \Zend\Form\Element\Text('twitter');
			$facebook->setLabel('Twitter');
			$facebook->setAttributes(array('id' => 'twitter', 'class' => 'inputTxt editViewField', 'placeholder' => 'Twitter Username'));
			$this->add($facebook);
			
			$linkedin = new \Zend\Form\Element\Text('linkedin');
			$linkedin->setLabel('LinkedIn');
			$linkedin->setAttributes(array('id' => 'linkedin', 'class' => 'inputTxt editViewField', 'placeholder' => 'URL Link'));
			$this->add($linkedin);			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}