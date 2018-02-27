 <?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use SaveDashboard\Model\SaveDashboardTable;
use SaveDashboard\Model\UserTable;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;


return array(
    
      'router' => array(
        
        'routes' => array(
            
            'hello' => array(
                
                'type' => 'Literal',
                
                'options' => array(
                    
                    'route' => '/hello',
                    
                    'defaults' => array(
                        
                        'controller' => 'SaveDashboard\Controller\SaveDashboard',
                        
                        'action' => 'index'
                        
                    )
                    
                ),
                
                'may_terminate' => true,
                
                'child_routes' => array(
                    
                    'default' => array(
                        
                        'type' => 'Segment',
                        
                        'options' => array(
                            
                            'route' => '/[:action]',
                            
                            'constraints' => array(
                                
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                                
                            )
                            
                        )
                        
                    )
                    
                )
                
            ),
                
 
            
        )
        
    ),
    
    //Start-Service Layer In which we can call Database Table
  'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
	    'LeadToCustomerForm' => 'Customer\Form\LeadToCustomerForm',
        ),
        'invokables' => array(
			//'LeadToCustomerForm' => 'Customer\Form\LeadToCustomerForm',
		),
        'factories'=>array(
			'SaveDashboard\Model\SaveDashboardTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'userdetail', $dbAdapter, null, $resultSetPrototype);
				$table = new SaveDashboardTable($tableGateway);
				return $table;
			}
                     

		),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    //End-Service Layer In which we can call Database Table
    
    
    'controllers' => array(
        
        'invokables' => array(
            
            'SaveDashboard\Controller\SaveDashboard' => 'SaveDashboard\Controller\SaveDashboardController'
            
        )
        
    ),
    
    'view_manager' => array(
        
        'template_path_stack' => array(
            
            __DIR__ . '/../view'
            
        )
        
    )
    
);