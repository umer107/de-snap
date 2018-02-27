<?php
/**
 * Notes Controller
 */

namespace Notes\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
	public function ajaxgetnotesAction(){
    	try{
			$config = $this->getServiceLocator()->get('Config');
    		$params = $this->getRequest()->getQuery()->toArray();
			$typeName = $this->params('type');
			$typeId = $this->params('id');
			$related = $params['related'];
			$pagenum = $params['pagenum'];
			$limit = $params['pagesize'];
			settype($limit, 'int');
			$offset = $pagenum * $limit;
			$notesTable = $this->getServiceLocator()->get('Notes\Model\NotesTable');
			
			/*
			 * If type is opportunity we will also display notes for the lead.
			 * Go find that if necessary.
			 */
			$leadId = 0;
			$opportunityId = 0;
			if ($typeName == 'opportunity') {
				$opportunityId = $typeId;
				$opportunitiesTable = $this->getServiceLocator()->get('Opportunities\Model\OpportunitiesTable');
				$opportunity = $opportunitiesTable->fetchOpportunityDetails($typeId);
				if (is_array($opportunity) && $opportunity[0]['lead_id']) {
					$leadId = $opportunity[0]['lead_id'];
				}
				if ($related) {
					$opportunityId = $opportunitiesTable->fetchOpportunityIdsForLead($leadId, array($opportunityId));
					/* Blank out lead because we don't want those notes */
					$leadId = 0;
				}
			} else {
				$leadId = $typeId;
			}
			
			$notesArr = $notesTable->fetchNotes($limit, $offset, $leadId, $opportunityId);
			foreach($notesArr as $key => $value){
				foreach($value as $field => $fieldValue){
					if($field == 'created_date'){
						$notesArr[$key]['created_date'] = date($config['phpDateFormat'], strtotime($notesArr[$key]['created_date']));
					}
					if($field == 'follow_up_date'){
						$notesArr[$key]['follow_up_date'] = date($config['phpDateFormat'], strtotime($notesArr[$key]['follow_up_date']));
					}
				}
			}
			echo json_encode($notesArr);
			exit;
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}    	
    }
	
	public function notesAction()
    {
		try{
			$sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			$notesTable = $this->getServiceLocator()->get('Notes\Model\NotesTable');
			$request = $this->getRequest();
			if($request->isPost()){
				$posts = $request->getPost();
				$data = $posts->toArray();
				$finalData = array();
				if(!empty($data['noteId'])){
					$noteDetails = $notesTable->getNoteDetails($data['noteId']);
					echo json_encode($noteDetails);
					exit;
				} else if(!empty($data['noteUpdateId'])){
					if($data['follow_up_date'] != ''){
						$dateOldFormat = explode("/", $data['follow_up_date']);
						$dateNewFormat = $dateOldFormat[2].'-'.$dateOldFormat[1].'-'.$dateOldFormat[0].' 00:00:00';
						$finalData['follow_up_date'] = $dateNewFormat;
					}
					if($data['note_type'] != ''){
						$finalData['note_type'] = $data['note_type'];
					}
					if($data['note_description'] != ''){
						$finalData['note_description'] = $data['note_description'];
					}
					if($data['type'] != ''){
						$finalData['grid_type'] = $data['type'];
					}
					if($data['typeId'] != ''){
						$finalData['grid_type_id'] = $data['typeId'];
					}
					$finalData['modified_by'] = $identity['user_id'];
					$finalData['modified_date'] = date('Y-m-d H:i:s');
					echo $notesTable->saveNotes($finalData, $data['noteUpdateId']);
					exit;
				} else if(!empty($data['deleteNote'])){
					echo $notesTable->deleteNotes($data['deleteNote']);
					exit;
				} else {
					if($data['follow_up_date'] != ''){
						$dateOldFormat = explode("/", $data['follow_up_date']);
						$dateNewFormat = $dateOldFormat[2].'-'.$dateOldFormat[1].'-'.$dateOldFormat[0].' 00:00:00';
						$finalData['follow_up_date'] = $dateNewFormat;
					}
					$finalData['note_type'] = $data['note_type'];
					$finalData['note_description'] = $data['note_description'];
					$finalData['grid_type'] = $data['type'];
					$finalData['grid_type_id'] = $data['typeId'];
					$finalData['created_by'] = $identity['user_id'];
					echo $notesTable->saveNotes($finalData);
					exit;
				}
				
			}
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
}
