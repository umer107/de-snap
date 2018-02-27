<?php

/**
 * This holds all the zend elements related to the Login form
 * Mandatory field(s) in this page are Email Address and Password
 * @author ranjan
 *
 */
 
namespace Inventory\Form;

use Zend\Form\Form;
 
class DiamondInventoryForm extends Form {

	public function __construct($objServiceManager) {
		try{
			parent::__construct ( null );
			
			$objLookupTable = $objServiceManager->get('Inventory\Model\LookupTable');		
						
			$this->setAttributes(array('name' => 'frm_diamond', 'id' => 'frm_diamond', 'action' => '/uploadfile', 'enctype' => 'multipart/form-data', 'method' => 'post'));
			
			$diamond_image = new \Zend\Form\Element\Hidden('image');
			$diamond_image->setAttributes(array('id' => 'image'));
			$this->add($diamond_image);
			
			$diamond_invoice = new \Zend\Form\Element\Hidden('invoice');
			$diamond_invoice->setAttributes(array('id' => 'invoice'));
			$this->add($diamond_invoice);
			
			$supplier_id = new \Zend\Form\Element\Hidden('supplier_id');
			$supplier_id->setAttributes(array('id' => 'supplier_id'));
			$this->add($supplier_id);
			
			$supplier_stock_code = new \Zend\Form\Element\Text('supplier_stock_code');
			$supplier_stock_code->setLabel('Stock code');
			$supplier_stock_code->setAttributes(array('id' => 'supplier_stock_code', 'class' => 'inputTxt'));
			$this->add($supplier_stock_code);
			
			$diamond_type = new \Zend\Form\Element\Select('diamond_type');
			$diamond_type->setLabel('Diamond Type');
			$diamond_type->setAttributes(array('id' => 'diamond_type', 'options' => $objLookupTable->fetchDiamonTypesOptions(), 'class' => 'dropdown'));
			/* #280 default value is 'White' */
			$diamond_type->setValue(1);
			$this->add($diamond_type);
			
			$white_type = new \Zend\Form\Element\Select('white_type');
			$white_type->setLabel('Colour');
			$white_type_options = array('0' => 'Select', 'D' => 'D', 'E' => 'E', 'F' => 'F', 'G' => 'G', 'H' => 'H', 'I' => 'I', 'J' => 'J', 'K' => 'K', 'L' => 'L', 'M' => 'M', 'N' => 'N', 'O' => 'O', 'P' => 'P', 'Q' => 'Q', 'R' => 'R', 'S' => 'S', 'T' => 'T', 'U' => 'U', 'V' => 'V', 'W' => 'W', 'X' => 'X', 'Y' => 'Y', 'Z' => 'Z');
			$white_type->setAttributes(array('id' => 'white_type', 'options' => $white_type_options, 'class' => 'dropdown'));
			$this->add($white_type);
			
			$color = new \Zend\Form\Element\Select('color');
			$color->setLabel('Colour');
			$color->setAttributes(array('id' => 'color', 'options' => $objLookupTable->fetchColorOptions(), 'class' => 'dropdown'));
			$this->add($color);
			
			$shape = new \Zend\Form\Element\Select('shape');
			$shape->setLabel('Shape');
			$shape->setAttributes(array('id' => 'shape', 'options' => $objLookupTable->fetchShapeOptions(), 'class' => 'dropdown'));
			$this->add($shape);
			
			$polish = new \Zend\Form\Element\Select('polish');
			$polish->setLabel('Polish');
			$polish->setAttributes(array('id' => 'polish', 'options' => $objLookupTable->fetchPolishOptions(), 'class' => 'dropdown'));
			$this->add($polish);
						
			$symmetry = new \Zend\Form\Element\Select('symmetry');
			$symmetry->setLabel('Symmetry');
			$symmetry->setAttributes(array('id' => 'symmetry', 'options' => $objLookupTable->fetchSymmetryOptions(), 'class' => 'dropdown'));
			$this->add($symmetry);			
			
			$lab = new \Zend\Form\Element\Select('lab');
			$lab->setLabel('Lab');
			$lab->setAttributes(array('id' => 'lab', 'options' => $objLookupTable->fetchLabOptions(), 'class' => 'dropdown'));
			$this->add($lab);
			
			$intensity = new \Zend\Form\Element\Select('intensity');
			$intensity->setLabel('Intensity');
			$intensity->setAttributes(array('id' => 'intensity', 'options' => $objLookupTable->fetchIntensityOptions(), 'class' => 'dropdown'));
			$this->add($intensity);
			
			$overtone = new \Zend\Form\Element\Select('overtone');
			$overtone->setLabel('Overtone');
			$overtone->setAttributes(array('id' => 'overtone', 'options' => $objLookupTable->fetchOvertoneOptions(), 'class' => 'dropdown'));
			$this->add($overtone);
			
			$cert_no = new \Zend\Form\Element\Text('cert_no');
			$cert_no->setLabel('Cert. No.');
			$cert_no->setAttributes(array('id' => 'cert_no', 'class' => 'inputTxt', 'maxlength' => 64));
			$this->add($cert_no);
			
			$cert_url = new \Zend\Form\Element\Text('cert_url');
			$cert_url->setLabel('Cert. Url');
			$cert_url->setAttributes(array('id' => 'cert_url', 'class' => 'inputTxt', 'maxlength' => 250));
			$this->add($cert_url);
			
			$video_url = new \Zend\Form\Element\Text('video_url');
			$video_url->setLabel('Video Url');
			$video_url->setAttributes(array('id' => 'video_url', 'class' => 'inputTxt', 'maxlength' => 250));
			$this->add($video_url);
			
			$cut = new \Zend\Form\Element\Select('cut');
			$cut->setLabel('Cut');
			$cut->setAttributes(array('id' => 'cut', 'options' => $objLookupTable->fetchCutOptions(), 'class' => 'dropdown'));
			$this->add($cut);
			
			$table = new \Zend\Form\Element\Text('carat');
			$table->setLabel('Carat');
			$table->setAttributes(array('id' => 'carat', 'class' => 'inputTxt', 'maxlength' => 6, 'data-numeric' => 'yes'));
			$this->add($table);
			
			$cut = new \Zend\Form\Element\Select('clarity');
			$cut->setLabel('Clarity');
			$cut->setAttributes(array('id' => 'clarity', 'options' => $objLookupTable->fetchClarityOptions(), 'class' => 'dropdown'));
			$this->add($cut);
			
			$depth = new \Zend\Form\Element\Text('depth');
			$depth->setLabel('Depth');
			$depth->setAttributes(array('id' => 'depth', 'class' => 'inputTxt', 'maxlength' => 6, 'data-numeric' => 'yes'));
			$this->add($depth);
			
			$table = new \Zend\Form\Element\Text('table');
			$table->setLabel('Table');
			$table->setAttributes(array('id' => 'table', 'class' => 'inputTxt', 'maxlength' => 6, 'data-numeric' => 'yes'));
			$this->add($table);
			
			$flurosence = new \Zend\Form\Element\Select('flurosence');
			$flurosence->setLabel('Flurosence');
			$flurosence->setAttributes(array('id' => 'flurosence', 'options' => $objLookupTable->fetchFlurosenceOptions(), 'class' => 'dropdown'));
			$this->add($flurosence);
			
			$measurement = new \Zend\Form\Element\Text('measurement');
			$measurement->setLabel('Measurements');
			$measurement->setAttributes(array('id' => 'measurement', 'class' => 'inputTxt'));
			$this->add($measurement);
			
			$add_to_rapnet = new \Zend\Form\Element\Checkbox('add_to_rapnet');
			$add_to_rapnet->setLabel('Add to RapNet');
			$add_to_rapnet->setAttributes(array('id' => 'add_to_rapnet'));
			$this->add($add_to_rapnet);

			$rapnet_pct_discount = new \Zend\Form\Element\Text('rapnet_pct_discount');
			$rapnet_pct_discount->setLabel('RapNet % Discount');
			$rapnet_pct_discount->setAttributes(array('id' => 'rapnet_pct_discount', 'class' => 'inputTxt', 'data-numeric' => 'yes'));
			$this->add($rapnet_pct_discount);

			$rapnet_cost_per_carat = new \Zend\Form\Element\Text('rapnet_cost_per_carat');
			$rapnet_cost_per_carat->setLabel('RapNet $/Carat');
			$rapnet_cost_per_carat->setAttributes(array('id' => 'rapnet_cost_per_carat', 'class' => 'inputTxt', 'data-numeric' => 'yes'));
			$this->add($rapnet_cost_per_carat);

			$rapnet_cost_per_carat = new \Zend\Form\Element\Text('rapnet_cost_per_carat');
			$rapnet_cost_per_carat->setLabel('RapNet $/Carat');
			$rapnet_cost_per_carat->setAttributes(array('id' => 'rapnet_cost_per_carat', 'class' => 'inputTxt', 'data-numeric' => 'yes'));
			$this->add($rapnet_cost_per_carat);

			/* TODO: maybe get source currencies from table */
			$source_ccy_options = array('AUD' => 'AUD', 'USD' => 'USD');
				
			$rapnet_price_source_ccy = new \Zend\Form\Element\Select('rapnet_price_source_ccy');
			$rapnet_price_source_ccy->setLabel('Source Currency');
			$rapnet_price_source_ccy->setAttributes(array('id' => 'rapnet_price_source_ccy', 'options' => $source_ccy_options, 'class' => 'dropdown'));
			$this->add($rapnet_price_source_ccy);

			$rapnet_price_usd = new \Zend\Form\Element\Text('rapnet_price_usd');
			$rapnet_price_usd->setLabel('USD Total');
			$rapnet_price_usd->setAttributes(array('id' => 'rapnet_price_usd', 'class' => 'inputTxt', 'data-numeric' => 'yes'));
			$this->add($rapnet_price_usd);
				
			$rapnet_price_aud = new \Zend\Form\Element\Text('rapnet_price_aud');
			$rapnet_price_aud->setLabel('RRP AUD');
			$rapnet_price_aud->setAttributes(array('id' => 'rapnet_price_aud', 'class' => 'inputTxt', 'data-numeric' => 'yes'));
			$this->add($rapnet_price_aud);

			$price_source_ccy = new \Zend\Form\Element\Select('price_source_ccy');
			$price_source_ccy->setLabel('Source Currency');
			$price_source_ccy->setAttributes(array('id' => 'price_source_ccy', 'options' => $source_ccy_options, 'class' => 'dropdown'));
			$this->add($price_source_ccy);

			$price = new \Zend\Form\Element\Text('price');
			$price->setLabel('Price (AUD)');
			$price->setAttributes(array('id' => 'price_aud', 'class' => 'inputTxt', 'data-numeric' => 'yes'));
			$this->add($price);
			
			$price_usd = new \Zend\Form\Element\Text('price_usd');
			$price_usd->setLabel('Price (USD)');
			$price_usd->setAttributes(array('id' => 'price_usd', 'class' => 'inputTxt', 'data-numeric' => 'yes'));
			$this->add($price_usd);

			$supplier_name = new \Zend\Form\Element\Text('supplier_name');
			$supplier_name->setLabel('Supplier Name');
			$supplier_name->setAttributes(array('id' => 'supplier_name', 'class' => 'inputTxt width60p', 'readonly' => 'readonly'));
			$this->add($supplier_name);
			
			$description = new \Zend\Form\Element\Textarea('description');
			$description->setLabel('Description');
			$description->setAttributes(array('id' => 'description', 'rows' => 6, 'style' => 'height:169px'));
			$this->add($description);
						
			$cancel = new \Zend\Form\Element\Button('cancel');
			$cancel->setLabel('Cancel');
			$cancel->setValue('Cancel');
			$cancel->setAttributes(array('id' => 'diamond_cancel', 'class' => 'cmnBtn cancelBtn', "onclick" => "cancelButtonProperty('frm_diamond', 'addItem');"));
			$this->add($cancel);
			
			$save = new \Zend\Form\Element\Button('save');
			$save->setLabel('Consign Item to Add');
			$save->setAttributes(array('id' => 'diamond_save', 'class' => 'cmnBtn blueBtn', 'onclick' => 'consignNewDiamond();'));
			$this->add($save);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}