<?php

/**
 * This holds all the zend elements related to the Login form
 * Mandatory field(s) in this page are Email Address and Password
 * @author ranjan
 *
 */
 
namespace Inventory\Form;

use Zend\Form\Form;
 
class WeddingringInventoryForm extends Form {

	public function __construct($objServiceManager) {
		try{
			parent::__construct ( null );
			
			$objLookupTable = $objServiceManager->get('Inventory\Model\LookupTable');		
						
			$this->setAttributes(array('name' => 'frm_weddingring', 'id' => 'frm_weddingring', 'action' => '/uploadfile', 'enctype' => 'multipart/form-data', 'method' => 'post'));
			
			$weddingring_image = new \Zend\Form\Element\Hidden('image');
			$weddingring_image->setAttributes(array('id' => 'image'));
			$this->add($weddingring_image);
			
			$weddingring_invoice = new \Zend\Form\Element\Hidden('invoice');
			$weddingring_invoice->setAttributes(array('id' => 'invoice'));
			$this->add($weddingring_invoice);
			
			$supplier_id = new \Zend\Form\Element\Hidden('supplier_id');
			$supplier_id->setAttributes(array('id' => 'supplier_id'));
			$this->add($supplier_id);
			
			$weddingring_type = new \Zend\Form\Element\Select('ring_type');
			$weddingring_type->setLabel('Wedding Ring Type');
			$weddingring_type->setAttributes(array('id' => 'ring_type', 'options' => $objLookupTable->fetchRingTypeOptions(), 'class' => 'dropdown'));
			$this->add($weddingring_type);
			
			$cad_code = new \Zend\Form\Element\Text('cad_code');
			$cad_code->setLabel('CAD Code');
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
			
			$band_width = new \Zend\Form\Element\Text('band_width');
			$band_width->setLabel('Band Width');
			$band_width->setAttributes(array('id' => 'band_width', 'class' => 'inputTxt', 'maxlength' => 64, 'data-numeric' => 'yes'));
			$this->add($band_width);
			
			$band_thickness = new \Zend\Form\Element\Text('band_thickness');
			$band_thickness->setLabel('Band Thickness');
			$band_thickness->setAttributes(array('id' => 'band_thickness', 'class' => 'inputTxt', 'maxlength' => 64, 'data-numeric' => 'yes'));
			$this->add($band_thickness);
			
			$finish = new \Zend\Form\Element\Select('finish');
			$finish->setLabel('Finish');
			$finish->setAttributes(array('id' => 'finish', 'options' => $objLookupTable->fetchFinishOptions(), 'class' => 'dropdown'));
			$this->add($finish);
			
			$fit_options = new \Zend\Form\Element\Select('fit_options');
			$fit_options->setLabel('Fit Options');
			$fit_options->setAttributes(array('id' => 'fit_options', 'options' => $objLookupTable->fetchFitOptions(), 'class' => 'dropdown'));
			$this->add($fit_options);
			
			$metal_thickness = new \Zend\Form\Element\Text('metal_thickness');
			$metal_thickness->setLabel('Metal Thickness');
			$metal_thickness->setAttributes(array('id' => 'metal_thickness', 'class' => 'inputTxt', 'data-numeric' => 'yes'));
			$this->add($metal_thickness);
			
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
			$cancel->setAttributes(array('id' => 'weddingring_cancel', 'class' => 'cmnBtn cancelBtn', "onclick" => "cancelButtonProperty('frm_weddingring', 'addItem');"));
			$this->add($cancel);
			
			$save = new \Zend\Form\Element\Button('save');
			$save->setLabel('Add Item');
			$save->setAttributes(array('id' => 'weddingring_save', 'class' => 'cmnBtn blueBtn', 'onclick' => 'saveWeddingring($(\'#frm_weddingring\'));'));
			$this->add($save);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}