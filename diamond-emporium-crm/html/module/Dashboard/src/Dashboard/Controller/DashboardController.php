<?php
/**
 * Created by PhpStorm.
 * User: MuhammadUmarWaheed
 */
namespace Dashboard\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Dashboard\Model\Dashboard;
use Dashboard\Form\DashboardForm;

class DashboardController extends AbstractActionController
{
    protected $dashboardTable;

       
    public function indexAction()
    {
  
  
      try{
         $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'); 
         $form = new DashboardForm($dbAdapter);        
         $form->get('submit');
         $request = $this->getRequest();
         if ($request->isPost()) {
            $dashboard = new Dashboard();
            $form->setInputFilter($dashboard->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $dashboard->exchangeArray($form->getData());
                $this->getDashboardTable()->saveDashboard($dashboard);
                return $this->redirect()->toRoute('dashboard');
            }
        }
        return array('form' => $form);	
      }
      catch (Exception $e)
      {
          \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
      }
       
    }


    
    public function ajaxAddDashboardAction()
    {
     
          try{
      
      $sm = $this->getServiceLocator();
			$identity = $sm->get('AuthService')->getIdentity();
			$request = $this->getRequest();
			
      $lead_owner = $identity['user_id'];
      $lead_owner_name = $identity['first_name'] . ' ' . $identity['last_name'];
                        
			if($request->isPost()){
                            
			$posts = $request->getPost()->toArray();
                        
                        
                        $AssignInUserId = $posts['assign_id'];
                        unset($posts['assign_id']);
                        $posts['assign_to_UserId'] = $AssignInUserId;
                              
                   
                       
			$posts['create_date'] = date('Y-m-d H:i:s');
                        $posts['lead_owner_name'] =  $lead_owner_name;
                        $posts['lead_owner'] = $lead_owner;
                      
			$objUserTable = $sm->get('SaveDashboard\Model\SaveDashboardTable');
      
	                 $userid = $identity['user_id'];
                         
			 $objUserTable->saveDashboard($posts);
      }
			exit;
      }

      catch (Exception $e)
      {
          \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
      }
        
     
    }


      


    public function ajaxGetUserBasedOnBudgetAction()
    {
                  try{
                      $sm = $this->getServiceLocator();
		      $identity = $sm->get('AuthService')->getIdentity();
                      $config = $this->getServiceLocator()->get('Config');
                      $params = $this->getRequest()->getQuery()->toArray();
                      $objUserTable = $sm->get('Leave\Model\LeaveTable');
                      $leadsArr = $objUserTable->fetchUserByBudget($params);
                     
                      echo json_encode($leadsArr);
                      exit;
      }
      catch (Exception $e)
      {
          \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
      }
    }

    //GetLeads
     public function  ajaxGetLeadsByBudgetAction()
    {
        
        try {
            $config = $this->getServiceLocator()->get('Config');
            $params = $this->getRequest()->getQuery()->toArray();         
            $leadsTable = $this->getServiceLocator()->get('Leave\Model\LeaveTable');
            $leadsArr = $leadsTable->fetchRecordByBudgetId($params);
            echo json_encode($leadsArr);
	    exit;
        } catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
	}
        
    }
    
    
    
    //Load Calender
    public function  ajaxGetDataforCalenderAction()
    {
         try {
            $config = $this->getServiceLocator()->get('Config');
            $params = $this->getRequest()->getQuery()->toArray();         
            $leadsTable = $this->getServiceLocator()->get('Leave\Model\LeaveTable');
            $leadsArr = $leadsTable->fetchCalenderData($params);
            echo json_encode($leadsArr);
            exit;
        } catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
	}
    }
    
    
    
    //ajaxQuestionViewCalender
    public function  ajaxGetDataForQuestionViewCalenderAction()
    {
        try {
             $sm = $this->getServiceLocator();
            $identity = $sm->get('AuthService')->getIdentity();
            $config = $this->getServiceLocator()->get('Config');
            $params = $this->getRequest()->getQuery()->toArray();         
            $leadsRes = $this->getServiceLocator()->get('Leave\Model\LeaveTable');          
            $calArr = $leadsRes->fetchQuestionViewCalenderData($params);
         
        echo json_encode($calArr);
            exit;
            
        } catch (Exception $e) {
            \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
    }
    
    
    public function  ajaxUpdateleadStatusAction()
    {
        try {
            $sm = $this->getServiceLocator();
            $identity = $sm->get('AuthService')->getIdentity();
            $config = $this->getServiceLocator()->get('Config');
            $request = $this->getRequest();
            
              if($request->isPost()){
                 $post = $request->getPost()->toArray();                                 		
		 $objUserTable = $sm->get('SaveDashboard\Model\SaveDashboardTable');
                 
                 if($post['lead_status'] == 'Closed / Lost')
                 {
                     $post['lead_status'] = 'Deal closed';
                 }
                 else if($post['lead_status'] == 'Closed Lost')
                 {
                     $post['lead_status'] = 'Closed';
                 }
                 if($post['lead_date'] != '')
                     {
			$dateOldFormat = explode("/", $post['lead_date']);
			$dateNewFormat = $dateOldFormat[2].'-'.$dateOldFormat[1].'-'.$dateOldFormat[0].' 00:00:00';
			$post['lead_close_date'] = $dateNewFormat;
		} else {
			$post['lead_close_date'] = null;
		}
                 unset($post['lead_date']);
		 $objUserData = $objUserTable->updateLeadStatus($post);
              }
            exit;
            
       } catch (Exception $e) {
            \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
    }


    
   public  function ajaxGetTeamStatusAction()
   {
       try {
                
            $sm = $this->getServiceLocator();
            $identity = $sm->get('AuthService')->getIdentity();
            $config = $this->getServiceLocator()->get('Config');
            $request = $this->getRequest();
            $user_id = $identity['user_id'];
            $today_Date = date("Y-m-d");            
            $params = array();
            $params['user_id'] = $user_id;
            $params['today_date'] = $today_Date; 
            $leadsTable = $this->getServiceLocator()->get('Leave\Model\LeaveTable');
            $calArr = $leadsTable->fetchTeamStatus($params);
            echo json_encode($calArr);
            exit;
            
                    
           
       }catch (Exception $e) {
            \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
   }

   //GetLeadDetailOnLeadClick
   public function  ajaxGetLeadDetailForLeadPageAction()
   {
        try {
                
            $config = $this->getServiceLocator()->get('Config');
            $params = $this->getRequest()->getQuery()->toArray();         
            $leadsTable = $this->getServiceLocator()->get('Leave\Model\LeaveTable');
            $leadsArr = $leadsTable->fetchLeadRecord($params);
            echo json_encode($leadsArr);
      exit;
            
                    
           
       }catch (Exception $e) {
            \De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
        }
       
       
       
   }


    
    public function addAction()
    {
        $form = new DashboardForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $dashboard = new Dashboard();
            $form->setInputFilter($dashboard->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $dashboard->exchangeArray($form->getData());
                $this->getDashboardTable()->saveDashboard($dashboard);
                return $this->redirect()->toRoute('dashboard');
            }
        }
        return array('form' => $form);
    }

    public function editAction()
    {

        $id   = (int) $this->params()->fromRoute('id', 0);
        if (!$id)
            return $this->redirect()->toRoute('dashboard', array( 'action' => 'add'  ));

        $dashboard    = $this->getDashboardTable()->getDashboard($id);
        $form       = new DashboardForm();
        $form->bind($dashboard);

        $form->get('submit')->setAttribute('value', 'Edit');

        $request    = $this->getRequest();
        if ($request->isPost()) {

            $form->setInputFilter($dashboard->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getDashboardTable()->saveDashboard($form->getData());                
                return $this->redirect()->toRoute('dashboard');
            }
        }
        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('dashboard');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getDashboardTable()->deleteDashboard($id);
            }

            return $this->redirect()->toRoute('dashboard');
        }

        return array(
            'id'    => $id,
            'dashboard' => $this->getDashboardTable()->getDashboard($id)
        );
    }

   
    public function getDashboardTable()
    {
        if (!$this->dashboardTable) {
            $sm = $this->getServiceLocator();
            $this->dashboardTable = $sm->get('SaveDashboard\Model\SaveDashboardTable');
        }
        return $this->dashboardTable;
    }
}
