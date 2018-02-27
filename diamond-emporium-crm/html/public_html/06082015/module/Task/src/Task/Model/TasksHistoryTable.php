<?php
namespace Task\Model;

use Zend\Db\Sql\Where;

use Zend\Db\TableGateway\TableGateway;

class TasksHistoryTable
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
	
	public function fetchTaskHistoryDetails($id)
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$taskCreaterFullname = new \Zend\Db\Sql\Expression(
				'CONCAT(users.first_name, \' \', users.last_name)'
			);
			$taskAssignedFullname = new \Zend\Db\Sql\Expression(
				'CONCAT(assign.first_name, \' \', assign.last_name)'
			);
			$taskAssignedShortname = new \Zend\Db\Sql\Expression(
				'CONCAT(substring(assign.first_name, 1, 1), \'\', substring(assign.last_name, 1, 1))'
			);
			$dateFormatChange = new \Zend\Db\Sql\Expression(
				'DATE_FORMAT(history.created_date, "%d/%m/%y %h:%i %p")'
			);
			$select->from(array('history' => 'de_task_history'))
				   ->columns(array('id', 'task_id', 'metadata', 'data', 'created_by', 'createdDate' => $dateFormatChange))
				   ->join(array('ca' => 'de_comment_attachments'), 'ca.comment_id = history.id', array('files'), 'left')
				   ->join(array('users' => 'de_users'), 'history.created_by = users.user_id', array('task_created_fullname' => $taskCreaterFullname), 'left')
				   ->join(array('assign' => 'de_users'), new \Zend\Db\Sql\Expression('history.data = assign.user_id AND history.metadata = \'assigned_to\''), array('task_assigned_fullname' => $taskAssignedFullname, 'task_assigned_shortname' => $taskAssignedShortname), 'left')
				   ->join(array('category' => 'de_tasks_category'), new \Zend\Db\Sql\Expression('history.data = category.id AND history.metadata = \'task_category\''), array('category_title' => 'title'), 'left')
				   ->join(array('subject' => 'de_tasks_subject'), new \Zend\Db\Sql\Expression('history.data = subject.id AND history.metadata = \'task_subject\''), array('subject_title' => 'title'), 'left')
				   ->join(array('priority' => 'de_tasks_priority'), new \Zend\Db\Sql\Expression('history.data = priority.id AND history.metadata = \'task_priority\''), array('priority_title' => 'title'), 'left')
				   /*->join(array('cus' => 'de_customers'), 'opp.user_id = cus.id', array('customer_fullname' => $customerFullname, 'customer_email' => 'email', 
				   'customer_mobile' =>  'mobile', 'partner_data_id' => 'partner_id'), 'left')
				   ->join(array('ref' => 'de_customers'), 'opp.referred_by_customer = ref.id', array('refered_fullname' => $referedFullname, 'refered_email' => 'email', 
				   'refered_mobile' =>  'mobile'), 'left')*/
				   //->join(array('own' => 'de_users'), 'opp.record_owner_id = own.user_id', array('account_owner' => $ownerFullname), 'left')
				   ->where(array('history.task_id = ?' => $id));
			$select->order('history.id DESC');	
			//echo $select->getSqlString(); exit;	
			$data = $this->executeQuery($select);
			$result = $data->toArray();
			return $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchLatestTaskHistoryRecord($id)
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$taskCreaterFullname = new \Zend\Db\Sql\Expression(
				'CONCAT(users.first_name, \' \', users.last_name)'
			);
			$taskAssignedFullname = new \Zend\Db\Sql\Expression(
				'CONCAT(assign.first_name, \' \', assign.last_name)'
			);
			$taskAssignedShortname = new \Zend\Db\Sql\Expression(
				'CONCAT(substring(assign.first_name, 1, 1), \'\', substring(assign.last_name, 1, 1))'
			);
			$dateFormatChange = new \Zend\Db\Sql\Expression(
				'DATE_FORMAT(history.created_date, "%d/%m/%y %h:%i %p")'
			);
			$select->from(array('history' => 'de_task_history'))
				   ->columns(array('id', 'task_id', 'metadata', 'data', 'created_by', 'createdDate' => $dateFormatChange))
				   ->join(array('ca' => 'de_comment_attachments'), 'ca.comment_id = history.id', array('files'), 'left')
				   ->join(array('users' => 'de_users'), 'history.created_by = users.user_id', array('task_created_fullname' => $taskCreaterFullname), 'left')
				   ->join(array('assign' => 'de_users'), new \Zend\Db\Sql\Expression('history.data = assign.user_id AND history.metadata = \'assigned_to\''), array('task_assigned_fullname' => $taskAssignedFullname, 'task_assigned_shortname' => $taskAssignedShortname), 'left')
				   ->join(array('category' => 'de_tasks_category'), new \Zend\Db\Sql\Expression('history.data = category.id AND history.metadata = \'task_category\''), array('category_title' => 'title'), 'left')
				   ->join(array('subject' => 'de_tasks_subject'), new \Zend\Db\Sql\Expression('history.data = subject.id AND history.metadata = \'task_subject\''), array('subject_title' => 'title'), 'left')
				   ->join(array('priority' => 'de_tasks_priority'), new \Zend\Db\Sql\Expression('history.data = priority.id AND history.metadata = \'task_priority\''), array('priority_title' => 'title'), 'left')
				   /*->join(array('cus' => 'de_customers'), 'opp.user_id = cus.id', array('customer_fullname' => $customerFullname, 'customer_email' => 'email', 
				   'customer_mobile' =>  'mobile', 'partner_data_id' => 'partner_id'), 'left')
				   ->join(array('ref' => 'de_customers'), 'opp.referred_by_customer = ref.id', array('refered_fullname' => $referedFullname, 'refered_email' => 'email', 
				   'refered_mobile' =>  'mobile'), 'left')*/
				   //->join(array('own' => 'de_users'), 'opp.record_owner_id = own.user_id', array('account_owner' => $ownerFullname), 'left')
				   ->where(array('history.task_id = ?' => $id));
			$select->order('history.id DESC');	
			$select->limit(1);
			//echo $select->getSqlString(); exit;	
			$data = $this->executeQuery($select);
			$result = $data->toArray();
			return $result[0];
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function saveTaskHistory($data, $updateId='')
	{
		try{
			if($updateId != ''){
				return $this->tableGateway->update($data, array('id' => $updateId));
			} else {
				$this->tableGateway->insert($data);
				return $this->tableGateway->lastInsertValue;
			}
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deleteTaskHistory($where){
		try{
			if(is_array($where))
				return $this->tableGateway->delete($where);
			else
				return $this->tableGateway->delete(array('task_id' => $where));
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchCommentDetails($comment_id){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('history' => 'de_task_history'))
				   ->columns(array('id'))
				   ->join(array('ca' => 'de_comment_attachments'), 'ca.comment_id = history.id', array('files'), 'left')
				   ->where(array('ca.comment_id = ?' => $comment_id));
				
			$data = $this->executeQuery($select);
			$result = $data->current();
			return (array) $result;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchTaskHistoryIds($task_id){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_task_history')
				   ->columns(array('id'))
				   ->where(array('task_id' => $task_id));
			   
			$result = $this->tableGateway->selectWith($select)->toArray();
			
			if(!empty($result)){
				$idsArr = array();
				
				foreach($result as $value){
					$idsArr[] = $value['id'];
				}
				
				return $idsArr;
			}
			
			return;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function editComment($comment_id, $comment){
		try{
			return $this->tableGateway->update(array('data' => $comment), array('id' => $comment_id));
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deleteComment($comment_id){
		try{
			return $this->tableGateway->delete(array('id' => $comment_id));
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}