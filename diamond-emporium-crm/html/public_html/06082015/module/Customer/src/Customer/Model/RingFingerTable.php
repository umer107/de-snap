<?php
namespace Customer\Model;

use Zend\Db\TableGateway\TableGateway;

class RingFingerTable
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
			$select->from('de_ring_finger');
			$select->columns(array('id', 'finger'));
			$result = $this->tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select finger');
			foreach($result as $value){
				$options[$value['id']] = $value['finger'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}