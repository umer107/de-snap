<?php

/**
 *
 */
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

use Inventory\Model\LookupTable;
use Order\Model\OrderTable;
use Order\Form\JobForm;
use Order\Form\OrderForm;
use Order\Model\JobPacketTable;
use Order\Model\CaddesignTable;
use Order\Model\PrototypeTable;
use Order\Model\CastTable;
use Order\Model\WorkshopTable;
use Order\Form\ComposeEmailForm;

return array(
	'router' => array(
		'routes' => array(
			// The following is a route to simplify getting started creating
			// new controllers and actions without needing to create a new
			// module. Simply drop new controllers in, and you can access them
			// using the path /admin/:controller/:action
			'order' => array(
				'type' => 'Literal',
				'options' => array(
					'route' => '/',
					'defaults' => array(
						'__NAMESPACE__' => 'Order\Controller',
						'controller' => 'Order\Controller\Index',
						'action' => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'default' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => '/[:controller[/:action]]',
							'constraints' => array(
								'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
							),
							'defaults' => array(
							),
						),
					),
					'index' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'orders[/:invoice_id]',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'index',
							),
						),
					),
					'neworder' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'neworder',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'neworder',
							),
						),
					),
					'ajaxinvoicelookup' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'ajaxinvoicelookup',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'ajaxinvoicelookup',
							),
						),
					),
					'createorder' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'createorder',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'createorder',
							),
						),
					),
					'ajaxorderlist' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'ajaxorderlist',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'ajaxorderlist',
							),
						),
					),
					'orderdetails' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'orderdetails/:id',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'orderdetails',
							),
						),
					),
					'editorderform' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'editorderform',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'editorderform',
							),
						),
					),
					'deleteorder' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'deleteorder',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'deleteorder',
							),
						),
					),
					'createjobpacket' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'createjobpacket',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'createjobpacket',
							),
						),
					),
					'ajaxjoblist' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'ajaxjoblist',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'ajaxjoblist',
							),
						),
					),
					'jobdetails' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'jobdetails/:id',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'jobdetails',
							),
						),
					),
					'startjob' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'startjob',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'startjob',
							),
						),
					),
					'savecaddesign' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'savecaddesign',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'savecaddesign',
							),
						),
					),
					'prototypestep1' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'prototypestep1',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'prototypestep1',
							),
						),
					),
					'getcaddesignstage' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'getcaddesignstage',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'getcaddesignstage',
							),
						),
					),
					'prototypestep2' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'prototypestep2',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'prototypestep2',
							),
						),
					),
					'caststep1' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'caststep1',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'caststep1',
							),
						),
					),
					'caststep2' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'caststep2',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'caststep2',
							),
						),
					),
					'workshopstep1' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'workshopstep1',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'workshopstep1',
							),
						),
					),
					'savesuppliertask' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'savesuppliertask',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'savesuppliertask',
							),
						),
					),					
					'workshopqualitycontrol' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'workshopqualitycontrol',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'workshopqualitycontrol',
							),
						),
					),
					'workshopfinalstep' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'workshopfinalstep',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'workshopfinalstep',
							),
						),
					),
					'startjobrequest' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'startjobrequest',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'startjobrequest',
							),
						),
					),
					'approvejob' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'approvejob/:job_id/:approval_code',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'approvejob',
							),
						),
					),
					'deletejob' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'deletejob',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'deletejob',
							),
						),
					),
					'addmilestone' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'addmilestone',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'addmilestone',
							),
						),
					),
					'deletemilestone' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'deletemilestone',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'deletemilestone',
							),
						),
					),
					'changejobstatus' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'changejobstatus',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'changejobstatus',
							),
						),
					),
					'emailmilestone' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'emailmilestone',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'emailmilestone',
							),
						),
					),
					'composemilestoneemail' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'composemilestoneemail',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'composemilestoneemail',
							),
						),
					),
					'ajaxgetmilestoneemail' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'ajaxgetmilestoneemail',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'ajaxgetmilestoneemail',
							),
						),
					),
					'viewmilestoneemail' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'viewmilestoneemail/:id',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'viewmilestoneemail',
							),
						),
					),
					'updatejobform' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'updatejobform',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'updatejobform',
							),
						),
					),
					'downloademailattachment' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'downloademailattachment/:id/:index',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'downloademailattachment',
							),
						),
					),
					'printjob' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'printjob',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'printjob',
							),
						),
					),
					'updateworkshoptask' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'updateworkshoptask',
							'defaults' => array(
								'controller' => 'Order\Controller\Index',
								'action' => 'updateworkshoptask',
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
		//'LeadToCustomerForm' => 'Customer\Form\LeadToCustomerForm',
		),
		'factories' => array(
			'Inventory\Model\LookupTable' => function($sm) {
				$table = new LookupTable($sm);
				return $table;
			},
			'Order\Form\OrderForm' => function($sm) {
				return new OrderForm();
			},
			'Order\Form\ComposeEmailForm' => function($sm) {
				return new ComposeEmailForm();
			},
			'Order\Model\OrderTable' => function($sm) {
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');
				$tableGateway = new TableGateway($config["dbPrefix"] . 'orders', $dbAdapter, null, $resultSetPrototype);
				$table = new OrderTable($tableGateway, $config, $sm);
				return $table;
			},
			'Order\Form\JobForm' => function($sm) {
				return new JobForm($sm);
			},
			'Order\Model\JobPacketTable' => function($sm) {
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');
				$tableGateway = new TableGateway($config["dbPrefix"] . 'job_packet', $dbAdapter, null, $resultSetPrototype);
				$table = new JobPacketTable($tableGateway, $config);
				return $table;
			},
			'Order\Model\CaddesignTable' => function($sm) {
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');
				$tableGateway = new TableGateway($config["dbPrefix"] . 'milestone_cad', $dbAdapter, null, $resultSetPrototype);
				$table = new CaddesignTable($tableGateway, $sm);
				return $table;
			},
			'Order\Model\PrototypeTable' => function($sm) {
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');
				$tableGateway = new TableGateway($config["dbPrefix"] . 'milestone_prototype', $dbAdapter, null, $resultSetPrototype);
				$table = new PrototypeTable($tableGateway, $sm);
				return $table;
			},
			'Order\Model\CastTable' => function($sm) {
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');
				$tableGateway = new TableGateway($config["dbPrefix"] . 'milestone_cast', $dbAdapter, null, $resultSetPrototype);
				$table = new CastTable($tableGateway, $sm);
				return $table;
			},
			'Order\Model\WorkshopTable' => function($sm) {
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');
				$tableGateway = new TableGateway($config["dbPrefix"] . 'milestone_workshop', $dbAdapter, null, $resultSetPrototype);
				$table = new WorkshopTable($tableGateway, $sm);
				return $table;
			},
		),
	),
	'translator' => array(
		'locale' => 'en_US',
		'translation_file_patterns' => array(
			array(
				'type' => 'gettext',
				'base_dir' => __DIR__ . '/../language',
				'pattern' => '%s.mo',
			),
		),
	),
	'controllers' => array(
		'invokables' => array(
			'Order\Controller\Index' => 'Order\Controller\IndexController',
		),
	),
	'view_manager' => array(
		'display_not_found_reason' => true,
		'display_exceptions' => true,
		'doctype' => 'HTML5',
		'not_found_template' => 'error/404',
		'exception_template' => 'error/index',
		'template_map' => array(
			'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
			'order/index/index' => __DIR__ . '/../view/order/index/index.phtml',
			'error/404' => __DIR__ . '/../view/error/404.phtml',
			'error/index' => __DIR__ . '/../view/error/index.phtml',
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
