<?php

/**
 * This holds all the zend elements related to the Login form
 * Mandatory field(s) in this page are Email Address and Password
 * @author ranjan
 *
 */
 
namespace Inventory\Form;

use Zend\Form\Form;
 
class MiscellaneousInventoryForm extends Form {

	public function __construct($objServiceManager) {
		try{
			parent::__construct ( null );
			
			$objLookupTable = $objServiceManager->get('Inventory\Model\LookupTable');		
						
			$this->setAttributes(array('name' => 'frm_miscellaneous', 'id' => 'frm_miscellaneous', 'action' => '/uploadfile', 'enctype' => 'multipart/form-data', 'method' => 'post'));
			
			$miscellaneous_invoice = new \Zend\Form\Element\Hidden('invoice');
			$miscellaneous_invoice->setAttributes(array('id' => 'invoice'));
			$this->add($miscellaneous_invoice);
			
			$supplier_id = new \Zend\Form\Element\Hidden('supplier_id');
			$supplier_id->setAttributes(array('id' => 'supplier_id'));
			$this->add($supplier_id);
			
			$miscellaneous_title = new \Zend\Form\Element\Text('title');
			$miscellaneous_title->setLabel('Title');
			$miscellaneous_title->setAttributes(array('id' => 'title', 'class' => 'inputTxt', 'maxlength' => 255));
			$this->add($miscellaneous_title);
			
			$cad_code = new \Zend\Form\Element\Text('cad_code');
			$cad_code->setLabel('CAD Stock Code');
			$cad_code->setAttributes(array('id' => 'cad_code', 'class' => 'inputTxt', 'maxlength' => 64));
			$this->add($cad_code);

			$description = new \Zend\Form\Element\Textarea('description');
			$description->setLabel('Description');
			$description->setAttributes(array('id' => 'description', 'rows' => 6, 'style' => 'height:169px'));
			$this->add($description);
			
			$price = new \Zend\Form\Element\Text('price');
			$price->setLabel('Price');
			$price->setAttributes(array('id' => 'price', 'class' => 'inputTxt', 'data-numeric' => 'yes'));
			$this->add($price);
			
			$supplier_name = new \Zend\Form\Element\Text('supplier_name');
			$supplier_name->setLabel('Supplier Name');
			$supplier_name->setAttributes(array('id' => 'supplier_name', 'class' => 'inputTxt width60p', 'readonly' => 'readonly'));
			$this->add($supplier_name);
						
			$cancel = new \Zend\Form\Element\Button('cancel');
			$cancel->setLabel('Cancel');
			$cancel->setValue('Cancel');
			$cancel->setAttributes(array('id' => 'miscellaneous_cancel', 'class' => 'cmnBtn cancelBtn', "onclick" => "cancelButtonProperty('frm_miscellaneous', 'addItem');"));
			$this->add($cancel);
			
			$save = new \Zend\Form\Element\Button('save');
			$save->setLabel('Add Item');
			$save->setAttributes(array('id' => 'miscellaneous_save', 'class' => 'cmnBtn blueBtn', 'onclick' => 'saveMiscellaneous($(\'#frm_miscellaneous\'));'));
			$this->add($save);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}