<?php

/**
 * This holds all the zend elements related to the Login form
 * Mandatory field(s) in this page are Email Address and Password
 * @author ranjan
 *
 */
 
namespace Inventory\Form;

use Zend\Form\Form;
 
class ConsignInventoryForm extends Form {

	public function __construct($objServiceManager) {
		try{
			parent::__construct ( null );
			
			$objLookupTable = $objServiceManager->get('Inventory\Model\LookupTable');
						
			$this->setAttributes(array('name' => 'frm_consign', 'id' => 'frm_consign', 'action' => '', 'method' => 'post'));
			
			$id = new \Zend\Form\Element\Hidden('id');
			$id->setAttributes(array('id' => 'consign_id'));
			$this->add($id);
			
			$item_id = new \Zend\Form\Element\Hidden('item_id');
			$item_id->setAttributes(array('id' => 'item_id'));
			$this->add($item_id);
			
			$jewel_type = new \Zend\Form\Element\Hidden('jewel_type');
			$jewel_type->setAttributes(array('id' => 'jewel_type'));
			$this->add($jewel_type);
			
			$edit_mode = new \Zend\Form\Element\Hidden('edit_mode');
			$edit_mode->setAttributes(array('id' => 'edit_mode'));
			$this->add($edit_mode);
			
			$inventory_status_id = new \Zend\Form\Element\Select('inventory_status_id');
			$inventory_status_id->setLabel('Inventory Status');
			$inventory_status_id->setAttributes(array('id' => 'inventory_status_id', 'options' => $objLookupTable->fetchInventoryStatusOptions(), 'class' => 'width100p dropdown'));
			$this->add($inventory_status_id);
			
			$inventory_status_reason = new \Zend\Form\Element\Select('inventory_status_reason');
			$inventory_status_reason->setLabel('Status Reason');
			//$objLookupTable->fetchInventoryStatusReasonOptions()
			$inventory_status_reason->setAttributes(array('id' => 'inventory_status_reason', 'options' => array(0 => 'Select'), 'class' => 'width100p dropdown'));
			$this->add($inventory_status_reason);	
			
			$inventory_type = new \Zend\Form\Element\Select('inventory_type');
			$inventory_type->setLabel('Inventory Type');
			$inventory_type->setAttributes(array('id' => 'inventory_type', 'options' => $objLookupTable->fetchInventoryTypeOptions(), 'class' => 'width100p dropdown'));
			$this->add($inventory_type);
			
			$inventory_tracking_status = new \Zend\Form\Element\Select('inventory_tracking_status');
			$inventory_tracking_status->setLabel('Tracking Status');
			$inventory_tracking_status->setAttributes(array('id' => 'inventory_tracking_status', 'options' => $objLookupTable->fetchInventoryTrackingStatusOptions(), 'class' => 'width100p dropdown'));
			$this->add($inventory_tracking_status);
						
			$inventory_tracking_reason = new \Zend\Form\Element\Select('inventory_tracking_reason');
			$inventory_tracking_reason->setLabel('Tracking Reason');
			// $objLookupTable->fetchInventoryTrackingReasonOptions()
			$inventory_tracking_reason->setAttributes(array('id' => 'inventory_tracking_reason', 'options' => array(0 => 'Select'), 'class' => 'width100p dropdown'));
			$this->add($inventory_tracking_reason);
			
			$owner_id = new \Zend\Form\Element\Select('owner_id');
			$owner_id->setLabel('Owner Name');
			$owner_id->setAttributes(array('id' => 'owner_id', 'options' => $objLookupTable->fetchConsignOwnerOptions(), 'class' => 'width100p dropdown'));
			$this->add($owner_id);
			
			$password = new \Zend\Form\Element\Password('password');
			$password->setAttributes(array('id' => 'password', 'class' => 'inputTxt'));
			$password->setLabel('User Password');
			$this->add($password);
			
			$reserve_time = new \Zend\Form\Element\Text('reserve_time');
			$reserve_time->setLabel('Reserve Time');
			$reserve_time->setAttributes(array('id' => 'reserve_time', 'class' => 'dateTimepickerInput'));
			$this->add($reserve_time);
			
			$reserve_notes = new \Zend\Form\Element\Text('reserve_notes');
			$reserve_notes->setLabel('Reserve Notes');
			$reserve_notes->setAttributes(array('id' => 'reserve_notes', 'class' => 'inputTxt'));
			$this->add($reserve_notes);
			
			$tracking_id = new \Zend\Form\Element\Text('tracking_id');
			$tracking_id->setLabel('Tracking ID');
			$tracking_id->setAttributes(array('id' => 'tracking_id', 'class' => 'inputTxt'));
			$this->add($tracking_id);
						
			$cancel = new \Zend\Form\Element\Button('consign_cancel');
			$cancel->setLabel('Cancel');
			$cancel->setValue('Cancel');
			$cancel->setAttributes(array('id' => 'consign_cancel', 'class' => 'cmnBtn cancelBtn', "onclick" => "cancelButtonProperty('frm_consign', 'consignItem');"));
			$this->add($cancel);
			
			$save = new \Zend\Form\Element\Button('consign_save');
			$save->setLabel('Consign');
			$save->setAttributes(array('id' => 'consign_save', 'class' => 'cmnBtn blueBtn', 'onclick' => 'saveConsign($(\'#frm_consign\'), $(\'#jewel_type\').val(), $(\'#edit_mode\').val());'));
			$this->add($save);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}