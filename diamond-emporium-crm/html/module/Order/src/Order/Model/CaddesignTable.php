<?php
/**
 *	This class is the main model for order module
 */

namespace Order\Model;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

class CaddesignTable
{
	protected $dbAdapter;
	protected $resultSetPrototype;
	protected $config;
	
	public function __construct(TableGateway $tableGateway, $serviceManager)
	{
		$this->tableGateway = $tableGateway;
		$this->serviceManager = $serviceManager;
	}
	
	/**
	 * Store CAD Design data in database
	 */
	public function saveCADdesign($data){
		try{
			if($data['steps_completed'] == 1){
				$data_changes = $this->tableGateway->insert($data);
				if($data_changes){
					$this->updateJobPacket(1, $data['steps_completed'], $data['job_id'], $data['milestone_id']);
					return $data_changes;
				}
			} else {
				$data_changes = $this->tableGateway->update($data, array('milestone_id' => $data['milestone_id']));
				if($data_changes){
					$this->updateJobPacket(1, $data['steps_completed'], $data['job_id'], $data['milestone_id']);
					return $data_changes;
				}
			}
			
			/*$cad_attachment['image'] = '';
			$stepOneAttachmentList = '';
			$stepTwoImageList = '';
			if($stage > 1){
				unset($data['job_id']);
			}
			if($stage == 1){
				$stepOneAttachmentList = $data['multipleattachmentsHidden'];
				unset($data['multipleattachmentsHidden']);
			}
			if($stage == 2){
				$stepTwoImageList = $data['multipleimagesHidden'];
				unset($data['multipleimagesHidden']);
			}
			if($stage == 3){
				$stepThreeImageList = $data['cad_client_review_image_data'];
				unset($data['multipleimagesHidden']);
			}
			
			if(!empty($id)){
				$this->tableGateway->update($data, array('id' => $id));
				if($stage == 3){
					$jobMilestone = $this->fetchJobPacketMilestone($uJobId);
					if($jobMilestone->milestones_completed != ''){
						$job_packet['milestones_completed'] = $jobMilestone->milestones_completed.',1';
					} else {
						$job_packet['milestones_completed'] = 1;
					}
					$tableJobPackets = new TableGateway('de_job_packet', $this->tableGateway->getAdapter());
					$tableJobPackets->update($job_packet, array('id' => $uJobId));
				}
				
				if(!empty($stepTwoImageList)){
					$tableCADGalleryAttchments = new TableGateway('de_milestone_images', $this->tableGateway->getAdapter());
					$gallery_attachment['milestone_type'] = 1;
					$gallery_attachment['step'] = $stage;
					$gallery_attachment['milestones_ref_id'] = $id;
					
					$imgTitlesArray = json_decode($stepTwoImageList);
					if(count($imgTitlesArray) > 0){
						foreach($imgTitlesArray as $imgKey => $imgName){
							$gallery_attachment['image'] = $imgName;
							if($this->checkMediaFileByName($gallery_attachment['image']) == 0){
								$tableCADGalleryAttchments->insert($gallery_attachment);
							}
						}
					}
					
				}
				
				if(!empty($cad_attachment['image'])){
					$tableCADAttchments = new TableGateway('de_milestone_images', $this->tableGateway->getAdapter());
					$cad_attachment['milestone_type'] = 1;
					$cad_attachment['step'] = $stage;
					$cad_attachment['milestones_ref_id'] = $id;
					if($this->checkMediaFiles($cad_attachment['milestone_type'], $cad_attachment['step'], $cad_attachment['milestones_ref_id']) > 0){
						$tableCADAttchments->update($cad_attachment, array('milestone_type' => $cad_attachment['milestone_type'], 'step' => $cad_attachment['step'], 'milestones_ref_id' => $cad_attachment['milestones_ref_id']));
					} else {
						$tableCADAttchments->insert($cad_attachment);
					}
				}
				
				return $id;
			} else {
				if($this->tableGateway->insert($data)){
					$id = $this->tableGateway->lastInsertValue;
					if(!empty($stepOneAttachmentList)){
						$tableCADGalleryAttchments = new TableGateway('de_milestone_images', $this->tableGateway->getAdapter());
						$gallery_attachment['milestone_type'] = 1;
						$gallery_attachment['step'] = $stage;
						$gallery_attachment['milestones_ref_id'] = $id;
						
						$imgTitlesArray = json_decode($stepOneAttachmentList);
						if(count($imgTitlesArray) > 0){
							foreach($imgTitlesArray as $imgKey => $imgName){
								$gallery_attachment['image'] = $imgName;
								if($this->checkMediaFileByName($gallery_attachment['image']) == 0){
									$tableCADGalleryAttchments->insert($gallery_attachment);
								}
							}
						}
						
					}
					return $id;
				}
			}*/
		}catch(\Exception $e){echo $e->getMessage ();
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Retreving CAD Design Data by Job ID
	 */
	public function getCaddesignDataByJobId($id){
		$select = new \Zend\Db\Sql\Select();
		$supplier_name = new \Zend\Db\Sql\Expression(
				'CONCAT(sup.first_name, \' \', sup.last_name)'
			);
		$select->from(array('cad' => 'de_milestone_cad'))
			   ->columns(array('id', 'job_id', 'supplier_id', 'priority', 'exp_delivery_date', 'description', 'dropbox_link', 'job_type', 'step1_emailed_on', 'stp2_delivery_date', 'step3_emailed_on', 'steps_completed', 'created_date', 'created_by'))
			   ->join(array('sup' => 'de_suppliers'), 'cad.supplier_id = sup.id', array('supplier_name' => $supplier_name), 'left')
			   ->where(array('cad.job_id = ?' => $id));
		$adapter = $this->tableGateway->getAdapter();				   
		$statement = $adapter->createStatement();
		$select->prepareStatement($adapter, $statement);
		
		$result = new \Zend\Db\ResultSet\ResultSet();
		$result->initialize($statement->execute());
		return $result->current();
	}
	
	/**
	 * Checking Milestone Media files
	 */
	 
	public function checkMediaFiles($milestoneType, $milestoneStep, $milestonesRefId){
		$select = new \Zend\Db\Sql\Select();
		$select->from(array('dmi' => 'de_milestone_images'))
			   ->columns(array('id'))
			   ->where(array('dmi.milestone_type = ?' => $milestoneType))
			   ->where(array('dmi.step = ?' => $milestoneStep))
			   ->where(array('dmi.milestones_ref_id = ?' => $milestonesRefId));
		$adapter = $this->tableGateway->getAdapter();				   
		$statement = $adapter->createStatement();
		$select->prepareStatement($adapter, $statement);
		
		$result = new \Zend\Db\ResultSet\ResultSet();
		$result->initialize($statement->execute());
		return $result->count();
	}
	
	/**
	 * Checking Job Packet Milestone Completed Status
	 */
	function fetchJobPacketMilestone($job_id){
		try{
			$select = new \Zend\Db\Sql\Select();
						
			$select->from(array('job' => 'de_job_packet'))
				   ->columns(array('milestones_completed'))
				   ->where(array('job.id = ?' => $job_id));
			//echo $select->getSqlString();exit;
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			return $result->current();
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Get Media Files for CAD Milestone
	 */
	function fetchCADMediaFiles($job_id){
		try{
			$select = new \Zend\Db\Sql\Select();
						
			$select->from(array('cad' => 'de_milestone_cad'))
				   //->columns(array('job_id', 'supplier_id'))
				   ->join(array('img' => 'de_milestone_images'), 'cad.milestone_id = img.milestones_ref_id', array('id', 'milestones_ref_id', 'step', 'image'), 'left')
				   //->where(array('img.milestone_type = ?' => 1))
				   ->where(array('cad.job_id = ?' => $job_id));
			//echo $select->getSqlString();exit;
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			
			return $result->toArray();
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Remove Media Image From DB
	 */
	function removeCADMediaFile($id){
		try{
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_milestone_images', $dbAdapter, null, $resultSetPrototype);
			$tableGateway->delete(array('id' => $id));
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/**
	 * Checking Media file unique name
	 */
	 
	public function checkMediaFileByName($fileName){
		$select = new \Zend\Db\Sql\Select();
		$select->from(array('dmi' => 'de_milestone_images'))
			   ->columns(array('id'))
			   ->where(array('dmi.image = ?' => $fileName));
		$adapter = $this->tableGateway->getAdapter();				   
		$statement = $adapter->createStatement();
		$select->prepareStatement($adapter, $statement);
		
		$result = new \Zend\Db\ResultSet\ResultSet();
		$result->initialize($statement->execute());
		return $result->count();
	}
	
	 /**
	  * Delete milestone
	  * $milestone_id
	  */
	 public function deleteMilestone($milestone_id){
	 	try{
			$this->tableGateway->delete( array('milestone_id' => $milestone_id));
			
			$dbAdapter = $this->tableGateway->getAdapter();
			$resultSetPrototype = new \Zend\Db\ResultSet\HydratingResultSet();
			
			$tableGateway = new \Zend\Db\TableGateway\TableGateway('de_job_to_milestone', $dbAdapter, null, $resultSetPrototype);
			return $tableGateway->delete(array('id' => $milestone_id));
			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	 /**
	  * Update jobpacket
	  * current_milestone_id, current_milestone_step_id
	  */
	 public function updateJobPacket($current_milestone_id, $current_milestone_step_id, $job_id, $milestone_ref_id){
	 	try{
			$data['milestone_id'] = $milestone_ref_id;
			$data['current_milestone_id'] = $current_milestone_id;
			$data['current_milestone_step_id'] = $current_milestone_step_id;
			$de_job_packet = new TableGateway('de_job_packet', $this->tableGateway->getAdapter());
			$returnData = $de_job_packet->update($data, array('id' => $job_id));
			//Updating Milestone Complete Status
			if(($current_milestone_id == 1 && $current_milestone_step_id == 3) || ($current_milestone_id == 2 && $current_milestone_step_id == 2) || ($current_milestone_id == 3 && $current_milestone_step_id == 2) || ($current_milestone_id == 4 && $current_milestone_step_id == 3)){
				$statusData['status'] = 1;
				$de_job_to_milestone = new TableGateway('de_job_to_milestone', $this->tableGateway->getAdapter());
				$de_job_to_milestone->update($statusData, array('id' => $milestone_ref_id));
			}
			
			return $returnData;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }	 
	 
	 /**
	  * Fetch Jobs By Order Id
	  */
	 public function getJobsByOrderId($order_id){
	 	try{
			$select = new \Zend\Db\Sql\Select();
						
			$select->from(array('job_packet' => 'de_job_packet'))
				   ->columns(array('id'))
				   ->where(array('job_packet.order_id = ?' => $order_id));
			//echo $select->getSqlString();exit;
			$adapter = $this->tableGateway->getAdapter();				   
			$statement = $adapter->createStatement();
			$select->prepareStatement($adapter, $statement);
			
			$result = new \Zend\Db\ResultSet\ResultSet();
			$result->initialize($statement->execute());
			$jobList = array();
			foreach($result->toArray() as $resData){
				$jobList[] = $resData['id'];
			}
			return $jobList;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
	 
	 /**
	  * Update milestone
	  * $data = array
	  * $where = array
	  */
	 public function updateMilestone($data, $where){
	 	try{
			return $this->tableGateway->update($data, $where);			
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	 }
}