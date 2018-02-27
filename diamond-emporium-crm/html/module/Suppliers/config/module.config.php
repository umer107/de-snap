<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

use Suppliers\Form\SuppliersForm;
use Suppliers\Model\SuppliersTable;

return array(
    'router' => array(
        'routes' => array(
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /admin/:controller/:action
            'suppliers' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Suppliers\Controller',
                        'controller'    => 'Suppliers\Controller\Index',
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
							'route'    => 'suppliers',
							'defaults' => array(
								'controller' => 'Suppliers\Controller\Index',
								'action'     => 'index',
							),
						),
					),
					'ajaxgetsuppliers' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'ajaxgetsuppliers',
							'defaults' => array(
								'controller' => 'Suppliers\Controller\Index',
								'action'     => 'ajaxgetsuppliers',
							),
						),
					),
					'supplierdetails' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'supplierdetails/:id',
							'defaults' => array(
								'controller' => 'Suppliers\Controller\Index',
								'action'     => 'supplierdetails',
							),
						),
					),
					'deletesupplier' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'deletesupplier/:id',
							'defaults' => array(
								'controller' => 'Suppliers\Controller\Index',
								'action'     => 'deletesupplier',
							),
						),
					),
					'newsuppliersform' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'newsuppliersform',
							'defaults' => array(
								'controller' => 'Suppliers\Controller\Index',
								'action'     => 'newsuppliersform',
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
        'factories'=>array(
			'Suppliers\Model\SuppliersTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'suppliers', $dbAdapter, null, $resultSetPrototype);
				$table = new SuppliersTable($tableGateway);
				return $table;
			},
			'Suppliers\Form\SuppliersForm' => function($sm) {				
				return new SuppliersForm($sm);
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
			'Suppliers\Controller\Index' => 'Suppliers\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'suppliers/index/index'           => __DIR__ . '/../view/suppliers/index/index.phtml'
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
