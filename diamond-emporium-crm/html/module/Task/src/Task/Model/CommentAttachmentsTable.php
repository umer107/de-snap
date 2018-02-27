<?php
namespace Task\Model;

use Zend\Db\Sql\Where;

use Zend\Db\TableGateway\TableGateway;

class CommentAttachmentsTable
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
	
	public function fetchAll($entityId, $assignedFor = null)
	{
		try{
			$keyword = trim($keyword);
			$select = new \Zend\Db\Sql\Select();
			$taskOwnerFullname = new \Zend\Db\Sql\Expression(
				'CONCAT(u.first_name, \' \', u.last_name)'
			);
			$taskOwnerShortname = new \Zend\Db\Sql\Expression(
				'CONCAT(substring(u.first_name, 1, 1), \'\', substring(u.last_name, 1, 1))'
			);
			
			$select->from(array('t' => 'de_tasks'))
				   ->columns(array('task_id' => 'id', 'task_title', 'due_date'))
				   ->join(array('u' => 'de_users'), 't.assigned_to = u.user_id', array('task_owner_shortname' => $taskOwnerShortname, 'task_owner_fullname' => $taskOwnerFullname), 'left');
				   
			if(!empty($assignedFor) && $assignedFor == 'lead')
				$select->where(array('t.lead_id' => $entityId, 't.task_status' => 1));
			
			//$select->limit($limit);
			//$select->offset($offset);
			
			$adapter = $this->tableGateway->getAdapter();
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
	
	public function saveAttachment($data)
	{
		try{
			$this->tableGateway->insert($data);
			return $this->tableGateway->lastInsertValue;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deleteAttachments($comment_ids){
		try{
			return $this->tableGateway->delete(new \Zend\Db\Sql\Predicate\Expression('comment_id IN ('.implode(',', $comment_ids).')'));
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}