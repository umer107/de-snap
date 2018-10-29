<?php
/**
 * Controller for Appointments
 */

namespace Appointment\Controller;



use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AppointmentController extends AbstractActionController
{
    //IndexController in which we can Show All leads
    public function indexAction()
    {
        try {
            $config = $this->getServiceLocator()->get('Config');        
            
            
            $leadsTable = $this->getServiceLocator()->get('Appointment\Model\AppointmentTable');
            /*$leadsArr = $leadsTable->fetchAll();
                    $view = new ViewModel([ 
                       'data' => $leadsArr, 
                    ]); */ 
            return  $view;
        }
        catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
    }
    

    
    
   
    
    
    
}