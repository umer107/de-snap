<?php
/**
 * Controller for leads
 */

namespace SaveDashboard\Controller;



use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SaveDashboardController extends AbstractActionController
{
    //IndexController in which we can Show All leads
    public function indexAction()
    {
        try {
            $config = $this->getServiceLocator()->get('Config');        
            
            
            $leadsTable = $this->getServiceLocator()->get('SaveDashboard\Model\SaveDashboardTable');
            $leadsArr = $leadsTable->fetchAll();
                    $view = new ViewModel([ 
                       'data' => $leadsArr, 
                    ]);  
            return  $view;
        }
        catch (Exception $e) {
            \De\Log::logApplicationInfo("Caught Exception: " . $e->getMessage() . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__);
        }
    }
    
    //IndexController in which show leads based on budget
    public function  ajaxGetLeadsByBudgetAction()
    {
        
        try {
            $config = $this->getServiceLocator()->get('Config');
            $params = $this->getRequest()->getQuery()->toArray();
            $leadsTable = $this->getServiceLocator()->get('SaveDashboard\Model\SaveDashboardTable');
            $leadsArr = $leadsTable->fetchRecordByBudgetId($params);
            echo json_encode($leadsArr);
	    exit;
        } catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
	}
        
    }
    
    
   
    
    
    
}