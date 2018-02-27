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

use Task\Model\TasksTable;
use Task\Model\TasksHistoryTable;
use Task\Model\TasksCategoryTable;
use Task\Model\TasksSubjectTable;
use Task\Model\TasksPriorityTable;
use Task\Model\CommentAttachmentsTable;

return array(
    'router' => array(
        'routes' => array(
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /admin/:controller/:action
            'tasks' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Task\Controller',
                        'controller'    => 'Task\Controller\Index',
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
					'tasks' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'tasks',
							'defaults' => array(
								'controller' => 'Task\Controller\Index',
								'action'     => 'index',
							),
						),
					),
					'gettaskdetails' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'gettaskdetails',
							'defaults' => array(
								'controller' => 'Task\Controller\Index',
								'action'     => 'gettaskdetails',
							),
						),
					),
					'createtask' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'createtask',
							'defaults' => array(
								'controller' => 'Task\Controller\Index',
								'action'     => 'createtask',
							),
						),
					),
					'updateindividual' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'updateindividual',
							'defaults' => array(
								'controller' => 'Task\Controller\Index',
								'action'     => 'updateindividual',
							),
						),
					),
					'savecomment' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'savecomment',
							'defaults' => array(
								'controller' => 'Task\Controller\Index',
								'action'     => 'savecomment',
							),
						),
					),
					'attachfile' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'attachfile',
							'defaults' => array(
								'controller' => 'Task\Controller\Index',
								'action'     => 'attachfile',
							),
						),
					),
					'gettaskhistorydetails' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'gettaskhistorydetails',
							'defaults' => array(
								'controller' => 'Task\Controller\Index',
								'action'     => 'gettaskhistorydetails',
							),
						),
					),
					'saveattachments' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'saveattachments',
							'defaults' => array(
								'controller' => 'Task\Controller\Index',
								'action'     => 'saveattachments',
							),
						),
					),
					'downloadattachment' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'downloadattachment/:comment_id/:file',
							'defaults' => array(
								'controller' => 'Task\Controller\Index',
								'action'     => 'downloadattachment',
							),
						),
					),
					'changetaskstatus' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'changetaskstatus',
							'defaults' => array(
								'controller' => 'Task\Controller\Index',
								'action'     => 'changetaskstatus',
							),
						),
					),
					'deletetask' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'deletetask',
							'defaults' => array(
								'controller' => 'Task\Controller\Index',
								'action'     => 'deletetask',
							),
						),
					),
					'editcomment' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'editcomment',
							'defaults' => array(
								'controller' => 'Task\Controller\Index',
								'action'     => 'editcomment',
							),
						),
					),
					'deletecomment' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'deletecomment',
							'defaults' => array(
								'controller' => 'Task\Controller\Index',
								'action'     => 'deletecomment',
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
			'Task\Model\TasksTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'tasks', $dbAdapter, null, $resultSetPrototype);
				$table = new TasksTable($tableGateway);
				return $table;
			},
			'Task\Model\TasksHistoryTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'task_history', $dbAdapter, null, $resultSetPrototype);
				$table = new TasksHistoryTable($tableGateway);
				return $table;
			},
			'Task\Model\CommentAttachmentsTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'comment_attachments', $dbAdapter, null, $resultSetPrototype);
				$table = new CommentAttachmentsTable($tableGateway);
				return $table;
			},
			'Task\Model\TasksCategoryTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'tasks_category', $dbAdapter, null, $resultSetPrototype);
				$table = new TasksCategoryTable($tableGateway);
				return $table;
			},
			'Task\Model\TasksSubjectTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'tasks_subject', $dbAdapter, null, $resultSetPrototype);
				$table = new TasksSubjectTable($tableGateway);
				return $table;
			},
			'Task\Model\TasksPriorityTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'tasks_priority', $dbAdapter, null, $resultSetPrototype);
				$table = new TasksPriorityTable($tableGateway);
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
			'Task\Controller\Index' => 'Task\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'task/index/index'           => __DIR__ . '/../view/task/index/index.phtml'
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
