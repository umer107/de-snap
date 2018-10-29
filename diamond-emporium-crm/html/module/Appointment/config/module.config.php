<?php
/**
 * Created by Netbeans.
 * User: MuhammadUmarWaheed
 */
use Appointment\Model\AppointmentTable;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

return array(
 
   'router' => array(
        
        'routes' => array(
            
            'appointment' => array(
                
                'type' => 'Literal',
                
                'options' => array(
                    
                    'route' => '/appointment',
                    
                    'defaults' => array(
                        
                     'controller' => 'Appointment\Controller\Appointment',
                        
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
                
            )

            
            
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
			'Appointment\Model\AppointmentTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"]."appointments", $dbAdapter, null, $resultSetPrototype);
				$table = new AppointmentTable($tableGateway);
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
            'Appointment\Controller\Appointment' => 'Appointment\Controller\AppointmentController',
        ),
    ),
                                
    //Service End
    'view_manager' => array(
        'template_path_stack' => array(
            'appointment' => __DIR__ . '/../view',
        ),
    ),
);
