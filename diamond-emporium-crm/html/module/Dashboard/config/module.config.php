<?php
/**
 * Created by Netbeans.
 * User: MuhammadUmarWaheed
 */
use Dashboard\Model\DashboardTable;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

return array(
 
   'router' => array(
        
        'routes' => array(
            
            'dashboard' => array(
                
                'type' => 'Literal',
                
                'options' => array(
                    
                    'route' => '/dashboard',
                    
                    'defaults' => array(
                        
                     'controller' => 'Dashboard\Controller\Dashboard',
                        
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
                'addDash' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxAddDashboard',
                            'defaults' => array(
                                 'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxAddDashboard',
                            ),
                        ),
                    ),
             'ajaxGetUserBasedOnBudget' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetUserBasedOnBudget',
                            'defaults' => array(
                                 'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetUserBasedOnBudget',
                            ),
                        ),
                    ),
             'ajaxGetLeadsByBudget' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetLeadsByBudget',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetLeadsByBudget',
                            ),
                        ),
                    ),
             'ajaxGetDataforCalender' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetDataforCalender',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetDataforCalender',
                            ),
                        ),
                    ),
            'ajaxGetDataForQuestionViewCalender' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetDataForQuestionViewCalender',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetDataForQuestionViewCalender',
                            ),
                        ),
                    ),
             'ajaxUpdateleadStatus' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxUpdateleadStatus',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxUpdateleadStatus',
                            ),
                        ),
                    ),
            'ajaxGetTeamStatus' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetTeamStatus',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetTeamStatus',
                            ),
                        ),
                    ),
           'getBudget' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'getBudget',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'getBudget',
                            ),
                        ),
                    ),
           'ajaxGetLeadDetailForLeadPage' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetLeadDetailForLeadPage',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetLeadDetailForLeadPage',
                            ),
                        ),
                    ),
            
        )
        
    ),
    //ServiceStart
    
       'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
			
        ),
        'invokables' => array(
			
		),
        'factories'=>array(
			'Dashboard\Model\DashboardTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"]."userdetail", $dbAdapter, null, $resultSetPrototype);
				$table = new DashboardTable($tableGateway);
				return $table;
			},
                               
		 
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
    
    'controllers' => array(
        'invokables' => array(
            'Dashboard\Controller\Dashboard' => 'Dashboard\Controller\DashboardController',
        ),
    ),
                                
    //Service End
    'view_manager' => array(
        'template_path_stack' => array(
            'dashboard' => __DIR__ . '/../view',
        ),
    ),
);
