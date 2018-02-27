<?php
/**
 * Created by NetBeans.
 * User: MuhammadUmarWaheed
 * Date: 10/30/17
 * Time: 07:53 AM
 */
namespace Leave\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;


class LeaveController extends AbstractActionController
{
    protected $leaveTable;

    
    public function indexAction()
    {
  
      try{
  
          
          $hello = "Welcome To Leave Page";
          
          echo json_encode($hello);
          
          exit;
          
      }
      catch (Exception $e)
      {
          \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
      }
       
    }
    
    
    //GetUserDetail For Leave Page
    public function  ajaxGetUserDetailForLeaveAction()
    {
        try {
            $sm = $this->getServiceLocator();
            $identity = $sm->get('AuthService')->getIdentity();
            $config = $this->getServiceLocator()->get('Config');
            $params = $this->getRequest()->getQuery()->toArray();         
            $leadsTable = $this->getServiceLocator()->get('Leave\Model\LeaveTable');            
            $calArr = $leadsTable->fetchUserDetailForLeave($params);
            echo json_encode($calArr);
            exit;
            
        } catch (Exception $e) {
            \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
    }
    //End ajaxEndViewCalender
    
    
    
    //SaveAjaxLeads
    public function ajaxSaveLeavesAction()
    {
        try
        {
              $sm = $this->getServiceLocator();
	      $identity = $sm->get('AuthService')->getIdentity();
	      $request = $this->getRequest();
             
              if($request->isPost()){
                $start_date = '';
               $end_date = '';      
               
	      $posts = $request->getPost()->toArray();	
              $objUserTable = $sm->get('Leave\Model\LeaveTable');    
              
              $leave_date = $posts['date'];
             
              //Check $leave_date contain ","
             if(!empty($leave_date))
             {
              if(strpos($leave_date, ',') !=  FALSE)
              {
                  //Convert String Into Array
                  $array = explode(',', $leave_date);
                  
              if(!empty($array))
              {
                  $start_date = reset($array);
                  $end_date = end($array);
              }
              
              }
              
              else{
                  
                  $start_date = $leave_date;
                  $end_date = $leave_date;
                  
              }
             }
                 $posts['Leave_StartDate'] = trim($start_date); 
                 $posts['Leave_EndDate'] = trim($end_date);
                 $Leave_AssignUserName  = $posts['AssignUs'];
                 $posts['Leave_AssignUserName'] = $Leave_AssignUserName;
                 $posts['Leave_UserId'] = $posts['Id'];
                 unset($posts['date']);
                 unset($posts['leave_userName']);
                 unset($posts['AssignUs']);
                 unset($posts['Id']);

             
                 $objUserTable->saveLeave($posts);      

              }
		
              exit; 
        }
        catch (Exception $ex)
        {
             \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
    }

    
    //GetAllLeaves
    
    public function ajaxGetAllLeavesAction()
    {
        try {
         
            $sm = $this->getServiceLocator();
            $identity = $sm->get('AuthService')->getIdentity();
            $config = $this->getServiceLocator()->get('Config');
            $params = $this->getRequest()->getQuery()->toArray();
            
            
            //Get Month Number and Year and Make Date
            $month_Number = $params['month'];
            $year = $params['year'];
            $dateStarted = 01;
            $date = "$year-$month_Number-01";
            
            unset($params['month']);
            unset($params['year']);
            
            $params['date'] = $date;
            $objLeaveDetail = $sm->get('Leave\Model\LeaveTable');  
            //$callDetail = $objLeaveDetail->getUserLeaves($params);
            //echo json_encode($callDetail);            


            $callDetail = $objLeaveDetail->getUserLeaves($params);
            
            echo json_encode($callDetail);
            
            exit;
            
        } 
        catch (Exception $ex)
        {
             \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
    }
        
        
        
        
    
}