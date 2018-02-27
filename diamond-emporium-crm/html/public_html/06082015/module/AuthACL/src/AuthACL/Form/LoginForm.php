<?php

/**
 * This holds all the zend elements related to the Login form
 * Mandatory field(s) in this page are Email Address and Password
 * @author ranjan
 *
 */
 
namespace AuthACL\Form;

use Zend\Form\Form;
 
class LoginForm extends Form {
	/**
	 * Create form and its elements
	 */
	public function __construct($options = null) {
		try{
			parent::__construct ( $options );
			
			$this->setAttributes(array('name' => '', 'action' => '', 'method' => 'post'));
			
			$email = new \Zend\Form\Element\Text('email');
			$email->setLabel('Email');
			$this->add($email);
			
			$password = new \Zend\Form\Element\Password('password');
			$password->setLabel('Password');
			$this->add($password);
			
			$submit = new \Zend\Form\Element\Submit('submit');
			$submit->setValue('Login');
			
			$this->add($submit);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}