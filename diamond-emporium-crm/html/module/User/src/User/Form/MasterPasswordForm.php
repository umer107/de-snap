<?php

/**
 * This holds all the zend elements related to the Login form
 * Mandatory field(s) in this page are Email Address and Password
 * @author ranjan
 *
 */
 
namespace User\Form;

use Zend\Form\Form;
 
class MasterPasswordForm extends Form {

	public function __construct() {
		try{
			parent::__construct ( null );
						
			$this->setAttributes(array('name' => 'frm_master_password', 'id' => 'frm_master_password', 'method' => 'post'));
			
			$password = new \Zend\Form\Element\Password('mp_password');
			$password->setLabel('Password');
			$password->setAttributes(array('id' => 'mp_password', 'class' => 'inputTxt', 'maxlength' => 64));
			$this->add($password);
			
			$confirm_password = new \Zend\Form\Element\Password('mp_confirm_password');
			$confirm_password->setLabel('Confirm Password');
			$confirm_password->setAttributes(array('id' => 'mp_confirm_password', 'class' => 'inputTxt', 'maxlength' => 64));
			$this->add($confirm_password);
						
			$cancel = new \Zend\Form\Element\Button('mp_cancel');
			$cancel->setLabel('Cancel');
			$cancel->setValue('Cancel');
			$cancel->setAttributes(array('id' => 'mp_cancel', 'class' => 'cmnBtn cancelBtn', "onclick" => "cancelButtonProperty('frm_master_password', 'masterPassword');"));
			$this->add($cancel);
			
			$save = new \Zend\Form\Element\Button('mp_save');
			$save->setLabel('Save');
			$save->setAttributes(array('id' => 'mp_save', 'class' => 'cmnBtn blueBtn', 'onclick' => 'saveMasterPassword($(\'#frm_master_password\'));'));
			$this->add($save);
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}