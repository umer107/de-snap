<?php
/**
 * Created by PhpStorm.
 * User: MuhammadUmarWaheed
 * Date: 12/7/16
 * Time: 10:01 AM
 */
use Leave\Model\LeaveTable;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

return array(
 
   'router' => array(
        
        'routes' => array(
            
            'leave' => array(
                
                'type' => 'Literal',
                
                'options' => array(
                    
                    'route' => '/leave',
                    
                    'defaults' => array(
                        
                     'controller' => 'Leave\Controller\Leave',
                        
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
                'ajaxGetUserDetailForLeave' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetUserDetailForLeave',
                            'defaults' => array(
                                 'controller' => 'Leave\Controller\Leave',
                                'action'     => 'ajaxGetUserDetailForLeave',
                            ),
                        ),
                    ),
             'ajaxSaveLeaves' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxSaveLeaves',
                            'defaults' => array(
                                 'controller' => 'Leave\Controller\Leave',
                                'action'     => 'ajaxSaveLeaves',
                            ),
                        ),
                    ),
             'ajaxGetAllLeaves' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => 'ajaxGetAllLeaves',
                            'defaults' => array(
                                 'controller' => 'Leave\Controller\Leave',
                                'action'     => 'ajaxGetAllLeaves',
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
			//'LeadToCustomerForm' => 'Customer\Form\LeadToCustomerForm',
        ),
        'invokables' => array(
			//'LeadToCustomerForm' => 'Customer\Form\LeadToCustomerForm',
		),
        'factories'=>array(
			'Leave\Model\LeaveTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"]."leaves", $dbAdapter, null, $resultSetPrototype);
				$table = new LeaveTable($tableGateway);
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
            'Leave\Controller\Leave' => 'Leave\Controller\LeaveController',
        ),
    ),
                                
    //Service End
    'view_manager' => array(
        'template_path_stack' => array(
            'leave' => __DIR__ . '/../view',
        ),
    ),
);