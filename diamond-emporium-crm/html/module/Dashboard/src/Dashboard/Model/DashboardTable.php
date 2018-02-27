<?php

/**
 * Created by Netbeans
 * User: MuhammadUmarWaheed
 */

namespace Dashboard\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Form\Element\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;


class DashboardTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    //Date of Day of Given Month
    function getTotalDatesArray($year, $month, $day){
        $date_ar=array();
        $from = $year."-".$month."-01";
        $t=date("t",strtotime($from));

        for($i=1; $i<=$t; $i++){
          
           $getDay =   strtolower(date("l",strtotime($year."-".$month."-".$i)));
           $getDayUpper = ucfirst(strtolower($getDay));
           if($getDayUpper == $day){
            $j= $i>9 ? $i: "0".$i;
             $date_ar[]=$year."-".$month."-".$j;
          }
      }
      return $date_ar;
 }
   //Month Range when enter start-date and end-date give whole month date
    function getDatesFromRange($first, $last, $step = '+1 day', $output_format = 'Y-m-d' ) {

    $dates = array();
    $current = strtotime($first);
    $last = strtotime($last);

    while( $current <= $last ) {

        $dates[] = date($output_format, $current);
        $current = strtotime($step, $current);
    }

    return $dates;
}
    //Month Range only give start and end date of month
    public  function rangeMonth($datestr) {
    date_default_timezone_set(date_default_timezone_get());
    $dt = strtotime($datestr);
    $res['start'] = date('Y-m-d', strtotime('first day of this month', $dt));
    $res['end'] = date('Y-m-d', strtotime('last day of this month', $dt));
    return $res;
    }
    public function executeQuery($select){
		$adapter = $this->tableGateway->getAdapter();
		$statement = $adapter->createStatement();
		$select->prepareStatement($adapter, $statement);
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet;
	}

    public function fetchAll()
    {
       //Dashboard
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    //GetUserBasedOnBudget
    public function  fetchUserByBudget( $filter = null)
    {
        try {
            
            $fullname = new \Zend\Db\Sql\Expression(
				'CONCAT(u.first_name, \' \', u.last_name)'
			);
            $lead_owner = new \Zend\Db\Sql\Expression(
				'l.lead_owner'
			);          
            $select = new \Zend\Db\Sql\Select();
			$select->from(array('u' => 'de_users'))
				   ->columns(array(
				   		'user_id','image','user_name' => $fullname, 'user_budget'				   		
				   ))				  

				   ->join(array('l' => 'de_userdetail'), 'u.user_id = l.lead_owner', array("lead_owner" => $lead_owner), 'left');
                        
               	
               if(!empty($filter['budget'])) {

				
                   $select->where(array('u.user_budget = ?' => $filter['budget']));
			
                   
               }
            
               $data = $this->executeQuery($select);
               $result = $data->toArray();
                   
                   //Group BY
                   $groups = array();
                   foreach ($result as $item) {
                       $key = $item['user_id'];
                    
                        $groups[$key]['id'] = $key;
                        $groups[$key]['items'] = $item;
                        $groups[$key]['count'] += 1;
                      
                      }
                        
                     
                        
                        return $groups;
                        
        }catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
    // Get dashboard theo Id
    public function getDashboard($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    // Insert | Update Data
    public function saveDashboard($data)
    {
        try
        {
         
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
             
				   			   		
				   		              
          return $this->tableGateway->insert($data);

          
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
     
     //FETCH TEAM STATUS
     public function  fetchTeamStatus($filter = null)
     {
        try {
           $date = $filter['today_date'];
          
          $select = new \Zend\Db\Sql\Select();
	  $select->from('de_users')->columns(array('user_id'));
            
          $fullname = new \Zend\Db\Sql\Expression(
				'CONCAT(u.first_name, \' \', u.last_name)'
			);
          
          $LeaveStatusUsersTable  = 'no';
          $LeaveStatusLeaveTable = 'yes';
         //---------------------------------------------------------GetUsers-----------------------------------------------------------------//
          $select = new \Zend\Db\Sql\Select();
			$select->from(array('u' => 'de_users'))
				   ->columns(array(
				   		'user_id','UserFullName' => $fullname, 'status' ,'image' , 'user_status'	
				   ));			   

         $data = $this->executeQuery($select);               
         $result = $data->toArray();
         
       
         /*Get User Data Group by*/
          $result_array = array();
                
                foreach ($result as $data_array) {
                     $id = $data_array['user_id'];
                     $data_array['LeaveStatus'] = 'no';
                     $data_array['Leave_StartDate'] = null;
                     if (isset($result_array[$id])) {
                      
                      $result_array[$id][] = $data_array;
                      } else {
                          
                  $result_array[$id] = array($data_array);
                      }
                   }
         
         
         //---------------------------------------------------------GetLeaves-----------------------------------------------------------------//
          $select_leaves = new \Zend\Db\Sql\Select();
	  $select_leaves->from('de_leaves')->columns(array('Leave_id'));
          $select_leaves = new \Zend\Db\Sql\Select();
			$select_leaves->from(array('l' => 'de_leaves'))
				   ->columns(array(
				   		'user_id' =>'Leave_UserId' , 'Leave_StartDate', 'status' => 'Leave_reason',  'UserFullName' => 'Leave_AssignUserName' 
				   ));

                        
         
         if(!empty($date))
         {             
             $select_leaves->where(array('l.Leave_StartDate = ?' =>  $date));
         }
                        
                        
         $data_leaves = $this->executeQuery($select_leaves);               
         $result_leave = $data_leaves->toArray();
        
         /*Get User Data Group by*/
           $result__leave_array = array();
                
                foreach ($result_leave as $data_leave_array) {
                     $id = $data_leave_array['user_id'];
                       $data_leave_array['LeaveStatus'] = 'yes';
                     if (isset($result__leave_array[$id])) {
                      
                      $result__leave_array[$id][] = $data_leave_array;
                      } else {
                          
                  $result__leave_array[$id] = array($data_leave_array);
                      }
                   }            
                   
     
                   
                   $return_array_result =  $result__leave_array + $result_array; 
                   
                   
                   return $return_array_result;
           
        }catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
     }

     // Delete Data
    public function deleteDashboard($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
    
    //GetLeadsData
            public function  fetchRecordByBudgetId($filter = null)
        {
            try {
                
                	$select = new \Zend\Db\Sql\Select();
			$select->from('de_userdetail')->columns(array('id'));
                        
                        
                        //FullName  From Table "de_users"
			$fullname = new \Zend\Db\Sql\Expression(
				'CONCAT(u.first_name, \' \', u.last_name)'
			);
                        //Image  From Table "de_users"
                        $image = new \Zend\Db\Sql\Expression(
				'u.image'
			);
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('l' => 'de_userdetail'))
				   ->columns(array(
				   		'id','first_name', 'last_name', 'phone_number', 'email', 'product', 'referral','special_instructions','budget','reference_product', 'contact_method', 'assign_to','reson_skip_next_in_line','lead_status','lead_owner','create_date','booking_date'		   		
				   ))				   
				   ->join(array('u' => 'de_users'), 'l.lead_owner = u.user_id', array('lead_owner_fullname' => $fullname , 'lead_owner_image' => $image ), 'left');
                        //Start-Filter-Parameter-From-User
                         $value = $filter['budget'];
                         $lead_status = $filter['lead_status'];
                         $referral = $filter['referral'];
                         $booking_date = $filter['booking_date'];
                         
                         if($filter['budget'] == 'all')
                         {
                             $filter['budget'] = '';
                         }
                         if($filter['lead_status'] == 'All')
                         {
                             $filter['lead_status'] = '';
                         }
                         if($filter['referral'] == 'All')
                         {
                             $filter['referral'] = '';
                         }
                        //End-Filter-Parameter-From-User
                        if(!empty($filter['budget'])) {
				$select->where(array('l.budget = ?' =>  $value));
			}
                        if(!empty($filter['lead_status'])) {
				$select->where(array('l.lead_status = ?' =>  $lead_status));
			}
                        if(!empty($filter['referral'])) {
				$select->where(array('l.referral = ?' =>  $referral));
			}                       
                        //Start Working With Calender
                         if($filter['booking_date'] == 'All')
                         {
                             $filter['booking_date'] = 'This month';
                             
                         if($filter['booking_date'] == 'This month')
                         {
                           $start_date = date('Y-m-01',strtotime('this month'));
                           $end_date = date('Y-m-t',strtotime('this month'));
                          
                           if(!empty($filter['booking_date'])) {
                               
                           $select->where->between('l.booking_date', $start_date, $end_date);
                           
                           }
                         }
                         else if(!empty($filter['booking_date'])) {
                                  
				$select->where(array('l.booking_date = ?' =>  $booking_date));
			          
                                
                              }
                         }
                         else if($filter['booking_date'] == 'Today')
                         {
                             $today = strtotime("today");
                             $today_booking_date = date('Y-m-d', $today);
                             $filter['booking_date'] = $today_booking_date;
                             if(!empty($filter['booking_date'])) {
                                  
				$select->where(array('l.booking_date = ?' =>  $filter['booking_date']));
			          
                                
                              }
                         }
                         else if($filter['booking_date'] == 'Yesterday')
                         {
                             $yesterday = strtotime("yesterday");
                             $yesterday_booking_date = date('Y-m-d', $yesterday);
                             $filter['booking_date'] = $yesterday_booking_date;
                             if(!empty($filter['booking_date'])) {
                                  
				$select->where(array('l.booking_date = ?' =>  $filter['booking_date']));
			          
                                
                              }
                         }
                         else if($filter['booking_date'] == 'This week')
                         {
                           $start_date = date("Y-m-d",strtotime('monday this week'));
                           $end_date = date("Y-m-d",strtotime("sunday this week"));
                          
                           if(!empty($filter['booking_date'])) {
                           $select->where->between('l.booking_date', $start_date, $end_date);
                           }
                           
                          
                         }
                         else if($filter['booking_date'] == 'This month')
                         {
                           $start_date = date('Y-m-01',strtotime('this month'));
                           $end_date = date('Y-m-t',strtotime('this month'));
                          
                           if(!empty($filter['booking_date'])) {
                               
                           $select->where->between('l.booking_date', $start_date, $end_date);
                           
                           
                           }
                           
                          
                         }
                          else if($filter['booking_date'] == 'This year')
                         {
                          
                          $start_date = date('Y-01-01');
                          $end_date = date('Y-12-31');
                        if(!empty($filter['booking_date'])) {
                               
                           $select->where->between('l.booking_date', $start_date, $end_date);
                           
                           
                           }
                           
                          
                         }
                         else {
                             
                             $array=explode(",",$booking_date); 
                             $start_date1 = reset($array);
                             $start_date = date('Y-m-d', strtotime( $start_date1 ));
                             $end_date1 = end($array);
                             $end_date = date('Y-m-d', strtotime( $end_date1 ));
                             if(!empty($filter['booking_date'])) {
                                 
                             $select->where->between('l.booking_date', $start_date, $end_date);
                             
                             
                             }
                         }
                         //End Working With Calender
                         
                       
                         
                        //Start Sorting
                         $select->order('id Desc');
                        //End Sorting
                         
			$data = $this->executeQuery($select);
                        
                        
                         $result = $data->toArray();
                         
                         
                 //ArrayGrouped BasedOnId
                     $groups = array();
                     foreach ($result as $item) {

                         
                         $key = $item['assign_to'];
                         $keyName = $item['lead_owner_fullname'];
                         $keyImage = $item['lead_owner_image'];
                         $groups[$key]['idOfUser'] = $key;
                         $groups[$key]['agentName'] = $keyName;
                         if($keyImage == null)
                         {
                            $groups[$key]['agentImage'] = 'empty';
                         }
                         else
                         {
                            $groups[$key]['agentImage'] = $keyImage;
                         }
                         

                         if (!isset($groups[$key])) 
                         {
                             $groups[$key] = array(
                              'items' => array($item),
                              'count' => 1,
                             );
                         } 
                         else 
                         {

                          $groups[$key]['items'][] = $item;
                          $groups[$key]['count'] += 1;

                         }

                      }
                        //EndArrayGrouped Based on Id

			return $groups;
                         
            } catch (\Exception $ex) {
                \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
            }
            
        }
        
        //GetCalenderData
        public function  fetchCalenderData($filter = null)
        {
            
             try {
                
                 $select = new \Zend\Db\Sql\Select();
		 $select->from('de_userdetail')->columns(array('id'));
                 
                 //Time of Booking
                 $title = new \Zend\Db\Sql\Expression(
				'l.booking_time'
			);
                 //Start Date booking
                 $booking_start_date = new \Zend\Db\Sql\Expression(
				'l.booking_date'
			);
                 
                 $select = new \Zend\Db\Sql\Select();
			$select->from(array('l' => 'de_userdetail'))
				   ->columns(array(
				   		'id','title' => $title ,'start' => $booking_start_date 	   		
				   ));				   
		//Get Month Range
                
                //End Month Range
                $lead_owner = $filter['lead_owner'];
                 //Filter Based on Lead owner
                 if(!empty($filter['lead_owner'])) {
				$select->where(array('l.lead_owner = ?' =>  $lead_owner));
			}                 
                 if(!empty($filter['booking_date'])) {
                     
                    $booked_date = $filter['booking_date']; 
                    $month_range = $this->rangeMonth($booked_date); 
                    
                    $start_date = reset($month_range);
                    $end_date = end($month_range);  
                    
                    if($start_date != null && $end_date != null)
                    {
                         $select->where->between('l.booking_date', $start_date, $end_date);
                    }
                   
		   
                    
			}   
                //Sort-Data   
                $select->order('booking_date Desc ');
                //End-Sort-Data        
                $data = $this->executeQuery($select);    
                 
                $result1 = $data->toArray();
                //result Come from database Grouped
                $result = array();
                
                foreach ($result1 as $data) {
                     $id = $data['start'];
                     $day = substr($data['start'],8);
                     if (isset($result['Day'.$day])) {
                      
                      $result['Day'.$day][] = $data;
                      } else {
                          $result['Day'.$day][] = $day;  
                  $result['Day'.$day] = array($data);
                      }
                   }
                
               
                //Month Data Grouped
                $dates = $this->getDatesFromRange('2017-10-01', '2017-10-31');
                foreach($dates as $key=>$value){
                    $day = substr($value,8);
                     //$out[$value] = $value;
                     $out['Day'.$day] = $value;
                  }
                 $arr= $out;   
                 // two array merged
                $return_result = array();
                $return_result =  array_merge_recursive($arr,$result);
                //return result 
                return $return_result;
                 
            } catch (\Exception $ex) {
                \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
            }
        }
        //End Get Calender Data
        
        /*
         ****************************************** 
         ***** QuestionViewCalenderService Start***
         ****************************************** 
        */
       public function fetchQuestionViewCalenderData($filter= null)
       {
           try {
               
               $select = new \Zend\Db\Sql\Select();
		 $select->from('de_userdetail')->columns(array('id'));
                 
                 
                 
                 
                
                 
                   $booking_start_date = new \Zend\Db\Sql\Expression('l.booking_date');
		   $booking_time = new \Zend\Db\Sql\Expression('l.booking_time');
                   
                    $select->from(array('l' => 'de_userdetail'))
				   ->columns(array(
				   		'id' , 'title' => $booking_time , 'start' => $booking_start_date , 'lead_owner'
				   ));
                             

                 //Get Values From $filter Parameter
                 $booking_date = $filter['booking_date'];
                 $booking_day = $filter['day'];
                 $dateElements = explode('-', $booking_date);
                 //Get Month and Year From Date
                 $booking_year = $dateElements[0];
                 $booking_Month = $dateElements[1];
                 
                 //Add this  Parameter to Function Get Day Date Of Given Month
                 //For-example : if the day is saturday Function return the date of saturday of current month
                 $DayOfMonth = array();
                 $DayOfMonth= $this->getTotalDatesArray($booking_year, $booking_Month ,$booking_day);
               
                 if($DayOfMonth != null)
                 {
                     
                     $select->where->in('l.booking_date', $DayOfMonth);
                 }
              
                
                 $assign_to = $filter['assigneeId'];
                
                 
                 $booking_timezone = $filter['booking_timezone'];
                 if(!empty($booking_timezone))
                 {
                    $select->where(array('l.booking_timezone = ?' =>  $booking_timezone));
                 }
                 
                 $data = $this->executeQuery($select);    
		 $result = $data->toArray();	
                 
                 $groups = array();
 
                          foreach ($result as $item) {

                      
                         $key = $item['start'];


                         if (!isset($groups[$key])) 
                         {
                             $groups[$key] = array(
                              'items' => array($item),
                              'count' => 1,
                             );
                         } 
                         else 
                         {

                          $groups[$key]['items'][] = $item;
                          $groups[$key]['count'] += 1;

                         }

                      }
            $output = array();
                      foreach ($groups as $arr)
                      {
                           
                          foreach ($arr['items'] as $value)
                          {
                               if(!isset($output[$value['start']][$value['title']]))
                              {

                               $output[$value['start']]['date'] =  $value['start'];
                               $output[$value['start']]['status'] =  $value['status'];                                 
                               
                               $output[$value['start']][$value['title']] = array(
                                
                                  
                                  'title' => $value['title'],
                                  'start' => $value['start'],
                                  'lead_owner' => $value['lead_owner'],
                                   'status' => $value['status'],
                                  'count' => 1,
                                  'class' => 'one'      
                              );
                                 
                              }
                           else {
                              $output[$value['start']][$value['title']]['count'] +=1;
                              if($output[$value['start']][$value['title']]['count'] == 1)
                              {
                                  $output[$value['start']][$value['title']]['class'] = 'one';
                              }
                              else if($output[$value['start']][$value['title']]['count'] == 2)
                              {
                                  $output[$value['start']][$value['title']]['class'] = 'two';
                              }
                              else if($output[$value['start']][$value['title']]['count'] == 3)
                              {
                                  $output[$value['start']][$value['title']]['class'] = 'three';
                              }
                             }
                              
                             
                            
                          }
                      }
            
                      
                      //---------------------------------------------------Start Annual Leave Api----------------------------------------------------------------------
                        
                        $select_leaves = new \Zend\Db\Sql\Select();
	                $select_leaves->from('de_leaves')->columns(array('Leave_id'));
                        $select_leaves = new \Zend\Db\Sql\Select();
			$select_leaves->from(array('d' => 'de_leaves'))
				   ->columns(array(
				   		'Leave_id' , 'Leave_StartDate' , 'Leave_EndDate' , 'status' => 'Leave_reason',  'Leave_UserId'
				   ));

                      $day_Of_month = $DayOfMonth;
                      
                      if(!empty($day_Of_month))
                      {
                           $select_leaves->where->in('d.Leave_StartDate',$day_Of_month);
                      }
                      
                      if(!empty($filter['assigneeId']))
                      {
                         $select_leaves->where(array('d.Leave_UserId = ?' => $filter['assigneeId']));                          
                      }
                      
                      $select_leaves->order('Leave_id Desc');
                      $data_leave = $this->executeQuery($select_leaves);    
		      $result_leave = $data_leave->toArray();	
                      
                       
                      
                      //Group According To Start Date
                      $group_leave  = array();
 
                          foreach ($result_leave as $item){

                      
                         $key = $item['Leave_StartDate'];


                         if (!isset($group_leave[$key])) 
                         {
                             $group_leave[$key] = array(
                              'items' => array($item),
                              'count' => 1,
                             );
                         } 
                         else 
                         {

                          $group_leave[$key]['items'][] = $item;
                          $group_leave[$key]['count'] += 1;

                         }

                      }
                      
                      //Leaves and Leads Array Merge
                     
                      //------------------------------------------------------Start LeaveTempleteDesignSet----------------------------------------------------------------------//
                      
                      $output_temp = array();
                      foreach ($group_leave as $key => $fdata)
                      {                      
                          foreach ($fdata['items'] as $fvalue)
                          {
                              if(!isset($output_temp[$fvalue['Leave_StartDate']]))
                              {

                               $output_temp[$fvalue['Leave_StartDate']] = array(
                                
                                  'date' => $fvalue['Leave_StartDate'],                                  
                                  'status' => $fvalue['status'],
                                  'count' => 1,                                     
                              );
                                 
                              }
                            else {
                              $output_temp[$fvalue['Leave_StartDate']]['count'] +=1;
                              if($output_temp[$fvalue['Leave_StartDate']]['count'] == 1)
                              {
                                  $output_temp[$fvalue['Leave_StartDate']]['class'] = 'one';
                              }
                              else if($output_temp[$fvalue['Leave_StartDate']]['count'] == 2)
                              {
                                  $output_temp[$fvalue['Leave_StartDate']]['class'] = 'two';
                              }
                              else if($output_temp[$fvalue['Leave_StartDate']]['count'] == 3)
                              {                                  
                                  $output_temp[$fvalue['Leave_StartDate']]['class'] = 'Three';
                              }
                             }
                          }
                         
                      }
                      
                      
                      
                      //---------------------------------------------------------End   LeaveTempleteDesignSet----------------------------------------------------------------------//
                      
                     
                     
                        $return_array_result =  $output_temp + $output;
                      
                    
                      
                      
                       //---------------------------------------------------End  Annual Leave Api----------------------------------------------------------------------
                      
                      
                   
                      
                   
                      $resOutput = array();
                      array_push($resOutput,$DayOfMonth);
                      $retArray = array_merge($return_array_result, $resOutput);
                      
                      return $retArray;
               
               
               
           } catch (Exception $e) {
               
               \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
           }
       }
         
      
}

