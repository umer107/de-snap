<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Task\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
		try{
			// Write your code here
			$sm = $this->getServiceLocator();
			$config = $sm->get('Config');
			
			$tasksTable = $sm->get('Task\Model\TasksTable');
			
			$tasks = $tasksTable->fetchAll(null, null, 1);
			foreach($tasks as $key => $value){
				$tasks[$key]['is_overdue'] = \De\Lib::isTaskOverDue($value);
			}
			
			$closedTasks = $tasksTable->fetchAll(null, null, 2);
			foreach($closedTasks as $key => $value){
				$closedTasks[$key]['is_overdue'] = \De\Lib::isTaskOverDue($value);
			}
			
			$usersTable = $sm->get('Customer\Model\UsersTable');
			$usersList = $usersTable->fetchUsersForTasks();
			$tasksCategoryTable = $sm->get('Task\Model\TasksCategoryTable');
			$CategoryList = $tasksCategoryTable->fetchAll();
			$tasksSubjectTable = $sm->get('Task\Model\TasksSubjectTable');
			$subjectList = $tasksSubjectTable->fetchAll();
			$tasksPriorityTable = $sm->get('Task\Model\TasksPriorityTable');
			$priorityList = $tasksPriorityTable->fetchAll();			
			
			$view = new ViewModel(array('tasks' => $tasks, 'closedTasks' => $closedTasks,
										'usersList' => $usersList, 'CategoryList' => $CategoryList,
										'subjectList' => $subjectList, 'priorityList' => $priorityList,
										'config' => $config, 'allTasks' => true));
			$view->setTemplate('task/index/tasks.phtml'); // path to phtml file under view folder
			return $view;
			
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
    
    public function createtaskAction()
    {
    	try{
			// Write your code here
			
    		$request = $this->getRequest();
			if($request->isPost()){
				$sm = $this->getServiceLocator();
				$identity = $sm->get('AuthService')->getIdentity();
				
				$tasksTable = $this->getServiceLocator()->get('Task\Model\TasksTable');
				
				$posts = $request->getPost()->toArray();
				$data = array('task_title' => $posts['task_title'], 'due_date' => 'No Due Date', 'created_by' => $identity['user_id'], 'created_date' => date('Y-m-d H:i:s'));
				if($posts['entity_type'] == 'lead')
					$data['lead_id'] = $posts['entity_id'];
				elseif($posts['entity_type'] == 'opportunity')
					$data['opportunity_id'] = $posts['entity_id'];
				elseif($posts['entity_type'] == 'customer')
					$data['customer_id'] = $posts['entity_id'];
				$task_id =  $tasksTable->saveTask($data);
				
				$data['task_id'] = $task_id;
				
				echo json_encode($data);				
				
				//$tasksTable = $sm->get('Task\Model\TasksTable');
				//$tasks = $tasksTable->fetchAll($id, 'lead');
			}
			
			exit;
			
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	public function gettaskdetailsAction()
    {
		try{
			$request = $this->getRequest();
			
			if($request->isPost()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				$id = $data['taskId'];
				$tasksTable = $this->getServiceLocator()->get('Task\Model\TasksTable');
				$taskData = $tasksTable->fetchTaskDetails($id);
				
				$config = $this->getServiceLocator()->get('Config');
				
				/*if(empty($taskData['due_date']) || !strtotime($taskData['due_date'])){
					$taskData['is_overdue'] = 0;
				}else{
					 = strtotime($taskData['due_date']) >= strtotime(date("Y-m-d")) ? 0 : 1;
				}*/
				
				$taskData['is_overdue'] = \De\Lib::isTaskOverDue($taskData);
				//$taskData['due_date'] = empty($taskData['due_date']) ? '' : date($config['phpDateFormat'], strtotime($taskData['due_date']));
				$taskData['due_date'] = empty($taskData['due_date']) || !strtotime($taskData['due_date']) ? $taskData['due_date'] : date($config['phpDateFormat'], strtotime($taskData['due_date']));
				
				echo json_encode($taskData);
				exit;
			}
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	public function updateindividualAction()
    {
		try{
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				$sm = $this->getServiceLocator();
				$identity = $sm->get('AuthService')->getIdentity();
				
				if($data['column_title'] == 'due_date'){
					if(strtotime(\De\Lib::dbDateFormat($data['column_value']))){
						$updateData[$data['column_title']] = \De\Lib::dbDateFormat($data['column_value']);
						
						$updateData['due_date_repeat_status'] = empty($data['repeat']) ? 0 : $data['repeat'];
						if(!empty($data['endon_date']))
							$updateData['due_date_end_on'] = \De\Lib::dbDateFormat($data['endon_date']);
							
					}else{
						$updateData[$data['column_title']] = $data['column_value'];
						$updateData['due_date_repeat_status'] = 0;
						$updateData['due_date_end_on'] = null;
					}
				}else{
					$updateData[$data['column_title']] = $data['column_value'];
				}
				
				$updateData['updated_by'] = $identity['user_id'];
				$updateData['updated_date'] = date('Y-m-d H:i:s');
				$tasksTable = $this->getServiceLocator()->get('Task\Model\TasksTable');
				$taskData = $tasksTable->saveTask($updateData, $data['taskId']);
				
				if($taskData){
					$tasksHistoryTable = $this->getServiceLocator()->get('Task\Model\TasksHistoryTable');
					$historyData = array();
					$historyData['task_id'] = $data['taskId'];
					$historyData['created_by'] = $identity['user_id'];
					$historyData['metadata'] = $data['column_title'];
					$historyData['data'] = $data['column_value'];
					$historyData['created_date'] = date('Y-m-d H:i:s');
					
					$taskHistoryData = $tasksHistoryTable->saveTaskHistory($historyData);
				}
				echo $taskHistoryData;
			}
			
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
    
    public function savecommentAction()
    {
    	try{
			// Write your code here
			
    		$request = $this->getRequest();
			if($request->isPost()){
				$sm = $this->getServiceLocator();
				$identity = $sm->get('AuthService')->getIdentity();
				
				$taskHistoryTable = $this->getServiceLocator()->get('Task\Model\TasksHistoryTable');
				
				$posts = $request->getPost()->toArray();
				$data = array('metadata' => 'comment', 'data' => $posts['task_comment'], 'task_id' => $posts['task_id'], 'created_by' => $identity['user_id'], 'created_date' => date('Y-m-d H:i:s'));
				echo $taskHistoryTable->saveTaskHistory($data);
			}
			
			exit;
			
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    
    }
	
	public function attachfileAction()
	{
		try{
			// Write your code here
			
			$request = $this->getRequest();
			if($request->isPost()){		
				if(!empty($_POST['comment_id']) && !empty($_FILES)){
					$targetFolder = '/comment_attachments/'.$_POST['comment_id']; // Relative to the root for local
					//$targetFolder = '/public/comment_attachments/'.$_POST['comment_id']; // Relative to the root for live (diamond.openseed.com.au)
				
					mkdir($_SERVER['DOCUMENT_ROOT'] . $targetFolder);
				
					$tempFile = $_FILES['Filedata']['tmp_name'];
					$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
					$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];
					
					// Validate the file type
					$fileTypes = array('pdf', 'txt', 'html', 'zip', 'doc', 'xls', 'ppt', 'gif', 'png', 'jpeg', 'jpg'); // File extensions
					
					$fileParts = pathinfo($_FILES['Filedata']['name']);
					
					if (in_array($fileParts['extension'],$fileTypes)) {
						move_uploaded_file($tempFile, $targetFile);						
						echo 1;						
					} else {
						echo 'Invalid file type.';
					}
				}
			}
			
			exit;
			
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function gettaskhistorydetailsAction()
    {
		try{
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				$type = '';
				$id = $data['taskId'];
				$type = $data['type'];
				$tasksHistoryTable = $this->getServiceLocator()->get('Task\Model\TasksHistoryTable');
				if(!empty($type)){
					$taskHistoryData = $tasksHistoryTable->fetchLatestTaskHistoryRecord($id);
				} else {
					$taskHistoryData = $tasksHistoryTable->fetchTaskHistoryDetails($id);
				}
				
				echo json_encode($taskHistoryData);
				exit;
			}
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	public function saveattachmentsAction(){
		try{
			$request = $this->getRequest();
			if($request->isPost()){
			
				$posts = $request->getPost()->toArray();
				$commentAttachmentsTable = $this->getServiceLocator()->get('Task\Model\CommentAttachmentsTable');
						
				$sm = $this->getServiceLocator();
				$identity = $sm->get('AuthService')->getIdentity();
				
				$files = explode(',', trim($posts['files'], ','));
				$uploadedFiles = array();
				
				$attachmentFolder = $_SERVER['DOCUMENT_ROOT'].'/comment_attachments/'.$posts['comment_id']; // Relative to the root for local
				//$attachmentFolder = $_SERVER['DOCUMENT_ROOT'].'/public/comment_attachments/'.$posts['comment_id'];  // Relative to the root for live (diamond.openseed.com.au)
				
				foreach($files as $value){
					if(file_exists($attachmentFolder.'/'.$value)){
						$uploadedFiles[] = $value;
					}				
				}
				
				$data = array(
					'comment_id' => $posts['comment_id'],
					'files' => implode(',', $uploadedFiles),
				);
				
				echo $commentAttachmentsTable->saveAttachment($data);
			}
			
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function downloadattachmentAction(){
		try{
			$request = $this->getRequest();
			//if($request->isPost()){
			
				$posts = $request->getPost();
				$data = $posts->toArray();
				$comment_id = $this->params('comment_id');
				$file = $this->params('file');
				$realPath = $_SERVER['DOCUMENT_ROOT'].'/comment_attachments/'.$comment_id.'/'.$file; // Relative to the root for local
				//$realPath = $_SERVER['DOCUMENT_ROOT'].'/public/comment_attachments/'.$comment_id.'/'.$file; // Relative to the root for live (diamond.openseed.com.au)
				
				$tasksHistoryTable = $this->getServiceLocator()->get('Task\Model\TasksHistoryTable');				
				$commentDetails = $tasksHistoryTable->fetchCommentDetails($comment_id);
				
				$files = explode(',', $commentDetails['files']);
				if(in_array($file, $files) && file_exists($realPath)){
					$ext = strtolower(end(explode('.', $file)));
					
					
					
					/*$request->setRawHeader ( "Content-Type: application/vnd.ms-excel; charset=UTF-8" )->setRawHeader ( "Content-Disposition: attachment; filename=Adhoc_Customers_Prospects.xls" )->setRawHeader ( "Content-Transfer-Encoding: binary" )->setRawHeader ( "Expires: 0" )->setRawHeader ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" )->setRawHeader ( "Pragma: public" )->setRawHeader ( "Content-Length: " . filesize ( $filename ) )->sendResponse ();*/
					
					$mimeTypes = array(
						'pdf' => 'application/pdf',
						'txt' => 'text/plain',
						'html' => 'text/html',
						//'exe' => 'application/octet-stream',
						'zip' => 'application/zip',
						'doc' => 'application/msword',
						'xls' => 'application/vnd.ms-excel',
						'ppt' => 'application/vnd.ms-powerpoint',
						'gif' => 'image/gif',
						'png' => 'image/png',
						'jpeg' => 'image/jpg',
						'jpg' => 'image/jpg',
						//'php' => 'text/plain'
					);
					
					header('Content-Description: File Transfer');
					header('Content-Type: '.$mimeTypes[$ext]);
					header('Content-Disposition: attachment; filename='.basename($realPath));
					header('Expires: 0');
					header('Cache-Control: must-revalidate');
					header('Pragma: public');
					header('Content-Length: ' . filesize($realPath));					
					
					readfile ( $realPath );
				}
			//}
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function changetaskstatusAction(){
		try{
		
			$request = $this->getRequest();
			if($request->isPost()){
				$request = $this->getRequest();
				$posts = $request->getPost()->toArray();
				
				$tasksTable = $this->getServiceLocator()->get('Task\Model\TasksTable');
				
				$data = array();
				
				$identity = $this->getServiceLocator()->get('AuthService')->getIdentity();
				$data['updated_by'] = $identity['user_id'];
				$data['updated_date'] = date('Y-m-d H:i:s');
				
				echo $tasksTable->changeStatus($posts['task_id'], $data);
			}
			
			exit;
			
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deletetaskAction(){
		try{
		
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$task_id = $posts['task_id'];
				$entity_id = $posts['entity_id'];
				$assigned_for = $posts['assigned_for'];				
				
				$tasksTable = $this->getServiceLocator()->get('Task\Model\TasksTable');				
				
				$historyTable = $this->getServiceLocator()->get('Task\Model\TasksHistoryTable');
				$history = $historyTable->fetchTaskHistoryIds($task_id);
				
				if(!empty($history)){
					$attachmentsTable = $this->getServiceLocator()->get('Task\Model\CommentAttachmentsTable');
				
					$historyTable->deleteTaskHistory($task_id);
					$attachmentsTable->deleteAttachments($history);
				}
				$isdeleted = $tasksTable->deleteTask($task_id);
				
				$countOpenedTasks = $tasksTable->getTasksCount($entity_id, $assigned_for, 1);
				$countClosedTasks = $tasksTable->getTasksCount($entity_id, $assigned_for, 2);
				
				echo json_encode(array('taskIsdeleted' => $isdeleted, 'countOpenedTasks' => $countOpenedTasks, 'countClosedTasks' => $countClosedTasks));
			}
			
			exit;
			
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function editcommentAction(){
		try{
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$comment_id = $posts['comment_id'];
				$comment = $posts['comment'];
				
				$historyTable = $this->getServiceLocator()->get('Task\Model\TasksHistoryTable');
				echo $historyTable->editComment($comment_id, $comment);
			}
			exit;
			
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function deletecommentAction(){
		try{
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost()->toArray();
				$comment_id = $posts['comment_id'];
				
				$historyTable = $this->getServiceLocator()->get('Task\Model\TasksHistoryTable');
				echo $historyTable->deleteComment($comment_id);
			}
			exit;
			
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}
