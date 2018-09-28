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
             $date = $data['startDate'];
             $end_date = $data['endDate'];
             
             unset($data['startDate']);
             unset($data['endDate']);
             
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
                  $fullname = new \Zend\Db\Sql\Expression('CONCAT(u.first_name, \' \', u.last_name)');
        
      
                       
                  $select = new \Zend\Db\Sql\Select();
                  $select->from(array('l' => 'de_userdetail'))
                         ->columns(array('id','title','gender','first_name', 'last_name', 'phone_number', 'email', 'country','communication_method','user_booking_date','product', 'referral','special_instructions','budget','reference_product', 'contact_method','lead_owner_fullname' => 'lead_owner_name', 'assign_to','reson_skip_next_in_line','lead_status','lead_owner','create_date','booking_date'))          
                         ->join(array('u' => 'de_users'), 'l.assign_to_UserId = u.user_id', array('lead_owner_image' => 'image' ), 'left');
                  
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
                     $start_budget = 0;
                     $end_budget = 0;
                    if($filter['budget'] == '$2,000 - $4,999'){                                                  
                       $select->where(array('l.budget = ?' =>  $filter['budget']));
                      }
                    else if($filter['budget'] == '$5,000 - $9,999')
                      {
                        $select->where(array('l.budget = ?' =>  $filter['budget']));
                      }
                      else if($filter['budget'] == '$10,000 - $19,999')
                       {
                         $select->where(array('l.budget = ?' =>  $filter['budget']));
                       }
                            else if($filter['budget'] == '$20,000 - $34,999')
                            {
                               $select->where(array('l.budget = ?' =>  $filter['budget']));
                            }
                            else if($filter['budget'] == '$35,000 - $49,999')
                            {
                              $select->where(array('l.budget = ?' =>  $filter['budget'])); 
                            }
                            else if($filter['budget'] == '$50,000 - $74,999')
                            {
                              $select->where(array('l.budget = ?' =>  $filter['budget']));
                            }
                            else if($filter['budget'] == '$75,000 - $99,999')
                            {
                              $select->where(array('l.budget = ?' =>  $filter['budget']));
                            }    
                            else if($filter['budget'] == '$100,000+')
                            {
                              $select->where(array('l.budget = ?' =>  $filter['budget']));
                            }
                            //$select->where(array('l.budget = ?' =>  $value));
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
                         
                          $filter_lead_Appointment = 0;
                          $select->where(array('l.AppointmentType = ?' =>  $filter_lead_Appointment));
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

        //fetchCountriesList
        public function fetchCountriesList($filter = null)
        {            
              try {                
                 $select = new \Zend\Db\Sql\Select();
                 $select->from('de_countries')->columns(array('country_id'));         
                 
                 $select = new \Zend\Db\Sql\Select();
                 $select->from(array('c' => 'de_countries'))
                         ->columns(array('country_id','country_name'));        
                 $select->order('country_id Asc ');               
                 //End-Sort-Data        
                 $data = $this->executeQuery($select);   
                 $result = $data->toArray();
                 return $result;               
                 
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
        
       //Start -- CustomViewCalenderBusinessLogic
       //---Start
       public function fetchCustomViewcalender($filter = null)
       {
           try
           {
                $select = new \Zend\Db\Sql\Select();
                $select->from('de_userdetail')->columns(array('id'));
                $select = new \Zend\Db\Sql\Select();
                $select->from(array('l' => 'de_userdetail'))
                      ->columns(array(
                'id','title' , 'gender' , 'first_name' , 'last_name' , 'phone_number' , 'email' , 'country' ,'State' ,'full_address', 'communication_method' , 'product' , 'product_shortcode' , 'referral' , 'only_referral' , 'special_instructions' , 'budget' , 'reference_product' , 'contact_method' , 'assign_to' , 'assignto_shortcode' , 'assign_to_UserId' , 'reson_skip_next_in_line' , 'specify_requirements' , 'lead_status' , 'lead_owner' , 'lead_owner_name' , 'create_date' , 'lead_close_date' , 'booking_date' , 'booking_time' , 'booking_room' , 'user_booking_date' ,'color' , 'durationTime' ,'bookingstart'  
                ))
                ->join(array('u' => 'de_users'), 'l.assign_to_UserId = u.user_id', array('booking_color' => 'color'), 'left');    
              
               
               //Filter The Data Of Current Week
               if(!empty($filter['booking_date']))
               {                  
                   $date = $filter['booking_date'];
                   $start_date =  date("Y-m-d", strtotime('monday this week', strtotime($date)));  
                   $end_date =  date("Y-m-d", strtotime('sunday this week', strtotime($date)));
                   $select->where->between('l.booking_date', $start_date, $end_date);
               }
               //Filter Data Based On  Assign_Us_UserId
               /*if(!empty($filter['assign_UserId']))
               {
                   $lead_assigni = $filter['assign_UserId'];
                   $select->where(array('l.assign_to_UserId = ?' =>  $lead_assigni));
               }*/
               //Filter the Data Based on Budget
               /*if(!empty($filter['budget']))
               {
                   $lead_assgni_budget = $filter['budget'];
                   $select->where(array('l.budget = ?' =>  $lead_assgni_budget));
               }*/
               $data = $this->executeQuery($select);                     
               $result = $data->toArray();
               
              
               /****Group-Data Start****/
                $groups = array();
                
                foreach ($result as $item) {
                       
                    $key = $item['booking_date'];
                    
                    
                    foreach ($result as $items)
                    {

                        foreach ($result as $item1)
                        {
                        if(empty($item1['booking_color']))
                        {
                            $item1['booking_color'] = "D3D3D3";
                        }
                         $booking_time_key = $item1['booking_time'];                         
                         $booking_room_key = $item1['booking_room'];
                         //Empty Data Not Push into Array   
                          if(!empty($key))
                          {
                              if(!empty($booking_time_key))
                              {
                                  if(!empty($booking_room_key))
                                  {
                                     
                                        //$groups[$key][$key1][$item1['booking_room']] = $item1; 
                                      $groups[$key][$booking_time_key][$booking_room_key] = $item1; 
                                  }
                              }
                          }
                          //Empty Data Not Push into Array
                                
                            //$groups[$key][$key1][$item1['booking_room']] = $item1; 
                        }
                        
                        //return $groups;

                    }
                    
                    
                    
                    //$groups[$key]['items'] = $item;
                    //$groups[$key]['count'] += 1;
                    //-------------------------------------------------------//

                    
                    
                    
                   
                      
                }
            
             /*foreach ($result as $item)
             {
                 $groups[$item['booking_date']][$item['booking_time']][$item['booking_room']] = $item;
             }*/
           
             //-------------------------------------------------------------------------------------//
             //-----------------------------Start Template Area-------------------------------------//
             /*Set The Array Of Whole Weeks*/ 
             $week_array = array();
             $current_date = $filter['booking_date'];
             $begin_date =  date("Y-m-d", strtotime('monday this week', strtotime($current_date)));  
             $end_date =  date("Y-m-d", strtotime('sunday this week', strtotime($current_date)));
             $num_days = floor((strtotime($end_date)-strtotime($begin_date))/(60*60*24));           
             for ($i=0; $i<= $num_days; $i++)
             {
                 $week_array[] = date('Y-m-d', strtotime($begin_date . "+ $i days")); 
             }
             //return $week_array;
             
             /*Set The Array Of Whole Weeks*/  
             
             /*Set The Array of Time*/
             $calendar_time = array();
             $calendar_time[0] = '8-9';//8-9
             $calendar_time[1] = '9-10';//9-10
             $calendar_time[2] = '10-11';//10-11
             $calendar_time[3] = '11-12';//11-12
             $calendar_time[4] = '12-1';//12-1
             $calendar_time[5] = '1-2';//1-2
             $calendar_time[6] = '2-3';//2-3
             $calendar_time[7] = '3-4';//3-4
             $calendar_time[8] = '4-5';//4-5
             $calendar_time[9] = '5-6';//4-5
             //return $calendar_time;
             /*Set The Array of Time*/
             
             /*Set The room of time*/
             $calendar_room = array();
             $calendar_room[0] = '1';
             $calendar_room[1] = '2';
             $calendar_room[2] = '3';
             $calendar_room[3] = '4';
             /*Set The room of time*/
             $template_array = array();
             foreach($week_array as $week_range)
             {
                 foreach($calendar_time as $cal_time)
                 {
                     foreach ($calendar_room as $cal_room)
                     {
                         $template_array[$week_range][$cal_time][$cal_room] = "";
                     }
                 }
             }

             //return $template_array;
            //-------------------------------------------------------------------------------------//
            //-------------------------------End Template Area-------------------------------------//
               /****Group-Data End****/
             
             /*Merge Two Array*/             
             //$result_set =  array_merge_recursive($template_array,$groups);
             $result_set =  array_merge_recursive($groups,$template_array);
             
             
             //$result_set = array_merge($template_array,$template_array);
             
             //array_merge_recursive
             //$result_set = array_merge($groups,$template_array);
             
             
             /******************************CustomMergeArray**************************************/
             
             //foreach ($groups as $item_group)
             /*foreach ($groups as $item_group)
             {
                 foreach ($item_group as $item_group1)
                 {
                     foreach ($item_group1 as $item_group2)
                     {
                        $booking_date_add = $item_group2['booking_date'];
                        $booking_time_add = $item_group2['booking_time'];
                        $booking_room_add = $item_group2['booking_room'];
                
                        $template_array[$booking_date_add][$booking_time_add][$booking_room_add] = $item_group2;
                        
                     }
                 }
               
             }*/
             foreach ($result as $item_group)
             {

               $booking_date_add = $item_group['booking_date'];
               $booking_time_add = $item_group['booking_time'];
               $booking_room_add = $item_group['booking_room'];
               $user_tekp = $item_group['id'];
               $template_array[$booking_date_add][$booking_time_add][$booking_room_add][$user_tekp]= $item_group;
                       

             }
             
    
             
             /******************************CustomMergeArray************************************/
             
              /******************************CustomAddLeaveStart************************************/
             
             
             
                $select_leave = new \Zend\Db\Sql\Select();
                $select_leave->from('de_leaves')->columns(array('Leave_id'));
                $select_leave = new \Zend\Db\Sql\Select();
                $select_leave->from(array('l' => 'de_leaves'))
                      ->columns(array(
                'Leave_id','Leave_StartDate' , 'Leave_AssignUserName' , 'Leave_UserId' , 'Leave_Reason'
                ));
                
               //Filter Data Based On  Assign_Us_UserId
               if($filter['assign_UserId'] != null)
               {

                if(!empty($filter['assign_UserId']))
                  {
                   $lead_assigni_leave = $filter['assign_UserId'];                   
                   $select_leave->where(array('l.Leave_UserId = ?' =>  $lead_assigni_leave));
                  }

               }       
               
                
               //Filter The Data Of Current Week
               if(!empty($filter['booking_date']))
               {                  
                   $date_booking = $filter['booking_date'];
                   $start_date_leave =  date("Y-m-d", strtotime('monday this week', strtotime($date_booking)));  
                   $end_date_leave =  date("Y-m-d", strtotime('sunday this week', strtotime($date_booking)));
                   $select_leave->where->between('l.Leave_StartDate', $start_date_leave, $end_date_leave);
               }
               $data_leave = $this->executeQuery($select_leave);                     
               $result_leave = $data_leave->toArray();

               if($filter['assign_UserId'] != null)
               {
                 foreach($result_leave as $items_leave)
                 {
                     
                     $booking_date_add_leave = $items_leave['Leave_StartDate'];
                     $leave_user_id = $items_leave['Leave_UserId'];
                     $template_array[$booking_date_add_leave]['100'] = $items_leave;
                 }
               }
               
              /******************************CustomAddLeaveEnd************************************/
             
             
             
             //return $result_set;
             return $template_array;
             
             //return $groups;
           } catch (\Exception $ex) {
                \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
            }
           
       }
       //---End
       //End -- CustomViewCalenderBusinessLogic
        
        /*
         ****************************************** 
         ***** LeaveByUserId Start***
         ****************************************** 
        */
       
        public function fetchLeavesByUserId($filter= null)
        {
            
            try {
           
             //Template
             $week_array = array();
             $current_date = $filter['booking_date'];
             $begin_date =  date("Y-m-d", strtotime('monday this week', strtotime($current_date)));  
             $end_date =  date("Y-m-d", strtotime('sunday this week', strtotime($current_date)));
             $num_days = floor((strtotime($end_date)-strtotime($begin_date))/(60*60*24));           
             for ($i=0; $i<= $num_days; $i++)
             {
                 $week_array[] = date('Y-m-d', strtotime($begin_date . "+ $i days")); 
             }
             
             $template_array = array();
             foreach($week_array as $week_range){
                 
                
                $template_array[$week_range]['isOnLeave'] = 0;
                $day = date("D", strtotime($week_range));
                
                switch ($day)
                {
                    case "Mon":
                        $day = "Monday";
                        break;
                    case "Tue":
                        $day = "Tuesday";
                        break;
                    case "Wed":
                        $day = "Wednesday";
                        break;
                    case "Thu":
                        $day = "Thursday";
                        break;
                    case "Fri":
                        $day = "Friday";
                        break;
                    case "Sat":
                        $day = "Saturday";
                        break;
                    case "Sun":
                        $day = "Sunday";
                        break;
                    default :
                        $day = "SomeIssueOccured";
                } 
                
                
                $template_array[$week_range]['Day'] = $day;
  
             }
           
             
             // return $template_array;
             //BusinessLogic
             $select = new \Zend\Db\Sql\Select();
             $select->from('de_leaves')->columns(array('Leave_id'));
             $select = new \Zend\Db\Sql\Select();
             $select->from(array('l' => 'de_leaves'))
                      ->columns(array(
                'Leave_id','Leave_StartDate' , 'Leave_AssignUserName' , 'Leave_UserId' , 'Leave_Reason'
             ));
                
             if(!empty($filter['assign_UserId']))
             {
                $lead_assigni_leave = $filter['assign_UserId'];                   
                $select->where(array('l.Leave_UserId = ?' =>  $lead_assigni_leave));
             }
             if(!empty($filter['booking_date']))
             {                  
                $date_booking = $filter['booking_date'];
                $start_date_leave =  date("Y-m-d", strtotime('monday this week', strtotime($date_booking)));  
                $end_date_leave =  date("Y-m-d", strtotime('sunday this week', strtotime($date_booking)));
                $select->where->between('l.Leave_StartDate', $start_date_leave, $end_date_leave);
             }
             $data = $this->executeQuery($select);                     
             $result = $data->toArray(); 
              
            foreach($result as $items_leave)
            {
                   
                   $booking_date_add_leave = $items_leave['Leave_StartDate'];
                   $leave_user_id = $items_leave['Leave_UserId'];
                   $template_array[$booking_date_add_leave]['isOnLeave'] = 1;
             }
                
            return $template_array;
                
            }catch (Exception $e) {
               
               \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
           }
            
            
        }




        /*
         ****************************************** 
         ***** LeaveByUserId Start***
         ****************************************** 
        */
        
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
       
       //FetchSales
       public function fetchListOfSalesRepo($filter = null)
       {
           
           try{
                    
               $fullname = new \Zend\Db\Sql\Expression(
                    'CONCAT(u.first_name, \' \', u.last_name)'
               );               
             
               $select = new \Zend\Db\Sql\Select();
               $select->from(array('r' => 'de_roles'))
                    ->columns(array('role_id','role_name'))
                    ->join(array('u' => 'de_users'), 'r.role_id = u.role_id', array('user_id','name' => $fullname,'image'), 'left');  
              
              $role_id = 6;
              if($role_id == 6){              
                $select->where(array('r.role_id = ?' => $role_id)); 
              }
              $data = $this->executeQuery($select);                                          
              $result = $data->toArray();
              return $result;
              
           }catch(\Exception $e){
             \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
          }
           
       }


       //FetchUserEmail
       public function fetchUserEmail($filter = null)
       {
           
        try
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
              'id','title','gender','first_name', 'last_name', 'phone_number', 'email','country', 'full_address' ,'communication_method','product_shortcode','user_booking_date','State','product', 'referral', 'only_referral' ,'special_instructions','budget','reference_product', 'contact_method', 'assign_to','assign_to_UserId','reson_skip_next_in_line','lead_status','specify_requirements','lead_status','lead_owner','create_date','lead_close_date','booking_date','booking_time','booking_room',           
               ));
              
              $select->where(array('l.email = ?' => $filter['email']));  
              $select->order("id desc");
              $data = $this->executeQuery($select);                                          
              $result = $data->toArray();
              if(empty($result))
              {
                  return 0;
              }
              
              return $result;
              
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

     
     public function  fetchCheckLeadEmail($filter= null)
     {
            try {
                
                $id = $filter['leadId'];
                $email_check = $filter['email'];
                $select = new \Zend\Db\Sql\Select();
                $select->from(array('l' => 'de_userdetail'))
                     ->columns(array(
                       'id','email'
                      ));
                $select->where(array('l.id = ?' =>  $id));
                $data = $this->executeQuery($select);               
                $result = $data->toArray();
                $result_count = count($result);
                
                $template_array = array();
                foreach($result as $items)
                 { 
                    if($items['email'] ==  $email_check)
                     {
                       $template_array[$items['id']]['response'] = 1;                           
                     }
                     else
                     {
                      $template_array[$items['id']]['response'] = 0;             
                     }
                     
                    
                 }
                return $template_array;
              
                
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
              'id','title','gender','first_name', 'last_name', 'phone_number', 'email','country', 'full_address' ,'communication_method','product_shortcode','user_booking_date','State','product', 'referral', 'only_referral' ,'special_instructions','budget','reference_product', 'contact_method', 'assign_to','assign_to_UserId','reson_skip_next_in_line','lead_status','specify_requirements','lead_status','lead_owner','create_date','lead_close_date','booking_date','booking_time','booking_room','durationTime','bookingstart','customerName','salesRepName'          
           ));
                        
                      
                        $value = $filter['leadId'];    
                        if(!empty($filter['leadId'])) {
        $select->where(array('l.id = ?' =>  $value));
      }
                        
                        $data = $this->executeQuery($select);                     
                        
                         $result = $data->toArray();
                         
                         return $result;
                        
       }

 public function fetchNextInLineUser($filter = null)     
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
                ))->join(array('l' => 'de_userdetail'), 'u.user_id = l.assign_to_UserId', array("lead_owner" => $lead_owner, 'assign_to_UserId' , 'budget'), 'left');      
          // ))->join(array('l' => 'de_userdetail'), 'u.user_id = l.lead_owner', array("lead_owner" => $lead_owner, 'assign_to_UserId'), 'left');      

      //SalesUser
      $role_id = 6; 
      
      if($role_id == 6)
      {
        $select->where(array('u.role_id = ?' => $role_id)); 
      }       
       
       $budget = $filter['budget']; 
       $start_budget = 0; 
       $end_budget = 0; 
       //Budget Greater than 2000 and Smaller than 5000 
       if($budget >= 2000 && $budget <= 4999) 
       {
           $start_budget = 2000; 
           $end_budget = 3000;            
       } 
       //Budget Greater than 5000 and Smaller than 10000 
       else if($budget >= 5000 && $budget <= 9999) 
       {
          
       } 
       //Greater than 10000 and  Smaller than 20000 
       else if($budget >= 10000 && $budget <= 19999) 
       {
       } 
       //Greater than 20000 and  Smaller than 35000 
       else if($budget >= 20000 && $budget <= 34999) 
       {
       } 
       //Greater than 35000 and  Smaller than 50000 
       else if($budget >= 35000 && $budget <= 49999) 
       {
       } 
       //Greater than 50000 and  Smaller than 75000 
       else if($budget >= 50000 && $budget <= 74999) 
       {
       } 
       //Greater than 75000 and  Smaller than 100000 
        else if($budget >= 75000 && $budget <= 99999) 
       { 
       } 

       $data = $this->executeQuery($select);
       $result = $data->toArray();
                   
       //Group BY
       $groups = array();
       foreach ($result as $item)
       {
           
       $key = $item['user_id'];                    
       $groups[$key]['assign_to_UserId'] = $key;
       $groups[$key]['items'] = $item;
       $groups[$key]['count'] += 1;
                      
       }
      
       return $groups;
       
       
                        
      }catch(\Exception $e){
      \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
    }
  
   } 
   
   
   public  function fetchUserOnLeave($filter = null)
   {
     $start_date = $filter['start_date'];
     $end_date = $filter['end_date'];
     $user_id = $filter['assign_UserId'];
     if(isset($user_id))
     {
       if(!isset($end_date))
       {
         $select_leaves = new \Zend\Db\Sql\Select();
         $select_leaves->from('de_leaves')->columns(array('Leave_id'));
         $select_leaves = new \Zend\Db\Sql\Select();
         $select_leaves->from(array('l' => 'de_leaves'))
           ->columns(array(
              'user_id' =>'Leave_UserId' , 'Leave_StartDate', 'status' => 'Leave_reason',  'UserFullName' => 'Leave_AssignUserName' 
         ));
         
         if(isset($start_date))
         {             
             $select_leaves->where(array('l.Leave_StartDate = ?' =>  $start_date));
         }
         
         $select_leaves->where(array('l.Leave_UserId = ?' =>  $user_id));
         
         $data_leaves = $this->executeQuery($select_leaves);            
         $result_leave = $data_leaves->toArray();
         
       
         $array_count = count($result_leave);
         
         if($array_count > 0)
         {
           $total_days = array();
           $num_days = floor((strtotime($start_date)-strtotime($start_date))/(60*60*24));
           for ($i=0; $i<= $num_days; $i++)
            {
             $total_days[] = date('Y-m-d', strtotime($start_date . "+ $i days"));
            }
         }
             
         $template_array = array();
         foreach($total_days as $items)
         {
          $template_array[$items]['isOnLeave'] = 1;
         }
             
         return $template_array;
     
       }
      
       
       if(isset($start_date))
       {
           
           if(isset($end_date))
           {
               
               
             $select = new \Zend\Db\Sql\Select();
             $select->from('de_leaves')->columns(array('Leave_id'));
             $select = new \Zend\Db\Sql\Select();
             $select->from(array('l' => 'de_leaves'))
                ->columns(array(
              'user_id' =>'Leave_UserId' , 'Leave_StartDate', 'status' => 'Leave_reason',  'UserFullName' => 'Leave_AssignUserName' 
             ));
         
             
             if(isset($start_date)&& isset($end_date))
             {
               $select->where->between('l.Leave_StartDate', $start_date, $end_date);
             }
             if($user_id)
             {
                $select->where(array('l.Leave_UserId = ?' =>  $user_id));                 
             }
            
             $data_leaves = $this->executeQuery($select);            
             $result_leave = $data_leaves->toArray();
               
             
             $array_count = count($result_leave);
             if($array_count > 0)
             {
                $total_days = array();
                $num_days = floor((strtotime($end_date)-strtotime($start_date))/(60*60*24));
                for ($i=0; $i<= $num_days; $i++)
                    {
                        $total_days[] = date('Y-m-d', strtotime($start_date . "+ $i days"));
                    }
             }
            
             
             $template_array = array();
             foreach($total_days as $items)
             {
                $template_array[$items]['isOnLeave'] = 1;
               
             }
             
             return $template_array;
               
           }
           
       }
       
         
         
         
     }
       
   }

   public function  fetchUserColor($filter = null)
   {
      $UserId = $filter['user_id'];
      $select = new \Zend\Db\Sql\Select(); 
      $select->from(array('u' => 'de_users')) 
           ->columns(array( 
              'color'    
           ))->where(array('u.user_id = ?' => $UserId)); 
               
      
       
       $data = $this->executeQuery($select);      
       
       $result = $data->toArray(); 
       
       
       $color = $result[0]['color'];
       
       if(empty(($color)))
       {
           $result[0]['color'] = '9b9b9b';
       }
       
       return $result;
       
       
   }

   public function  fetchCustomerData()
   {
               try{
		
                $fullname = new \Zend\Db\Sql\Expression(
                 'CONCAT(c.first_name, \' \', c.last_name)'
                );    
                $select = new \Zend\Db\Sql\Select(); 
                $select->from(array('c' => 'de_customers')) ->columns(array( 'id','user_name' => $fullname));    
                $select->order("id desc");
                $data = $this->executeQuery($select);      
                $result = $data->toArray(); 
       
                return $result;
             
           
                    
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
       
       
   }

   //
      public function  fetchCustomerNameData()
   {
               try{
		
                $fullname = new \Zend\Db\Sql\Expression(
                 'CONCAT(u.first_name, \' \', u.last_name)'
                );    
                $select = new \Zend\Db\Sql\Select(); 
                $select->from(array('u' => 'de_userdetail')) ->columns(array( 'id','user_name' => $fullname));    
                $select->order("id desc");
                $data = $this->executeQuery($select);      
                $result = $data->toArray(); 
       
                return $result;
             
           
                    
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
       
       
   }
   public function fetchUserNextInLine($filter = null)
   {
       //-----------------------------------------------UserList------------------------------------// 
      //Sales-Person-RoleId =  6  
      $role_id = 6; 
      $select = new \Zend\Db\Sql\Select(); 
      $select->from('de_users')->columns(array('user_id'));                         
                         
      //FullName  From Table "de_userdetail" 
      $fullname = new \Zend\Db\Sql\Expression( 
        'CONCAT(u.first_name, \' \', u.last_name)' 
      ); 
                
       
      $select = new \Zend\Db\Sql\Select(); 
      $select->from(array('u' => 'de_users')) 
           ->columns(array( 
              'user_id','image','user_name' => $fullname,'lead_assign_to' =>  'user_id'    
           ))->where(array('u.role_id = ?' => $role_id)); 
               
       $select->order('user_name Asc');
       
       $data = $this->executeQuery($select);      
       
       $result = $data->toArray();       
       
       $groupUser = array();
       
       foreach ($result as $item){
       $key = $item['user_id'];                    
       $groupUser[$key]['user_id'] = $key;
       $groupUser[$key]['items'] = $item;
       $groupUser[$key]['count'] = 0;
                      
       }
      
      
       //-----------------------------------------------LeadList------------------------------------// 
       $budget = $filter['budget']; 

    //Budget Greater than 2000 and Smaller than 5000 
    if($budget == '$2,000 - $4,999') 
     { 
              
       $select_detail = new \Zend\Db\Sql\Select(); 
       $select_detail->from('de_userdetail')->columns(array('id'));        
       $select_detail = new \Zend\Db\Sql\Select(); 
       $select_detail->from(array('l' => 'de_userdetail')) 
           ->columns(array( 
              //'id','first_name', 'last_name', 'phone_number', 'email','Street','State','City','Zip', 'product', 'referral','special_instructions','budget','reference_product', 'contact_method','name' => 'assign_to','assign_to_UserId','reson_skip_next_in_line','lead_status','specify_requirements','lead_status','lead_owner','create_date','lead_close_date','booking_date','booking_time','booking_timezone','booking_room','booking_duration'            
               'user_id'=> 'assign_to_UserId','user_name' => 'assign_to' , 'lead_assign_to' => 'assign_to_UserId'
           ))->join(array('u' => 'de_users'), 'l.assign_to_UserId = u.user_id', array('image'), 'left');
       $select_detail->where(array('l.budget = ?' =>  $budget));
       $data_detail = $this->executeQuery($select_detail);               
       $result_detail = $data_detail->toArray(); 
            
       $groups = array();
       
       foreach ($result_detail as $item){
       $key = $item['user_id'];                    
       $groups[$key]['user_id'] = $key;
       $groups[$key]['items'] = $item;
       $groups[$key]['count'] += 1;
                      
       }
     } 
     //Budget Greater than 5,000  and Smaller than 9,999 $5,000 - $9,999  
      if($budget == '$5,000 - $9,999') 
     { 

       $select_detail = new \Zend\Db\Sql\Select(); 
       $select_detail->from('de_userdetail')->columns(array('id'));        
       $select_detail = new \Zend\Db\Sql\Select(); 
       $select_detail->from(array('l' => 'de_userdetail')) 
           ->columns(array( 
              //'id','first_name', 'last_name', 'phone_number', 'email','Street','State','City','Zip', 'product', 'referral','special_instructions','budget','reference_product', 'contact_method','name' => 'assign_to','assign_to_UserId','reson_skip_next_in_line','lead_status','specify_requirements','lead_status','lead_owner','create_date','lead_close_date','booking_date','booking_time','booking_timezone','booking_room','booking_duration'            
               'user_id'=> 'assign_to_UserId','user_name' => 'assign_to' , 'lead_assign_to' => 'assign_to_UserId'
           ))->join(array('u' => 'de_users'), 'l.assign_to_UserId = u.user_id', array('image'), 'left');
       $select_detail->where(array('l.budget = ?' =>  $budget));
       $data_detail = $this->executeQuery($select_detail);               
       $result_detail = $data_detail->toArray(); 
            
       $groups = array();
       
       foreach ($result_detail as $item){
       $key = $item['user_id'];                    
       $groups[$key]['user_id'] = $key;
       $groups[$key]['items'] = $item;
       $groups[$key]['count'] += 1;
                      
       }
     } 
    //Budget Greater than 5,000  and Smaller than 9,999 $10,000 - $19,999
     if($budget == '$10,000 - $19,999') 
     { 

       $select_detail = new \Zend\Db\Sql\Select(); 
       $select_detail->from('de_userdetail')->columns(array('id'));        
       $select_detail = new \Zend\Db\Sql\Select(); 
       $select_detail->from(array('l' => 'de_userdetail')) 
           ->columns(array( 
              //'id','first_name', 'last_name', 'phone_number', 'email','Street','State','City','Zip', 'product', 'referral','special_instructions','budget','reference_product', 'contact_method','name' => 'assign_to','assign_to_UserId','reson_skip_next_in_line','lead_status','specify_requirements','lead_status','lead_owner','create_date','lead_close_date','booking_date','booking_time','booking_timezone','booking_room','booking_duration'            
               'user_id'=> 'assign_to_UserId','user_name' => 'assign_to' , 'lead_assign_to' => 'assign_to_UserId'
           ))->join(array('u' => 'de_users'), 'l.assign_to_UserId = u.user_id', array('image'), 'left');
       $select_detail->where(array('l.budget = ?' =>  $budget));
       $data_detail = $this->executeQuery($select_detail);               
       $result_detail = $data_detail->toArray(); 
            
       $groups = array();
       
       foreach ($result_detail as $item){
       $key = $item['user_id'];                    
       $groups[$key]['user_id'] = $key;
       $groups[$key]['items'] = $item;
       $groups[$key]['count'] += 1;
                      
       }
     } 
    //Budget Greater than 5,000  and Smaller than 9,999 $20,000 - $34,999
     if($budget == '$20,000 - $34,999') 
     { 

       $select_detail = new \Zend\Db\Sql\Select(); 
       $select_detail->from('de_userdetail')->columns(array('id'));        
       $select_detail = new \Zend\Db\Sql\Select(); 
       $select_detail->from(array('l' => 'de_userdetail')) 
           ->columns(array( 
              //'id','first_name', 'last_name', 'phone_number', 'email','Street','State','City','Zip', 'product', 'referral','special_instructions','budget','reference_product', 'contact_method','name' => 'assign_to','assign_to_UserId','reson_skip_next_in_line','lead_status','specify_requirements','lead_status','lead_owner','create_date','lead_close_date','booking_date','booking_time','booking_timezone','booking_room','booking_duration'            
               'user_id'=> 'assign_to_UserId','user_name' => 'assign_to' , 'lead_assign_to' => 'assign_to_UserId'
           ))->join(array('u' => 'de_users'), 'l.assign_to_UserId = u.user_id', array('image'), 'left');
       $select_detail->where(array('l.budget = ?' =>  $budget));
       $data_detail = $this->executeQuery($select_detail);               
       $result_detail = $data_detail->toArray(); 
            
       $groups = array();
       
       foreach ($result_detail as $item){
       $key = $item['user_id'];                    
       $groups[$key]['user_id'] = $key;
       $groups[$key]['items'] = $item;
       $groups[$key]['count'] += 1;
                      
       }
     } 
    //Budget Greater than 5,000  and Smaller than 9,999 $35,000 - $49,999
     if($budget == '$35,000 - $49,999') 
     { 

       $select_detail = new \Zend\Db\Sql\Select(); 
       $select_detail->from('de_userdetail')->columns(array('id'));        
       $select_detail = new \Zend\Db\Sql\Select(); 
       $select_detail->from(array('l' => 'de_userdetail')) 
           ->columns(array( 
              //'id','first_name', 'last_name', 'phone_number', 'email','Street','State','City','Zip', 'product', 'referral','special_instructions','budget','reference_product', 'contact_method','name' => 'assign_to','assign_to_UserId','reson_skip_next_in_line','lead_status','specify_requirements','lead_status','lead_owner','create_date','lead_close_date','booking_date','booking_time','booking_timezone','booking_room','booking_duration'            
               'user_id'=> 'assign_to_UserId','user_name' => 'assign_to' , 'lead_assign_to' => 'assign_to_UserId'
           ))->join(array('u' => 'de_users'), 'l.assign_to_UserId = u.user_id', array('image'), 'left');
       $select_detail->where(array('l.budget = ?' =>  $budget));
       $data_detail = $this->executeQuery($select_detail);               
       $result_detail = $data_detail->toArray(); 
            
       $groups = array();
       
       foreach ($result_detail as $item){
       $key = $item['user_id'];                    
       $groups[$key]['user_id'] = $key;
       $groups[$key]['items'] = $item;
       $groups[$key]['count'] += 1;
                      
       }
     } 
    //Budget Greater than 5,000  and Smaller than 9,999 $50,000 - $74,999
     if($budget == '$50,000 - $74,999') 
     { 

       $select_detail = new \Zend\Db\Sql\Select(); 
       $select_detail->from('de_userdetail')->columns(array('id'));        
       $select_detail = new \Zend\Db\Sql\Select(); 
       $select_detail->from(array('l' => 'de_userdetail')) 
           ->columns(array( 
              //'id','first_name', 'last_name', 'phone_number', 'email','Street','State','City','Zip', 'product', 'referral','special_instructions','budget','reference_product', 'contact_method','name' => 'assign_to','assign_to_UserId','reson_skip_next_in_line','lead_status','specify_requirements','lead_status','lead_owner','create_date','lead_close_date','booking_date','booking_time','booking_timezone','booking_room','booking_duration'            
               'user_id'=> 'assign_to_UserId','user_name' => 'assign_to' , 'lead_assign_to' => 'assign_to_UserId'
           ))->join(array('u' => 'de_users'), 'l.assign_to_UserId = u.user_id', array('image'), 'left');
       $select_detail->where(array('l.budget = ?' =>  $budget));
       $data_detail = $this->executeQuery($select_detail);               
       $result_detail = $data_detail->toArray(); 
            
       $groups = array();
       
       foreach ($result_detail as $item){
       $key = $item['user_id'];                    
       $groups[$key]['user_id'] = $key;
       $groups[$key]['items'] = $item;
       $groups[$key]['count'] += 1;
                      
       }
     } 
    //Budget Greater than 5,000  and Smaller than 9,999 $75,000 - $99,999
     if($budget == '$75,000 - $99,999') 
     { 

       $select_detail = new \Zend\Db\Sql\Select(); 
       $select_detail->from('de_userdetail')->columns(array('id'));        
       $select_detail = new \Zend\Db\Sql\Select(); 
       $select_detail->from(array('l' => 'de_userdetail')) 
           ->columns(array( 
              //'id','first_name', 'last_name', 'phone_number', 'email','Street','State','City','Zip', 'product', 'referral','special_instructions','budget','reference_product', 'contact_method','name' => 'assign_to','assign_to_UserId','reson_skip_next_in_line','lead_status','specify_requirements','lead_status','lead_owner','create_date','lead_close_date','booking_date','booking_time','booking_timezone','booking_room','booking_duration'            
               'user_id'=> 'assign_to_UserId','user_name' => 'assign_to' , 'lead_assign_to' => 'assign_to_UserId'
           ))->join(array('u' => 'de_users'), 'l.assign_to_UserId = u.user_id', array('image'), 'left');
       $select_detail->where(array('l.budget = ?' =>  $budget));
       $data_detail = $this->executeQuery($select_detail);               
       $result_detail = $data_detail->toArray(); 
            
       $groups = array();
       
       foreach ($result_detail as $item){
       $key = $item['user_id'];                    
       $groups[$key]['user_id'] = $key;
       $groups[$key]['items'] = $item;
       $groups[$key]['count'] += 1;
                      
       }
     } 
    //Budget Greater than 5,000  and Smaller than 9,999 $100,000+
     if($budget == '$100,000+') 
     { 

       $select_detail = new \Zend\Db\Sql\Select(); 
       $select_detail->from('de_userdetail')->columns(array('id'));        
       $select_detail = new \Zend\Db\Sql\Select(); 
       $select_detail->from(array('l' => 'de_userdetail')) 
           ->columns(array( 
              //'id','first_name', 'last_name', 'phone_number', 'email','Street','State','City','Zip', 'product', 'referral','special_instructions','budget','reference_product', 'contact_method','name' => 'assign_to','assign_to_UserId','reson_skip_next_in_line','lead_status','specify_requirements','lead_status','lead_owner','create_date','lead_close_date','booking_date','booking_time','booking_timezone','booking_room','booking_duration'            
               'user_id'=> 'assign_to_UserId','user_name' => 'assign_to' , 'lead_assign_to' => 'assign_to_UserId'
           ))->join(array('u' => 'de_users'), 'l.assign_to_UserId = u.user_id', array('image'), 'left');
       $select_detail->where->where('l.budget',$budget);
       $data_detail = $this->executeQuery($select_detail);               
       $result_detail = $data_detail->toArray(); 
            
       $groups = array();
       
       foreach ($result_detail as $item){
       $key = $item['user_id'];                    
       $groups[$key]['user_id'] = $key;
       $groups[$key]['items'] = $item;
       $groups[$key]['count'] += 1;
                      
       }
     } 
     //Merge Array
     $merge_array = $groups + $groupUser;
     //SortArrayAcoordingToName
     $sortArray = array();
     foreach ($merge_array as $key => $row)
     {
      $sortArray[$key] = $row['items']['user_name']; 
     }
     //sort
     array_multisort($sortArray, SORT_ASC, $merge_array);
     //GroupArray
     $result_end = array();
       foreach ($merge_array as $element) {
       $result_end[$element['user_id']][] = $element;
      }
     
      return $result_end;

   }

  //**********************Dashboard**********************************************************//
   


         
      
}


