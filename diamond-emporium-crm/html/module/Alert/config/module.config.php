<?php
/**
 * Alert module configuration
 */

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

use Alert\Model\AlertTable;

return array(
    'router' => array(
        'routes' => array(
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /admin/:controller/:action
            'alert' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Alert\Controller',
                        'controller'    => 'Alert\Controller\Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
					'index' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'alerts',
							'defaults' => array(
								'controller' => 'Alert\Controller\Index',
								'action'     => 'index',
							),
						),
					),
               		'ajaxuseralertlist' => array(
           				'type' => 'Segment',
           				'options' => array(
       						'route'    => 'ajaxuseralertlist',
       						'defaults' => array(
   								'controller' => 'Alert\Controller\Index',
  								'action'     => 'ajaxuseralertlist',
       						),
          				),
              		),
               		'ajaxallalertlist' => array(
           				'type' => 'Segment',
           				'options' => array(
       						'route'    => 'ajaxallalertlist',
       						'defaults' => array(
   								'controller' => 'Alert\Controller\Index',
  								'action'     => 'ajaxallalertlist',
       						),
          				),
              		),
                	'ajaxuseralertcount' => array(
           				'type' => 'Segment',
           				'options' => array(
       						'route'    => 'ajaxuseralertcount',
       						'defaults' => array(
   								'controller' => 'Alert\Controller\Index',
  								'action'     => 'ajaxuseralertcount',
       						),
          				),
              		),
                	'ajaxuseralertclear' => array(
           				'type' => 'Segment',
           				'options' => array(
       						'route'    => 'ajaxuseralertclear',
       						'defaults' => array(
   								'controller' => 'Alert\Controller\Index',
  								'action'     => 'ajaxuseralertclear',
       						),
          				),
              		),
                ),
			),
        ),
    ),
	'child_routes' => array(
	
	),
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
			'Alert\Model\AlertTable' => function($sm) {
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');
				$tableGateway = new TableGateway($config["dbPrefix"] . 'alerts', $dbAdapter, null, $resultSetPrototype);
				$table = new AlertTable($tableGateway);
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
			'Alert\Controller\Index' => 'Alert\Controller\IndexController',
		),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'alert/index/index' => __DIR__ . '/../view/alert/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
       'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
