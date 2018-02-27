<?php

/**
 * This holds all the zend elements related to the Login form
 * Mandatory field(s) in this page are Email Address and Password
 * @author ranjan
 *
 */
 
namespace Inventory\Form;

use Zend\Form\Form;
 
class PendantInventoryForm extends Form {

	public function __construct($objServiceManager) {
		try{
			parent::__construct ( null );
			
			$objLookupTable = $objServiceManager->get('Inventory\Model\LookupTable');		
						
			$this->setAttributes(array('name' => 'frm_pendant', 'id' => 'frm_pendant', 'action' => '/uploadfile', 'enctype' => 'multipart/form-data', 'method' => 'post'));
			
			$pendant_image = new \Zend\Form\Element\Hidden('image');
			$pendant_image->setAttributes(array('id' => 'image'));
			$this->add($pendant_image);
			
			$pendant_invoice = new \Zend\Form\Element\Hidden('invoice');
			$pendant_invoice->setAttributes(array('id' => 'invoice'));
			$this->add($pendant_invoice);
			
			$supplier_id = new \Zend\Form\Element\Hidden('supplier_id');
			$supplier_id->setAttributes(array('id' => 'supplier_id'));
			$this->add($supplier_id);
			
			$pendant_style = new \Zend\Form\Element\Select('ring_style');
			$pendant_style->setLabel('Style');
			$pendant_style->setAttributes(array('id' => 'ring_style', 'options' => $objLookupTable->fetchPendantStyleOptions(), 'class' => 'dropdown'));
			$this->add($pendant_style);
			
			$metal_type = new \Zend\Form\Element\Select('metal_type');
			$metal_type->setLabel('Metal Type');
			$metal_type->setAttributes(array('id' => 'metal_type', 'options' => $objLookupTable->fetchMetalTypeOptions(), 'class' => 'dropdown'));
			$this->add($metal_type);
			
			$profile = new \Zend\Form\Element\Select('profile');
			$profile->setLabel('Profile');
			$profile->setAttributes(array('id' => 'profile', 'options' => $objLookupTable->fetchProfileOptions(), 'class' => 'dropdown'));
			$this->add($profile);

			$other_ring_style = new \Zend\Form\Element\Text('other_ring_style');
			$other_ring_style->setAttributes(array('id' => 'other_ring_style', 'class' => 'inputTxt', 'maxlength' => 64));
			$this->add($other_ring_style);

			$metal_weight = new \Zend\Form\Element\Text('metal_weight');
			$metal_weight->setLabel('Metal Weight');
			$metal_weight->setAttributes(array('id' => 'metal_weight', 'class' => 'inputTxt pureNumaric', 'maxlength' => 64));
			$this->add($metal_weight);
			
			$head_settings = new \Zend\Form\Element\Select('head_settings');
			$head_settings->setLabel('Head Settings');
			$head_settings->setAttributes(array('id' => 'head_settings', 'options' => $objLookupTable->fetchHeadSettingsOptions(), 'class' => 'dropdown'));
			$this->add($head_settings);
			
			$setting_style = new \Zend\Form\Element\Text('setting_style');
			$setting_style->setLabel('Setting Style');
			$setting_style->setAttributes(array('id' => 'setting_style', 'class' => 'inputTxt', 'data-numeric' => 'yes'));
			$this->add($setting_style);
			
			$halo_width = new \Zend\Form\Element\Text('halo_width');
			$halo_width->setLabel('Halo Width');
			$halo_width->setAttributes(array('id' => 'halo_width', 'class' => 'inputTxt', 'maxlength' => 64, 'data-numeric' => 'yes'));
			$this->add($halo_width);
			
			$halo_thickness = new \Zend\Form\Element\Text('halo_thickness');
			$halo_thickness->setLabel('Halo Thickness');
			$halo_thickness->setAttributes(array('id' => 'halo_thickness', 'class' => 'inputTxt', 'maxlength' => 64, 'data-numeric' => 'yes'));
			$this->add($halo_thickness);
			
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
			$cancel->setAttributes(array('id' => 'pendant_cancel', 'class' => 'cmnBtn cancelBtn', "onclick" => "cancelButtonProperty('frm_pendant', 'addItem');"));
			$this->add($cancel);
			
			$save = new \Zend\Form\Element\Button('save');
			$save->setLabel('Add Item');
			$save->setAttributes(array('id' => 'pendant_save', 'class' => 'cmnBtn blueBtn', 'onclick' => 'savePendant($(\'#frm_pendant\'));'));
			$this->add($save);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}