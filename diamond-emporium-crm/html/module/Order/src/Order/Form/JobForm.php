<?php

/**
 * Form to create new job
 */
 
namespace Order\Form;

use Zend\Form\Form;
 
class JobForm extends Form {

	public function __construct($sm) {
		try{
			parent::__construct ( null );
			
			$config = $sm->get('config');
			
			$objLookupTable = $sm->get('Inventory\Model\LookupTable');
						
			$this->setAttributes(array('name' => 'frm_job_packet', 'id' => 'frm_job_packet', 'action' => '', 'method' => 'post'));
			
			$id = new \Zend\Form\Element\Hidden('id');
			$this->add($id);
			
			$order_id = new \Zend\Form\Element\Hidden('order_id');
			$order_id->setAttributes(array('id' => 'order_id'));
			$this->add($order_id);
			
			$owner_id = new \Zend\Form\Element\Select('owner_id');
			$owner_id->setLabel('Record Owner');
			$owner_id->setAttributes(array('id' => 'owner_id', 'options' => $objLookupTable->fetchConsignOwnerOptions(), 'class' => 'width100p dropdown'));
			$this->add($owner_id);
			
			$milestones = new \Zend\Form\Element\MultiCheckbox('milestones');
			$milestones->setLabel('Select Milestones Required');
			$milestones->setValueOptions($config['milestones']);
			$this->add($milestones);
			
			$exp_delivery_date = new \Zend\Form\Element\Text('exp_delivery_date');
			$exp_delivery_date->setLabel('Expected Delivery Date');
			$exp_delivery_date->setAttributes(array('id' => 'exp_delivery_date', 'class' => 'inputTxt', 'readonly' => 'readonly', 'placeholder' => 'DD/MM/YYYY'));
			$this->add($exp_delivery_date);
						
			$cancel = new \Zend\Form\Element\Button('cancel');
			$cancel->setLabel('Cancel');
			$cancel->setValue('Cancel');
			$cancel->setAttributes(array('id' => 'job_cancel', 'class' => 'cmnBtn cancelBtn', "onclick" => "cancelButtonProperty('frm_job_packet', 'createJob');"));
			$this->add($cancel);
			
			$save = new \Zend\Form\Element\Button('job_save');
			$save->setLabel('Create');
			$save->setAttributes(array('id' => 'job_save', 'class' => 'cmnBtn lookupBtn', 'onclick' => 'createJobPacket($(\'#frm_job_packet\'));'));
			$this->add($save);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}