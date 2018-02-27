<?php
namespace Customer\Model;

use Zend\Db\TableGateway\TableGateway;

class CountryTable
{
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function fetchSelectOptions()
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_country');
			$select->columns(array('id', 'name'));
			$result = $this->tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['id']] = $value['name'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}