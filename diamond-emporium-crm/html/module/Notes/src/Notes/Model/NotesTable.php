<?php
namespace Notes\Model;

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
	
	public function fetchNotes($limit, $offset, $leadId, $opportunityId){
		try{

			$fullname = new \Zend\Db\Sql\Expression('CONCAT(u.first_name, \' \', u.last_name)');
			
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('notes' => 'de_notes'))
				   ->columns(array('*'))
				   ->join(array('u' => 'de_users'), 'u.user_id= notes.created_by', array('created_by_name' => $fullname), 'left');
			
			if ($leadId) {
				$lead_predicate = new \Zend\Db\Sql\Where();
				$lead_predicate->equalTo('notes.grid_type_id', $leadId);
				$lead_predicate->equalTo('notes.grid_type', 'lead');
			}

			if ($opportunityId) {
				$opportunity_predicate = new \Zend\Db\Sql\Where();
				$opportunity_predicate->equalTo('notes.grid_type', 'opportunity');
				if (is_array($opportunityId)) {
					$opportunity_predicate->in('notes.grid_type_id', $opportunityId);
				} else {
					$opportunity_predicate->equalTo('notes.grid_type_id', $opportunityId);
				}
			}

			if ($lead_predicate && $opportunity_predicate) {
				$select->where(array($lead_predicate, $opportunity_predicate), \Zend\Db\Sql\Predicate\PredicateSet::OP_OR);
			} else if ($lead_predicate) {
				$select->where($lead_predicate);
			} else {
				$select->where($opportunity_predicate);
			}
			
			$select->order('notes.id DESC');
			$data = $this->executeQuery($select);
			$result = $data->toArray();
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
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
}