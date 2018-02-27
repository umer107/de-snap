<?php

/**
 * This holds all the zend elements related to the Login form
 * Mandatory field(s) in this page are Email Address and Password
 * @author ranjan
 *
 */
 
namespace Suppliers\Form;

use Zend\Form\Form;
 
class SuppliersForm extends Form {
	
	public function __construct($objServiceManager) {
		try{
			parent::__construct ( null );
			$objSuppliersTable = $objServiceManager->get('Suppliers\Model\SuppliersTable');
			$supplierTypeLookup = $objSuppliersTable->getSupplierTypesLookup();
			$servicesTypeLookup = $objSuppliersTable->getServicesTypesLookup();
			$this->setAttributes(array('name' => 'frm_supplier', 'action' => '', 'method' => 'post'));
						
			$company_name = new \Zend\Form\Element\Text('company_name');
			$company_name->setLabel('Company Name');
			$company_name->setAttributes(array('id' => 'company_name', 'class' => 'inputTxt textboxGlobalMaxLength'));
			$this->add($company_name);
			
			$first_name = new \Zend\Form\Element\Text('first_name');
			$first_name->setLabel('First Name');
			$first_name->setAttributes(array('id' => 'first_name', 'class' => 'inputTxt textboxGlobalMaxLength'));
			$this->add($first_name);
			
			$last_name = new \Zend\Form\Element\Text('last_name');
			$last_name->setLabel('Last Name');
			$last_name->setAttributes(array('id' => 'last_name', 'class' => 'inputTxt textboxGlobalMaxLength'));
			$this->add($last_name);
			
			$address = new \Zend\Form\Element\Textarea('address');
			$address->setLabel('Address');
			$address->setAttributes(array('id' => 'address', 'class' => 'textareaGlobalMaxLength'));
			$this->add($address);
			
			$email = new \Zend\Form\Element\Text('email');
			$email->setLabel('Email');
			$email->setAttributes(array('id' => 'email', 'class' => 'inputTxt', 'maxlength' => 255));
			$this->add($email);
			
			$mobile = new \Zend\Form\Element\Text('mobile');
			$mobile->setLabel('Mobile');
			$mobile->setAttributes(array('id' => 'mobile', 'class' => 'inputTxt', 'maxlength' => 10));
			$this->add($mobile);
			
			$phone = new \Zend\Form\Element\Text('phone');
			$phone->setLabel('Phone');
			$phone->setAttributes(array('id' => 'phone', 'class' => 'inputTxt pureNumaric', 'maxlength' => 20));
			$this->add($phone);
			
			$website = new \Zend\Form\Element\Text('website');
			$website->setLabel('Website');
			$website->setAttributes(array('id' => 'website', 'class' => 'inputTxt isUrlValid'));
			$this->add($website);
			
			$supplier_type = new \Zend\Form\Element\Select('supplier_type');
			$supplier_type->setLabel('Supplier Type');
			$supplier_type->setAttributes(array('id' => 'supplier_type', 'options' => $supplierTypeLookup, 'class' => 'dropdown'));
			$this->add($supplier_type);
			
			$rap_id = new \Zend\Form\Element\Text('rap_id');
			$rap_id->setLabel('Rap. ID (Optional)');
			$rap_id->setAttributes(array('id' => 'rap_id', 'class' => 'inputTxt textboxGlobalMaxLength'));
			$this->add($rap_id);
			
			$service = new \Zend\Form\Element\MultiCheckbox('service');
			$service->setLabel('Services Offered');
			$service->setAttributes(array('id' => 'service', 'options' => $servicesTypeLookup, 'class' => ''));
			$this->add($service);
						
			$cancel = new \Zend\Form\Element\Button('cancel');
			$cancel->setLabel('Cancel');
			$cancel->setValue('Cancel');
			$cancel->setAttributes(array('id' => 'cancel', 'class' => 'cmnBtn cancelBtn', "onclick" => "cancelButtonProperty('frm_supplier', 'leadsList');"));
			$this->add($cancel);
			
			$save = new \Zend\Form\Element\Button('save');
			$save->setLabel('Create');
			$save->setValue('Save');
			$save->setAttributes(array('id' => 'saveOpp', 'class' => 'cmnBtn', 'onclick' => 'saveSupplier($(\'#frm_supplier\'));'));
			$this->add($save);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}