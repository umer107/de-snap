<?php
namespace Opportunities\Model;

use Zend\Db\Sql\Where;

use Zend\Db\TableGateway\TableGateway;

class NotesTable
{
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}
	
	public function executeQuery($select){
		$adapter = $this->tableGateway->getAdapter();
		$statement = $adapter->createStatement();
		$select->prepareStatement($adapter, $statement);
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet;
	}
	
	public function saveNotes($data, $updateId='')
	{
		try{
			if($updateId != ''){
				return $this->tableGateway->update($data, array('id' => $updateId));
			} else {
				return $this->tableGateway->insert($data);
			}
		}catch(\Exception $e){
			echo $e->getMessage (); exit;
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function getNoteDetails($id)
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('notes' => 'de_notes'))
				   ->columns(array('*'));
			$select->where('notes.id='.$id);	
			//echo $select->getSqlString(); exit;	
			$data = $this->executeQuery($select);
			$result = $data->toArray();
			return $result[0];
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	
	public function deleteNotes($noteId)
	{
		try{
			return $this->tableGateway->delete(array('id' => $noteId));
		}catch(\Exception $e){
			echo $e->getMessage (); exit;
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function convertNotes($data, $where)
	{
		try{
			return $this->tableGateway->update($data, $where);
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}