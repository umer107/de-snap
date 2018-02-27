<?php

/**
 * This holds all the zend elements related to the Login form
 * Mandatory field(s) in this page are Email Address and Password
 * @author ranjan
 *
 */
 
namespace Inventory\Form;

use Zend\Form\Form;
 
class EarringInventoryForm extends Form {

	public function __construct($objServiceManager) {
		try{
			parent::__construct ( null );
			
			$objLookupTable = $objServiceManager->get('Inventory\Model\LookupTable');		
						
			$this->setAttributes(array('name' => 'frm_earring', 'id' => 'frm_earring', 'action' => '/uploadfile', 'enctype' => 'multipart/form-data', 'method' => 'post'));
			
			$earring_image = new \Zend\Form\Element\Hidden('image');
			$earring_image->setAttributes(array('id' => 'image'));
			$this->add($earring_image);
			
			$earring_invoice = new \Zend\Form\Element\Hidden('invoice');
			$earring_invoice->setAttributes(array('id' => 'invoice'));
			$this->add($earring_invoice);
			
			$supplier_id = new \Zend\Form\Element\Hidden('supplier_id');
			$supplier_id->setAttributes(array('id' => 'supplier_id'));
			$this->add($supplier_id);
			
			$earring_style = new \Zend\Form\Element\Select('ring_style');
			$earring_style->setLabel('Style');
			$earring_style->setAttributes(array('id' => 'ring_style', 'options' => $objLookupTable->fetchEarringStyleOptions(), 'class' => 'dropdown'));
			$this->add($earring_style);
			
			$cad_code = new \Zend\Form\Element\Text('cad_code');
			$cad_code->setLabel('CAD Stock Code');
			$cad_code->setAttributes(array('id' => 'cad_code', 'class' => 'inputTxt', 'maxlength' => 64));
			$this->add($cad_code);
			
			$metal_type = new \Zend\Form\Element\Select('metal_type');
			$metal_type->setLabel('Metal Type');
			$metal_type->setAttributes(array('id' => 'metal_type', 'options' => $objLookupTable->fetchMetalTypeOptions(), 'class' => 'dropdown'));
			$this->add($metal_type);
			
			$profile = new \Zend\Form\Element\Select('profile');
			$profile->setLabel('Profile');
			$profile->setAttributes(array('id' => 'profile', 'options' => $objLookupTable->fetchProfileOptions(), 'class' => 'dropdown'));
			$this->add($profile);

			$metal_weight = new \Zend\Form\Element\Text('metal_weight');
			$metal_weight->setLabel('Metal Weight');
			$metal_weight->setAttributes(array('id' => 'metal_weight', 'class' => 'inputTxt', 'maxlength' => 64));
			$this->add($metal_weight);
			
			$band_width = new \Zend\Form\Element\Text('band_width');
			$band_width->setLabel('Band Width');
			$band_width->setAttributes(array('id' => 'band_width', 'class' => 'inputTxt', 'maxlength' => 64, 'data-numeric' => 'yes'));
			$this->add($band_width);
			
			$band_thickness = new \Zend\Form\Element\Text('band_thickness');
			$band_thickness->setLabel('Band Thickness');
			$band_thickness->setAttributes(array('id' => 'band_thickness', 'class' => 'inputTxt', 'maxlength' => 64, 'data-numeric' => 'yes'));
			$this->add($band_thickness);
			
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
			$cancel->setAttributes(array('id' => 'earring_cancel', 'class' => 'cmnBtn cancelBtn', "onclick" => "cancelButtonProperty('frm_earring', 'addItem');"));
			$this->add($cancel);
			
			$save = new \Zend\Form\Element\Button('save');
			$save->setLabel('Add Item');
			$save->setAttributes(array('id' => 'earring_save', 'class' => 'cmnBtn blueBtn', 'onclick' => 'saveEarring($(\'#frm_earring\'));'));
			$this->add($save);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}