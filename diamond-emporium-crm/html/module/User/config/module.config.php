<?php
/**
 * User module configuration
 */

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

use User\Model\UserTable;
use User\Form\UserForm;
use User\Form\MasterPasswordForm;

return array(
    'router' => array(
        'routes' => array(
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /admin/:controller/:action
            'user' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'User\Controller',
                        'controller'    => 'User\Controller\Index',
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
							'route'    => 'users',
							'defaults' => array(
								'controller' => 'User\Controller\Index',
								'action'     => 'index',
							),
						),
					),
					'ajaxuserlist' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'ajaxuserlist',
							'defaults' => array(
								'controller' => 'User\Controller\Index',
								'action'     => 'ajaxuserlist',
							),
						),
					),
                                        'uploadfileimage' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'uploadfileimage',
							'defaults' => array(
								'controller' => 'User\Controller\Index',
								'action' => 'uploadfileimage',
							),
						),
					),
					'userform' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'userform',
							'defaults' => array(
								'controller' => 'User\Controller\Index',
								'action'     => 'userform',
							),
						),
					),
					'saveuser' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'saveuser',
							'defaults' => array(
								'controller' => 'User\Controller\Index',
								'action'     => 'saveuser',
							),
						),
					),
					'deleteuser' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'deleteuser/:id',
							'defaults' => array(
								'controller' => 'User\Controller\Index',
								'action'     => 'deleteuser',
							),
						),
					),
					'checkduplicateemail' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'checkduplicateemail',
							'defaults' => array(
								'controller' => 'User\Controller\Index',
								'action'     => 'checkduplicateemail',
							),
						),
					),
					'setmasterpass' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'setmasterpass',
							'defaults' => array(
								'controller' => 'User\Controller\Index',
								'action'     => 'setmasterpass',
							),
						),
					),
					'emails' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'emails',
							'defaults' => array(
								'controller' => 'User\Controller\Index',
								'action'     => 'emails',
							),
						),
					),
                                      'ajaxuserstatusupdate' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'ajaxuserstatusupdate',
							'defaults' => array(
								'controller' => 'User\Controller\Index',
								'action'     => 'ajaxuserstatusupdate',
							),
						),
					),
                                      'ajaxGetUserStatus' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'ajaxGetUserStatus',
							'defaults' => array(
								'controller' => 'User\Controller\Index',
								'action'     => 'ajaxGetUserStatus',
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
			'User\Model\UserTable' => function($sm) {
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');
				$tableGateway = new TableGateway($config["dbPrefix"] . 'users', $dbAdapter, null, $resultSetPrototype);
				$table = new UserTable($tableGateway);
				return $table;
			},
			'User\Form\UserForm' => function($sm) {
				return new UserForm($sm);
			},
			'User\Form\MasterPasswordForm' => function($sm) {
				return new MasterPasswordForm();
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
			'User\Controller\Index' => 'User\Controller\IndexController',
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
            'user/index/index' => __DIR__ . '/../view/user/index/index.phtml',
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
