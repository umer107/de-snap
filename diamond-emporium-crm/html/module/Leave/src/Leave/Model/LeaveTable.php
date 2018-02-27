<?php

/**
 * Created by NetBeans.
 * User: MuhammadUmarWaheed
 * Date: 10-30-2017
 * Time: 08:10 AM
 */

namespace Leave\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Form\Element\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class LeaveTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    //SortDateTimeArray
    function compare_func($a, $b)
    {
    // CONVERT $a AND $b to DATE AND TIME using strtotime() function
    $t1 = strtotime($a["end_time"]);
    $t2 = strtotime($b["end_time"]);

    return ($t2 - $t1);
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
        
        
    
        
   public function  fetchUserDetailForLeave($filter = null)
   {
            try{
            
                        //FullName  From Table "de_users"
			$fullname = new \Zend\Db\Sql\Expression(
				'CONCAT(u.first_name, \' \', u.last_name)'
			);
                        //Image  From Table "de_users"
                        $image = new \Zend\Db\Sql\Expression(
				'u.image'
			);
			$select = new \Zend\Db\Sql\Select();
			$select->from(array('u' => 'de_users'))
				   ->columns(array(
				   		'user_id' , 'UserName' => $fullname , 'image'  ));
			
                        $select->order('user_id Desc ');
			$data = $this->executeQuery($select); 
                        $result = $data->toArray();
                        
                        
                        return $result;
                 
          }catch (\Exception $ex) {
                \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
            }
       
   }
        
   public function  saveLeave($data)
   {
       
        try{
            $leave_reason = $data['Reason'];
            $data['Leave_Reason'] = $leave_reason;
            unset($data['Reason']);
             $date = $data['Leave_StartDate'];
             $end_date = $data['Leave_EndDate'];
             
             unset($data['Leave_StartDate']);
             unset($data['Leave_EndDate']);
             
             //Check Leave Already Assign To User           
                 
                 $select = new \Zend\Db\Sql\Select();
		 $select->from(array('u' => 'de_leaves'))
			->columns(array('Leave_id','Leave_StartDate','Leave_EndDate','Leave_AssignUserName','Leave_UserId','Leave_Reason'));
			
                 if(!empty($date)&&!empty($end_date))
                  {
                    $select->where->between('u.Leave_StartDate', $date, $end_date);
                  }
                  if(!empty($data['Leave_UserId']))
                  {
                      $select->where(array('u.Leave_UserId = ?' =>  $data['Leave_UserId']));
                  }
                 $exec_data = $this->executeQuery($select);
                 $counter = count($exec_data);
                 
                 if($counter > 0)
                 {
                     return "Please Select Different Date  You hava alrady ";
                 }
                     
                    
	  
             
             
             //SaveLeave
             while (strtotime($date) <= strtotime($end_date)) {
                 
                     $data['Leave_StartDate'] = $date;
                     $this->tableGateway->insert($data);
                     unset($data['Leave_StartDate']);
                     $date = date ("Y-m-d", strtotime("+1 day", strtotime($date))); 
	     }
         
             return "Sucess";
          }catch (\Exception $ex) {
                \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
            }
       
       
   }
   
   
   public function getUserLeaves($data)
   {
       try {
       
          $date = $data['date'];
          $range_Month = array();
          if(!empty($date))
          {
              //MonthRange
              $range_Month = $this->rangeMonth($date);
              
              //Start and  Last date of month
              $start_date = reset($range_Month);
              $end_date = end($range_Month);
             
              //Iterate Month Array
               $range_Month = $this->rangeMonth($date);
              
              //Start and  Last date of month
              $start_date_array = reset($range_Month);
              $end_date_array = end($range_Month);
              $date_array = array();
              while (strtotime($start_date_array) <= strtotime($end_date)) {                  
                 
                array_push($date_array, $start_date_array);
                
              $start_date_array = date ("Y-m-d", strtotime("+1 day", strtotime($start_date_array)));
               
                
	     }
             //Group Array Same Value
             $sorted_month_array = array();
         
               foreach ($date_array as $data) {
                $id = $data;
                if (isset($sorted_month_array[$id])) {
                 $sorted_month_array[$id][] = $data;                 
              } else {
                $sorted_month_array[$id] = array($data);                
              }
            }
             //
             //Iterate Month Array End
              
            //Get-Data-From-Leaves-Table
              $select = new \Zend\Db\Sql\Select();
			$select->from(array('u' => 'de_leaves'))
				   ->columns(array(
				   		'Leave_id','Leave_StartDate','Leave_EndDate','Leave_AssignUserName','Leave_UserId','Leave_Reason'));
            //Check Month Start Date and End Date is Not Empty            
            if(!empty($start_date) && !empty($end_date))
            {
                 $select->where->between('u.Leave_StartDate', $start_date, $end_date);
            }           
            //Execute Raw Query
            $data = $this->executeQuery($select);
            //Convert Result into array
            $result = $data->toArray();
            
            //Group by Start Date
            $sorted_leave_data = array();
         
               foreach ($result as $data) {
                $id = $data['Leave_StartDate'];
                if (isset($sorted_leave_data[$id])) {
                 $sorted_leave_data[$id][] = $data;
              } else {
                $sorted_leave_data[$id] = array($data);
              }
            }
            
            
            
            //$return_result = array();
            $return_result = array_merge($sorted_month_array , $sorted_leave_data );
        
            
            //Checking Array is Corect or not
            //$json_encode_array = json_encode($return_result);
            $array_re = array();
            $array_re = array_values($return_result);
            
            return $array_re;
          }
           
       } catch (\Exception $ex) {
                \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
       }
   }
        

   

   //****************************************Dashboard**********************************************************//
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

        //
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




public function fetchLeadRecord($filter= null)
       {
           
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
              'id','first_name', 'last_name', 'phone_number', 'email','Street','State','City','Zip', 'product', 'referral','special_instructions','budget','reference_product', 'contact_method', 'assign_to','assign_to_UserId','reson_skip_next_in_line','lead_status','specify_requirements','lead_status','lead_owner','create_date','lead_close_date','booking_date','booking_time','booking_timezone','booking_room','booking_duration'           
           ));
                        
                      
                        $value = $filter['leadId'];    
                        if(!empty($filter['leadId'])) {
        $select->where(array('l.id = ?' =>  $value));
      }
                        
                        $data = $this->executeQuery($select);                     
                        
                         $result = $data->toArray();
                         
                         return $result;
                        
       }


  

 

 //**********************Dashboard**********************************************************//
   


         
      
}


