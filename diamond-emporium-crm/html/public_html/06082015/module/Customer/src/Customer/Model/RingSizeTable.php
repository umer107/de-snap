<?php
namespace Customer\Model;

use Zend\Db\TableGateway\TableGateway;

class RingSizeTable
{
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function fetchSelectOptions($type='')
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_ring_size');
			$select->columns(array('id', 'size'));
			$result = $this->tableGateway->selectWith($select)->toArray();
			if($type == 'left'){
				$options = array(0 => 'L-Select size');
			} else if($type == 'right'){
				$options = array(0 => 'R- Select size');
			} else {
				$options = array(0 => 'Select size');
			}
			foreach($result as $value){
				$options[$value['id']] = $value['size'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}