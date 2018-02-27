<?php
 namespace AuthACL\Model;

 // Add these import statements
 use Zend\InputFilter\InputFilter;
 use Zend\InputFilter\InputFilterAwareInterface;
 use Zend\InputFilter\InputFilterInterface;

class Login implements InputFilterAwareInterface
{
     public $email;
     public $password;
     protected $inputFilter;                       // <-- Add this variable

	/**
	 * Store array data into data members in this class
	 */
	public function exchangeArray($data)
	{
		try{
			$this->email = (isset($data['email'])) ? $data['email'] : null;
			$this->password  = (isset($data['password']))  ? $data['password']  : null;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

	// Add content to these methods:
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}

	/**
	 * Add valivators for the fields in the form
	 */
	public function getInputFilter()
	{
		try{
			if (!$this->inputFilter) {
				$inputFilter = new InputFilter();
	
				$inputFilter->add(array(
					'name'     => 'email',
					'required' => true,
					'filters'  => array(
						array('name' => 'StripTags'),
						array('name' => 'StringTrim'),
					),
					'validators' => array(
						/*array(
							'name'    => 'NotEmpty',
							'options' => array(
								'messages' => array(
									\Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter email address',
								)
							)
						),*/
						array(
							'name'    => 'EmailAddress',
							'options' => array(
								'messages' => array(
									\Zend\Validator\EmailAddress::INVALID_FORMAT => 'Please enter valid email address',
								)
							)
						),
					),
				));
	
				$inputFilter->add(array(
					'name'     => 'password',
					'required' => true,
					'filters'  => array(
						array('name' => 'StripTags'),
						array('name' => 'StringTrim'),
					),
					'validators' => array(
						array(
							'name'    => 'NotEmpty',
							'options' => array(
								'messages' => array(
									\Zend\Validator\NotEmpty::IS_EMPTY => 'Please enter password',
								)
							)
						),
					),
				));
	
				$this->inputFilter = $inputFilter;
			}
	
			return $this->inputFilter;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}
