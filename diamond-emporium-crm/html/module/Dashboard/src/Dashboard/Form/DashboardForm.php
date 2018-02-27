<?php
/**
 * Created by NetBeans
 * User: MuhammadUmarWaheed
 */
namespace Dashboard\Form;

use Zend\Form\Form;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class DashboardForm extends Form
{
    protected $adapter;
    public function __construct(AdapterInterface $dbAdapter)
    {
        $this->adapter =$dbAdapter;
        try{
            
       parent::__construct('dashboard');
       
      
        
        $this->setAttributes(array('method' => 'post'));
        
        $dashboard_id = new \Zend\Form\Element\Hidden('id');
        $this->add($dashboard_id);
        
        
        $first_name = new \Zend\Form\Element\Text('first_name');
	$first_name->setAttributes(array('id' => 'first_name', 'autocomplete' => 'off' ,'class' => 'firstname', 'placeholder' => 'First name','type'  => 'text',  'maxlength' => 40));
	$this->add($first_name);
        
        
        $last_name = new \Zend\Form\Element\Text('last_name');
	$last_name->setAttributes(array('id' => 'last_name', 'autocomplete' => 'off' , 'placeholder' => 'Last name', 'type'  => 'text', 'class' => 'lastname', 'maxlength' => 40));
	$this->add($last_name);
        
        $phoneNumber = new \Zend\Form\Element\Text('phone_number');
	$phoneNumber->setAttributes(array('id' => 'phonenumber', 'autocomplete' => 'off' , 'type'  => 'text','placeholder' => 'Phone number*',  'class' => 'phonenumber', 'maxlength' => 20));
	$this->add($phoneNumber);
        
        $email = new \Zend\Form\Element\Text('email');
	$email->setAttributes(array('id' => 'email', 'type'  => 'text', 'autocomplete' => 'off' , 'placeholder' => 'Email*', 'class' => 'email checkEmailCount', 'maxlength' => 40));
	$this->add($email);
  
        
     
          
           
         
        $referral = new \Zend\Form\Element\Select('referral');
        $referral->setAttributes(array('id' => 'referral', 'type'  => 'text',));  
        $this->add($referral);
        
        
        $special_instructions = new \Zend\Form\Element\Text('special_instructions');
	$special_instructions->setAttributes(array('id' => 'last_name', 'autocomplete' => 'off' , 'type'  => 'text',  'placeholder' => 'Special instruction', 'type'  => 'text', 'class' => 'instructions color-red', 'maxlength' => 300));
	$this->add($special_instructions);
        
    
        $budget = new \Zend\Form\Element\Text('budget');      
	$budget->setAttributes(array('id' => 'budget','type'  => 'text', 'autocomplete' => 'off' , 'class' => '', 'maxlength' => 20, 'data-numeric' => 'yes'));
	$this->add($budget);
        
        
        $reference_product = new \Zend\Form\Element\Text('reference_product');
	$reference_product->setAttributes(array('id' => 'reference_product', 'class' => '', 'maxlength' => 45));
	$this->add($reference_product);
        
      
        $contact_method = new \Zend\Form\Element\Text('contact_method');
	$contact_method->setAttributes(array('id' => 'contact_method', 'class' => '', 'maxlength' => 128));
	$this->add($reference_product);
        
      
        $model = $this->getUserForSelect();
        $groups = array();
        foreach ($model as $id => $list) {
          $groups[$id] = $list;
        } 
    
        
        
    $specify_requirements = new \Zend\Form\Element\Text('specify_requirements');
	$specify_requirements->setAttributes(array('id' => 'specify_requirements', 'autocomplete' => 'off' , 'type'  => 'text',  'placeholder' => 'Specify requirements', 'type'  => 'text', 'class' => 'requirements', 'maxlength' => 300));
	$this->add($specify_requirements);
       
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Save',
                'class' => 'd-b full-width',
                'id' => 'submitbutton',
            ),
        ));

        }
        catch(\Exception $e){
    			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
    		}

    }
    
    //Get User FirstName and Id from User Table
    public function getUserForSelect()
    {
     //TODO HANDLER
    }
    
    
    	public function fetchUserData()
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('t' => 'de_users'))
				   ->columns(array('User_id' => 'user_id', 'User_First_Name' => 'first_name', 'User_Status' => 'status' , 'User_Image' => 'image' ));
			
		
                        $adapter = $this->adapter;
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($statement->execute());
			
			$result = $resultSet->toArray();
			
			return $result;
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
    
}