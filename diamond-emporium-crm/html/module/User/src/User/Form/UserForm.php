<?php

/**
 * User add & edit form
 */
 
namespace User\Form;

use Zend\Form\Form;
 
class UserForm extends Form {

	public function __construct($sm) {
		try{
			parent::__construct ( null );
			
			$objLookupTable = $sm->get('Inventory\Model\LookupTable');
						
			$this->setAttributes(array('name' => 'frm_user', 'id' => 'frm_user','action' => '/uploadfileimage','enctype' => 'multipart/form-data',  'method' => 'post'));
			
                        //Image
                        $user_image = new \Zend\Form\Element\Hidden('image');
			$user_image->setAttributes(array('id' => 'image'));
			$this->add($user_image);	
                         //EndImage
                        
			$user_id = new \Zend\Form\Element\Hidden('user_id');
			$user_id->setAttributes(array('id' => 'user_id'));
			$this->add($user_id);			
			
			$first_name = new \Zend\Form\Element\Text('first_name');
			$first_name->setLabel('First Name');
			$first_name->setAttributes(array('id' => 'first_name', 'class' => 'inputTxt', 'maxlength' => 64));
			$this->add($first_name);
			
			$last_name = new \Zend\Form\Element\Text('last_name');
			$last_name->setLabel('Last Name');
			$last_name->setAttributes(array('id' => 'last_name', 'class' => 'inputTxt', 'maxlength' => 64));
			$this->add($last_name);
			
                          
                      // Single file upload        
                    
                      
                       
                        
			$email = new \Zend\Form\Element\Text('email');
			$email->setLabel('Email');
			$email->setAttributes(array('id' => 'email', 'class' => 'inputTxt', 'maxlength' => 128));
			$this->add($email);
			
			$password = new \Zend\Form\Element\Password('password');
			$password->setLabel('Password');
			$password->setAttributes(array('id' => 'password', 'class' => 'inputTxt', 'maxlength' => 128));
			$this->add($password);
			
			$role_id = new \Zend\Form\Element\Select('role_id');
			$role_id->setLabel('Role');
			$role_id->setAttributes(array('id' => 'role_id', 'options' => $objLookupTable->fetchRoleOptions(), 'class' => 'dropdown'));
			$this->add($role_id);
			
			$mobile_number = new \Zend\Form\Element\Text('mobile_number');
			$mobile_number->setLabel('Mobile Number');
			$mobile_number->setAttributes(array('id' => 'mobile_number', 'class' => 'inputTxt pureNumaric', 'maxlength' => 10));
			$this->add($mobile_number);
			
			$color = new \Zend\Form\Element\Text('color');
			$color->setLabel('Colour');
			$color->setAttributes(array('id' => 'color', 'class' => 'inputTxt', 'maxlength' => 6, 'readonly' => 'readonly'));
			$this->add($color);
                        
                       $selectbudget = array(
                         '$2-5k',
                         '$5-10k',
                        '$10-20k',
                        '20k+',
                        );
                        $user_budget = new \Zend\Form\Element\Select('user_budget');
			$user_budget->setLabel('User Budget');
			$user_budget->setAttributes(array('id' => 'user_budget', 'options' => $selectbudget, 'class' => 'dropdown'));
			$this->add($user_budget);
						
			$cancel = new \Zend\Form\Element\Button('cancel');
			$cancel->setLabel('Cancel');
			$cancel->setValue('Cancel');
			$cancel->setAttributes(array('id' => 'cancel', 'class' => 'cmnBtn cancelBtn', "onclick" => "cancelButtonProperty('frm_user', 'userForm');"));
			$this->add($cancel);
			
			$save = new \Zend\Form\Element\Button('save');
			$save->setLabel('Save');
			$save->setAttributes(array('id' => 'save', 'class' => 'cmnBtn blueBtn', 'onclick' => 'saveUser($(\'#frm_user\'));'));
			$this->add($save);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}
