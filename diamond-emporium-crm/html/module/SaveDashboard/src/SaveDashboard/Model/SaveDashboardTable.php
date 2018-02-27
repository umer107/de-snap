<?php
namespace SaveDashboard\Model;

use Zend\Db\TableGateway\TableGateway;


class SaveDashboardTable
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

   
        
//***************************************CoreFunction******************************************//
 

    // Insert | Update Data
      // Insert | Update Data
    public function saveDashboard($data , $where = null)
    {
        try
        {
        
       $leadId = $data['lead_id'];
       unset($data['lead_id']);
       if(empty($leadId))
        {
          $data['lead_status'] = 'Open'; 
          unset($data['booking_room']);
          $select = new \Zend\Db\Sql\Select();
			$select->from('de_userdetail')->columns(array('id'));
          
          $select = new \Zend\Db\Sql\Select();
			$select->from(array('l' => 'de_userdetail'))
				   ->columns(array('id'));	
          
        $booking_date = $data['booking_date'];
        $booking_time = $data['booking_time'];
        $booking_timezone = $data['booking_timezone'];
        
        
         /*if(!empty($booking_date && $booking_time && $booking_timezone))              
             {*/
	    $select->where(array('l.booking_date = ?' => $booking_date ,'l.booking_time = ?' => $booking_time ,'l.booking_timezone = ?' => $booking_timezone ));
	     /*}*/
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
        }
                 
          //Insert
                 return $this->tableGateway->insert($data);
                 
        }
          else {
  
          
          
          unset($data['booking_room']);
          $select = new \Zend\Db\Sql\Select();
	  $select->from('de_userdetail')->columns(array('id'));
          
          $select = new \Zend\Db\Sql\Select();
			$select->from(array('l' => 'de_userdetail'))
				   ->columns(array('id','booking_date','booking_time','booking_timezone'));	
          
          $booking_date = $data['booking_date'];
          $booking_time = $data['booking_time'];
          $booking_timezone = $data['booking_timezone'];
        
        
         /*if(!empty($booking_date && $booking_time && $booking_timezone))              
             {*/
	 $select->where(array('l.booking_date = ?' => $booking_date ,'l.booking_time = ?' => $booking_time ,'l.booking_timezone = ?' => $booking_timezone ));
	     /*}*/
         $exec_data = $this->executeQuery($select);
         $data_booking = $exec_data->toArray();
         $counter = count($exec_data);
         
         $data_bookingDate = $data_booking[0]['booking_date'];
         $data_bookingTime = $data_booking[0]['booking_time'];
         $data_bookingTimeZone = $data_booking[0]['booking_timezone'];
         
         if($data_bookingDate ==  $booking_date  && $data_bookingTime == $booking_time && $data_bookingTimeZone == $booking_timezone  )
         {
          
             if($where)
		  return $this->tableGateway->update($data, $where);
		else
		 return $this->tableGateway->update($data, array('id' => $leadId));
             
         }
        else {
            
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
        }
              
	        if($where)
		  return $this->tableGateway->update($data, $where);
		else
		 return $this->tableGateway->update($data, array('id' => $leadId));
            
        }
             
         
			
                
             }
				   		              
          //return $this->tableGateway->insert($data);

          
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
     
    

   
//***************************************CoreFunction*****************************************//    

}