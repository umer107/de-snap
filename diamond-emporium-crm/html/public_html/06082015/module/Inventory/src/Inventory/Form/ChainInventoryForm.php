<?php

/**
 * This holds all the zend elements related to the Login form
 * Mandatory field(s) in this page are Email Address and Password
 * @author ranjan
 *
 */
 
namespace Inventory\Form;

use Zend\Form\Form;
 
class ChainInventoryForm extends Form {

	public function __construct($objServiceManager) {
		try{
			parent::__construct ( null );
			
			$objLookupTable = $objServiceManager->get('Inventory\Model\LookupTable');		
						
			$this->setAttributes(array('name' => 'frm_chain', 'id' => 'frm_chain', 'action' => '/uploadfile', 'enctype' => 'multipart/form-data', 'method' => 'post'));
			
			$chain_image = new \Zend\Form\Element\Hidden('image');
			$chain_image->setAttributes(array('id' => 'image'));
			$this->add($chain_image);
			
			$chain_invoice = new \Zend\Form\Element\Hidden('invoice');
			$chain_invoice->setAttributes(array('id' => 'invoice'));
			$this->add($chain_invoice);
			
			$supplier_id = new \Zend\Form\Element\Hidden('supplier_id');
			$supplier_id->setAttributes(array('id' => 'supplier_id'));
			$this->add($supplier_id);
			
			$chain_style = new \Zend\Form\Element\Select('style');
			$chain_style->setLabel('Style');
			$chain_style->setAttributes(array('id' => 'style', 'options' => $objLookupTable->fetchChainStyleOptions(), 'class' => 'dropdown'));
			$this->add($chain_style);
			
			$length = new \Zend\Form\Element\Text('length');
			$length->setLabel('Length');
			$length->setAttributes(array('id' => 'length', 'class' => 'inputTxt', 'maxlength' => 64, 'data-numeric' => 'yes'));
			$this->add($length);
			
			$thickness = new \Zend\Form\Element\Text('thickness');
			$thickness->setLabel('Thickness');
			$thickness->setAttributes(array('id' => 'thickness', 'class' => 'inputTxt', 'maxlength' => 64, 'data-numeric' => 'yes'));
			$this->add($thickness);
			
			$metal_type = new \Zend\Form\Element\Select('metal_type');
			$metal_type->setLabel('Metal Type');
			$metal_type->setAttributes(array('id' => 'metal_type', 'options' => $objLookupTable->fetchMetalTypeOptions(), 'class' => 'dropdown'));
			$this->add($metal_type);
			
			$metal_weight = new \Zend\Form\Element\Text('metal_weight');
			$metal_weight->setLabel('Metal Weight');
			$metal_weight->setAttributes(array('id' => 'metal_weight', 'class' => 'inputTxt', 'maxlength' => 64, 'data-numeric' => 'yes'));
			$this->add($metal_weight);
			
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
			$cancel->setAttributes(array('id' => 'chain_cancel', 'class' => 'cmnBtn cancelBtn', "onclick" => "cancelButtonProperty('frm_chain', 'addItem');"));
			$this->add($cancel);
			
			$save = new \Zend\Form\Element\Button('save');
			$save->setLabel('Add Item');
			$save->setAttributes(array('id' => 'chain_save', 'class' => 'cmnBtn blueBtn', 'onclick' => 'saveChain($(\'#frm_chain\'));'));
			$this->add($save);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}