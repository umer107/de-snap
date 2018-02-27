<?php
namespace Customer\Model;

use Zend\Db\TableGateway\TableGateway;

class UsersTable
{
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function fetchSelectOptions()
	{
		try{			
			$fullname = new \Zend\Db\Sql\Expression(
				'CONCAT(first_name, \' \', last_name)'
			);
			
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_users')
				   ->columns(array('user_id', 'fullname' => $fullname));
			$result = $this->tableGateway->selectWith($select)->toArray();
			
			$options = array(0 => 'Select');
			foreach($result as $value){
				$options[$value['user_id']] = $value['fullname'];
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchUsersForTasks()
	{
		try{			
			$fullname = new \Zend\Db\Sql\Expression(
				'CONCAT(first_name, \' \', last_name)'
			);
			$shortname = new \Zend\Db\Sql\Expression(
				'CONCAT(substring(first_name, 1, 1), \'\', substring(last_name, 1, 1))'
			);
			
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_users')
				   ->columns(array('user_id', 'fullname' => $fullname, 'shortname' => $shortname));
			$result = $this->tableGateway->selectWith($select)->toArray();
			
			$options = array('0' => 'Unassigned');
			foreach($result as $value){
				$options[$value['user_id']] = '<span>'.strtoupper($value['shortname']).'</span>'.ucfirst($value['fullname']);
			}
			return $options;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchUsersDetails($id)
	{
		try{			
			$fullname = new \Zend\Db\Sql\Expression(
				'CONCAT(first_name, \' \', last_name)'
			);
			$shortname = new \Zend\Db\Sql\Expression(
				'CONCAT(substring(first_name, 1, 1), \'\', substring(last_name, 1, 1))'
			);
			
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_users')
				   ->columns(array('user_id', 'email', 'fullname' => $fullname, 'shortname' => $shortname))
				    ->where(array('user_id' => $id));
			$result = $this->tableGateway->selectWith($select)->toArray();
			
			return $result[0];
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}