<?php
namespace Appointment\Model;

use Zend\Db\TableGateway\TableGateway;


class AppointmentTable
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
	
	/*public function fetchAll()
	{
		try{
			
			$select = new \Zend\Db\Sql\Select();
			$select->from('de_leads')->columns(array('lead_id'));
			
			//FullName  From Table "de_users"
			$fullname = new \Zend\Db\Sql\Expression(
				'CONCAT(u.first_name, \' \', u.last_name)'
			);
                        //Image  From Table "de_users"
                        $image = new \Zend\Db\Sql\Expression(
				'u.image'
			);
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('l' => 'de_leads'))
				   ->columns(array(
				   		'lead_id', 'title', 'first_name', 'last_name', 'email', 'mobile', 'budget', 'priority', 'created_date','lead_owner'				   		
				   ))				   
				   ->join(array('u' => 'de_users'), 'l.lead_owner = u.user_id', array('lead_owner_fullname' => $fullname , 'lead_owner_image' => $image ), 'left');

			$data = $this->executeQuery($select);
			//$result['TotalRows'] = count($counter);
			//$result['Rows'] = $data->toArray();
                        $result = $data->toArray();
			
                        //ArrayGrouped BasedOnId
                      $groups = array();
                   foreach ($result as $item) {
                       $key = $item['lead_owner'];
                       if (!isset($groups[$key])) {
                           $groups[$key] = array(
                          'items' => array($item),
                          'count' => 1,
                             );
                       } else {
                        $groups[$key]['items'][] = $item;
                        $groups[$key]['count'] += 1;
                       }
                      }
                        //EndArrayGrouped Based on Id
                        
			return $groups;
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}*/

        
       
        //******************************************DashboardCode****************************************************//

        // Insert | Update Data
        public function saveDashboard($data , $where = null)
        {
        try
        {
          /*Product Short Code*/            
            $product_name = explode(" ", $data['product']);           
            $lastname_product = array_pop($product_name);
            $firstname_product = implode(" ", $product_name);
            $first_letter = $firstname_product[0];
            $last_letter = $lastname_product[0];
            $product_shortcode = $first_letter.$last_letter;
            $data['product_shortcode'] = $product_shortcode;
          /*Product Short Code*/
            
          /*AssignUsUserShortCode*/
            $assigni_name = explode(" ", $data['assign_to']);
            $total_assigni_count = count($assigni_name);
            if($total_assigni_count >= 1 && $total_assigni_count <  2)
            {
               $firstname_assigni = $assigni_name[0];
               $first_letter_assigni_name = $firstname_assigni[0];
               $assign_shortcode = $first_letter_assigni_name;
               $data['assignto_shortcode'] = $first_letter_assigni_name;
               
            }
            else if($total_assigni_count >=1  && $total_assigni_count < 3)
            {
               $firstname_assigni = $assigni_name[0];
               $lastname_assigni =  $assigni_name[1];
               $first_letter_assigni_name = $firstname_assigni[0];
               $last_letter_assigni_name =  $lastname_assigni[0];
               $assign_shortcode = $first_letter_assigni_name.$last_letter_assigni_name;
                $data['assignto_shortcode'] = $assign_shortcode;
               
            }
            else if($total_assigni_count >=1  && $total_assigni_count < 4)
            {
               $firstname_assigni = $assigni_name[0];
               $middle_assigni =  $assigni_name[1];
               $lastname_assigni =  $assigni_name[2];
               $first_letter_assigni_name = $firstname_assigni[0];
               $middle_assigni_letter_assigni_name =  $middle_assigni[0];
               $last_letter_assigni_name = $lastname_assigni[0];
               $assign_shortcode = $first_letter_assigni_name.$middle_assigni_letter_assigni_name.$last_letter_assigni_name;
               $data['assignto_shortcode'] = $assign_shortcode;
            }
          /*AssignUsUserShortCode*/
            $AssignInUserId = $data['assign_id'];
            unset($data['assign_id']);
            $data['assign_to_UserId'] = $AssignInUserId;
	    $data['create_date'] = date('Y-m-d H:i:s');     
            $leadId = $data['lead_id'];
            unset($data['lead_id']);
            if(empty($leadId) || $data['AppointmentType'] == 1)
             {
              $data['lead_status'] = 'Open';
           
              if(empty($data['booking_room']))
             {
               if(empty($data['booking_date']))
               {
                   unset($data['booking_room']);
                   $data['booking_date'] = date('Y-m-d'); 
                   $data['user_booking_date'] = 1;
               }
             }
           else
           {
               $data['user_booking_date'] = 0;
           }
          
           
            
           
           
           //unset($data['booking_room']);
           
           /*$select = new \Zend\Db\Sql\Select();
	   $select->from('de_userdetail')->columns(array('id'));
          
           $select = new \Zend\Db\Sql\Select();
	   $select->from(array('l' => 'de_userdetail'))->columns(array('id'));
				   	
           
           $booking_date = $data['booking_date'];
           
           $booking_time = $data['booking_time'];
           $booking_timezone = $data['booking_timezone'];
           $select->where(array('l.booking_date = ?' => $booking_date ,'l.booking_time = ?' => $booking_time ,'l.booking_timezone = ?' => $booking_timezone ));
           $exec_data = $this->executeQuery($select);
           $counter = count($exec_data);
	   
	   if($counter == 0)
              {
             //TODO HANDLER booking_room  set to 1
             $data['booking_room'] = 1;
              }
           else if($counter == 1)
              {
             //TODO HANDLER booking_room set to 2
             $data['booking_room'] = 2;
              }
           else if($counter == 2)
             {
             //TODO HANDLER booking_room set to 3
             $data['booking_room'] = 3;
             }
          else
             {
            //TODO HANDLER    return 0 or exit;
            return " Room Full Pleae Find Another Nearby Hotel";
             }*/
         
           //return $this->tableGateway->insert($data);
           $this->tableGateway->insert($data);
           $insertedId = $this->tableGateway ->getLastInsertValue();
           return $insertedId;
   
           }       
        
          else {
              
             if(empty($data['booking_room']))
             {
               if(empty($data['booking_date']))
               {
                   unset($data['booking_room']);
                   $data['booking_date'] = date('Y-m-d'); 
                   $data['user_booking_date'] = 1;
               }
             }
            else{
               $data['user_booking_date'] = 0;
             } 
             if($where){
                        
                   //return $this->tableGateway->update($data, $where);
                    $this->tableGateway->update($data, $where);
                    return 0;
             } 
             else{
                  //return $this->tableGateway->update($data, array('id' => $leadId));
                    $this->tableGateway->update($data, array('id' => $leadId));
                    return 0;
             }
         }
         
        }
     	 catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
    
       //Update Lead Status
       public function updateLeadStatus($data, $where = null)
       {
     	try{
			$lead_id = $data['lead_id'];
			unset($data['lead_id']);
			if (empty($lead_id)) {
			
                            //Lead Not Exist
                            
			} else {
				if($where)
					return $this->tableGateway->update($data, $where);
				else
					return $this->tableGateway->update($data, array('id' => $lead_id));
			}
     	}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
     }
     
     
        public function saveLead($data , $where = null)
        {
        try
        {
           if(empty($data['lead_id']))
           {     
                 unset($data['lead_id']);
                 //Check Lead Already Exists ot Not
                 $select = new \Zend\Db\Sql\Select();
		 $select->from(array('u' => 'de_userdetail'))
			->columns(array('id','email'));
                 $select->where(array('u.email = ?' =>  $data['email']));
                 $exec_data = $this->executeQuery($select);
                 $result = $exec_data->toArray();
                 $counter = count($exec_data);
                 if($counter > 0)
                 {
                     foreach ($result as $items)
                     {
                         
                         return $items['id'];
                          
                     }
                 }
                 else
                 {
                     $this->tableGateway->insert($data);
                     $insertedId = $this->tableGateway ->getLastInsertValue();
                     return $insertedId;
                     
                 }
               
            }
            else
            {
               
               
            }
          
   
         
         }
     	 catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
    
       public function saveAppointments($data , $where = null)
        {
        try
        {
          /*Product Short Code*/            
            $product_name = explode(" ", $data['product']);           
            $lastname_product = array_pop($product_name);
            $firstname_product = implode(" ", $product_name);
            $first_letter = $firstname_product[0];
            $last_letter = $lastname_product[0];
            $product_shortcode = $first_letter.$last_letter;
            $data['product_shortcode'] = $product_shortcode;
          /*Product Short Code*/
            
          /*AssignUsUserShortCode*/
            $assigni_name = explode(" ", $data['assign_to']);
            $total_assigni_count = count($assigni_name);
            if($total_assigni_count >= 1 && $total_assigni_count <  2)
            {
               $firstname_assigni = $assigni_name[0];
               $first_letter_assigni_name = $firstname_assigni[0];
               $assign_shortcode = $first_letter_assigni_name;
               $data['assignto_shortcode'] = $first_letter_assigni_name;
               
            }
            else if($total_assigni_count >=1  && $total_assigni_count < 3)
            {
               $firstname_assigni = $assigni_name[0];
               $lastname_assigni =  $assigni_name[1];
               $first_letter_assigni_name = $firstname_assigni[0];
               $last_letter_assigni_name =  $lastname_assigni[0];
               $assign_shortcode = $first_letter_assigni_name.$last_letter_assigni_name;
                $data['assignto_shortcode'] = $assign_shortcode;
               
            }
            else if($total_assigni_count >=1  && $total_assigni_count < 4)
            {
               $firstname_assigni = $assigni_name[0];
               $middle_assigni =  $assigni_name[1];
               $lastname_assigni =  $assigni_name[2];
               $first_letter_assigni_name = $firstname_assigni[0];
               $middle_assigni_letter_assigni_name =  $middle_assigni[0];
               $last_letter_assigni_name = $lastname_assigni[0];
               $assign_shortcode = $first_letter_assigni_name.$middle_assigni_letter_assigni_name.$last_letter_assigni_name;
               $data['assignto_shortcode'] = $assign_shortcode;
            }
          /*AssignUsUserShortCode*/
            $AssignInUserId = $data['assign_id'];
            unset($data['assign_id']);
            $data['assign_to_UserId'] = $AssignInUserId;
	          $data['create_date'] = date('Y-m-d H:i:s');     
            $leadId = $data['lead_id'];
            //unset($data['lead_id']);
          
             if(!empty($leadId) && empty($data['appointment_id']))
             {
               unset($data['appointment_id']);
               $data['lead_status'] = 'Open';
           
              if(empty($data['booking_room']))
               {
               if(empty($data['booking_date']))
               {
                   unset($data['booking_room']);
                   $data['booking_date'] = date('Y-m-d'); 
                   $data['user_booking_date'] = 1;
               }
               }
           else
           {
               $data['user_booking_date'] = 0;
           }
           
           $data['isFirstBooked'] = 0; 
           $this->tableGateway->insert($data);
           $insertedId = $this->tableGateway ->getLastInsertValue();
           return $insertedId;
   
           }       
        
          else {
              
            if(!empty($leadId))
            {
                   $select = new \Zend\Db\Sql\Select();
                   $select->from(array('u' => 'de_appointments'))
                    ->columns(array('appointment_id','lead_id'));
                   $select->where(array('u.lead_id = ?' =>  $leadId));
                    $exec_data = $this->executeQuery($select);
                    $counter = count($exec_data);
                    $result = $exec_data->toArray();
                    if($counter == 1)
                    {
                    
                        $data['isFirstBooked'] = 0; 
                      }

                    }
                    else
                    {
                         $data['isFirstBooked'] = 1; 
                    }
           
             $app = $data['appointment_id'];
             if(empty($data['booking_room']))
             {
               if(empty($data['booking_date']))
               {
                   unset($data['booking_room']);
                   $data['booking_date'] = date('Y-m-d'); 
                   $data['user_booking_date'] = 1;
               }
             }
            else{
               $data['user_booking_date'] = 0;
             } 
             if($where){
                        
                   //return $this->tableGateway->update($data, $where);
                    $this->tableGateway->update($data, $where);
                    return 0;
             } 
             else{
                  //return $this->tableGateway->update($data, array('id' => $leadId));
                    $this->tableGateway->update($data, array('appointment_id' => $app));
                    return 0;
             }
                
            }
         }
         
        
     	 catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }

   
//***************************************CoreFunction*****************************************//    

}