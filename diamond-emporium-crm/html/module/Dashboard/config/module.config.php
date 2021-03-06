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
              'checkLeadEmail' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'checkLeadEmail',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'checkLeadEmail',
                            ),
                        ),
                    ),
             'ajaxGetUserLeaves' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetUserLeaves',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetUserLeaves',
                            ),
                        ),
                    ),
             'ajaxCheckUserIsOnLeave' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxCheckUserIsOnLeave',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxCheckUserIsOnLeave',
                            ),
                        ),
                    ),  
            'ajaxGetDataListofSalesRep' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetDataListofSalesRep',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetDataListofSalesRep',
                            ),
                        ),
                    ),
            'ajaxGetCheckUserEmail' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetCheckUserEmail',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetCheckUserEmail',
                            ),
                        ),
                    ),
             'ajaxGetUserColor' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetUserColor',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetUserColor',
                            ),
                        ),
                    ),
             'ajaxGetCustomerOnLookup' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetCustomerOnLookup',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetCustomerOnLookup',
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
             'ajaxGetCustomerByName' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetCustomerByName',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetCustomerByName',
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
                    
              'ajaxGetDataForSearch' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetDataForSearch',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetDataForSearch',
                            ),
                        ),
                    ),
             'ajaxGetCustomerById' => array(
                              'type' => 'Segment',
                              'options' => array(
                                    'route'    => 'ajaxGetCustomerById',
                                    'defaults' => array(
                                        'controller' => 'Dashboard\Controller\Dashboard',
                                        'action'     => 'ajaxGetCustomerById',
                                    ),
                                ),
                            ),
             'ajaxGetDataForCustomViewCalender' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetDataForCustomViewCalender',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetDataForCustomViewCalender',
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
            'GetNextInLine' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'GetNextInLine',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'GetNextInLine',
                            ),
                        ),
                    ),
            'ajaxGetUserLoginDetail' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetUserLoginDetail',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetUserLoginDetail',
                            ),
                        ),
                    ),
             'ajaxGetCountriesList' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetCountriesList',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetCountriesList',
                            ),
                        ),
                    ),
              'ajaxGetStateList' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetStateList',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetStateList',
                            ),
                        ),
                    ),
            'ajaxGetProductsList' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetProductsList',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetProductsList',
                            ),
                        ),
                    ),
            'ajaxGetHowHeardList' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetHowHeardList',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetHowHeardList',
                            ),
                        ),
                    ),
               'ajaxGetCustomerAgainstEmail' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetCustomerAgainstEmail',
                            'defaults' => array(
                                'controller' => 'Dashboard\Controller\Dashboard',
                                'action'     => 'ajaxGetCustomerAgainstEmail',
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
