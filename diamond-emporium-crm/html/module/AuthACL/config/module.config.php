<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
 
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

//use AuthACL\Model\AclActions;
use AuthACL\Model\AclActionsTable;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

return array(    
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'AuthACL\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /authacl/:controller/:action
           'authacl' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'AuthACL\Controller',
                        'controller'    => 'AuthACL\Controller\Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
							//'route'    => '[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
					'logout' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'logout',
							'defaults' => array(
								'controller' => 'AuthACL\Controller\Index',
								'action'     => 'logout',
							),
						),
					),
					'forgotpassword' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'forgotpassword',
							'defaults' => array(
								'controller' => 'AuthACL\Controller\Index',
								'action'     => 'forgotpassword',
							),
						),
					),
					'sendresetpassurl' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'sendresetpassurl',
							'defaults' => array(
								'controller' => 'AuthACL\Controller\Index',
								'action'     => 'sendresetpassurl',
							),
						),
					),
					'resetpass' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'resetpass/:reset_pass_code',
							'defaults' => array(
								'controller' => 'AuthACL\Controller\Index',
								'action'     => 'resetpass',
							),
						),
					),
					'storeresetpass' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'storeresetpass',
							'defaults' => array(
								'controller' => 'AuthACL\Controller\Index',
								'action'     => 'storeresetpass',
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
        	'MyStorage' => '\AuthACL\Model\MyAuthStorage',
		),
		'factories'=>array(
			'AuthACL\Model\MyAuthStorage' => function($sm){
				return new \AuthACL\Model\MyAuthStorage(); 
			},
	 
			'AuthService' => function($sm) {
				//My assumption, you've alredy set dbAdapter
				//and has users table with columns : user_name and pass_word
				//that password hashed with md5
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$config = $sm->get('Config');
				$dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, $config["dbPrefix"].'users', 'email', 'password', 'MD5(?)');
				$authService = new AuthenticationService();
				$authService->setAdapter($dbTableAuthAdapter);
				$authService->setStorage($sm->get('MyStorage'));
				return $authService;
			},
		),
    ),
	'controllers' => array(
        'invokables' => array(
            'AuthACL\Controller\Index' => 'AuthACL\Controller\IndexController',
        ),
    ),
	'controller_plugins' => array(
		'invokables' => array(
			'AuthPlugin' => 'AuthACL\Controller\Plugin\AuthPlugin',
		)
	),
	'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'auth-acl/index/index' => __DIR__ . '/../view/auth-acl/index/index.phtml',
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
