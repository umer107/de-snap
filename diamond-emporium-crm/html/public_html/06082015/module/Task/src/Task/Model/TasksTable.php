<?php
namespace Task\Model;

use Zend\Db\Sql\Where;

use Zend\Db\TableGateway\TableGateway;

class TasksTable
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
	
	public function fetchAll($entityId = null, $assignedFor = null, $taskStatus = null, $limit = null, $offset = null)
	{
		try{
			
			$result['total_count'] = $this->getTasksCount($entityId, $assignedFor, $taskStatus);
			
			$select = new \Zend\Db\Sql\Select();
			$taskOwnerFullname = new \Zend\Db\Sql\Expression(
				'CONCAT(u.first_name, \' \', u.last_name)'
			);
			$taskOwnerShortname = new \Zend\Db\Sql\Expression(
				'CONCAT(substring(u.first_name, 1, 1), \'\', substring(u.last_name, 1, 1))'
			);
			
			$select->from(array('t' => 'de_tasks'))
				   ->columns(array('task_id' => 'id', 'task_title', 'due_date', 'due_date_repeat_status', 'due_date_end_on'))
				   ->join(array('u' => 'de_users'), 't.assigned_to = u.user_id', array('task_owner_shortname' => $taskOwnerShortname, 'task_owner_fullname' => $taskOwnerFullname), 'left');
			
			if(!empty($assignedFor) && $assignedFor == 'lead')
				$select->where(array('t.lead_id' => $entityId));
			elseif(!empty($assignedFor) && $assignedFor == 'opportunity')
				$select->where(array('t.opportunity_id' => $entityId));
			elseif(!empty($assignedFor) && $assignedFor == 'customer')
				$select->where(array('t.customer_id' => $entityId));				
				
			if(!empty($taskStatus))	
				$select->where(array('t.task_status' => $taskStatus));
			
			$select->order('t.id DESC');
			
			if($limit)
				$select->limit($limit);
			if($offset)
				$select->offset($offset);
			
			$adapter = $this->tableGateway->getAdapter();
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			$resultSet = new \Zend\Db\ResultSet\ResultSet();
			$resultSet->initialize($statement->execute());
			
			$result['data']  = $resultSet->toArray();
			
			return $result;
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function fetchTaskDetails($id)
	{
		try{
			$select = new \Zend\Db\Sql\Select();
			$taskAssignedFullname = new \Zend\Db\Sql\Expression(
				'CONCAT(users.first_name, \' \', users.last_name)'
			);
			$taskAssignedShortname = new \Zend\Db\Sql\Expression(
				'CONCAT(substring(users.first_name, 1, 1), \'\', substring(users.last_name, 1, 1))'
			);
			$select->from(array('tasks' => 'de_tasks'))
				   ->columns(array('main_task_id' => 'id', 'task_title', 'task_category', 'task_subject', 'due_date', 'due_date_repeat_status', 'due_date_end_on', 'task_priority', 'assigned_to', 'task_status'))
				   ->join(array('ca' => 'de_comment_attachments'), 'tasks.id = ca.comment_id', array('files'), 'left')
				   ->join(array('users' => 'de_users'), 'tasks.assigned_to = users.user_id', array('task_assigned_fullname' => $taskAssignedFullname, 'task_assigned_shortname' => $taskAssignedShortname), 'left')
				   /*->join(array('cus' => 'de_customers'), 'opp.user_id = cus.id', array('customer_fullname' => $customerFullname, 'customer_email' => 'email', 
				   'customer_mobile' =>  'mobile', 'partner_data_id' => 'partner_id'), 'left')
				   ->join(array('ref' => 'de_customers'), 'opp.referred_by_customer = ref.id', array('refered_fullname' => $referedFullname, 'refered_email' => 'email', 
				   'refered_mobile' =>  'mobile'), 'left')*/
				   //->join(array('own' => 'de_users'), 'opp.record_owner_id = own.user_id', array('account_owner' => $ownerFullname), 'left')
				   ->where(array('tasks.id = ?' => $id));
			//$select->where('opp.id='$id);	
			//echo $select->getSqlString(); exit;	
			$data = $this->executeQuery($select);
			$result = $data->toArray();
			return $result[0];
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function saveTask($data, $updateId='')
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
	
	public function deleteTask($where){
		try{
			if(is_array($where))
				return $this->tableGateway->delete($where);
			else
				return $this->tableGateway->delete(array('id' => $where));
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function changeStatus($task_id, $data){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_tasks')
				   ->columns(array('task_status'))
				   ->where(array('id' => $task_id));
			
			$task = (array) $this->tableGateway->selectWith($select)->current();
			$data['task_status'] = ($task['task_status'] == 1) ? 2 : 1;
				
			$this->tableGateway->update($data, array('id' => $task_id));
			
			return $data['task_status'];
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function getTasksCount($entityId = null, $assignedFor = null, $taskStatus = null){
		try{
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_tasks')
				   ->columns(array('id'));
				   
			if(!empty($assignedFor) && $assignedFor == 'lead')
				$select->where(array('lead_id' => $entityId));
			elseif(!empty($assignedFor) && $assignedFor == 'opportunity')
				$select->where(array('opportunity_id' => $entityId));
			elseif(!empty($assignedFor) && $assignedFor == 'customer')
				$select->where(array('customer_id' => $entityId));
				
			if(!empty($taskStatus))			
				$select->where(array('task_status' => $taskStatus));

			$counter = $this->tableGateway->selectWith($select);
			return count($counter);
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function convertTasks($lead_id, $customer_id){
		try{
			$data = array('lead_id' => 0, 'customer_id' => $customer_id);
			$where = array('lead_id' => $lead_id);
			return $this->tableGateway->update($data, $where);			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}