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

use Invoice\Model\InvoiceTable;
use Invoice\Model\InvoiceItemTable;

return array(
	'router' => array(
		'routes' => array(
			// The following is a route to simplify getting started creating
			// new controllers and actions without needing to create a new
			// module. Simply drop new controllers in, and you can access them
			// using the path /admin/:controller/:action
			'invoice' => array(
				'type' => 'Literal',
				'options' => array(
					'route' => '/',
					'defaults' => array(
						'__NAMESPACE__' => 'Invoice\Controller',
						'controller' => 'Invoice\Controller\Index',
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
					'invoicequotes' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'invoicequotes',
							'defaults' => array(
								'controller' => 'Invoice\Controller\Index',
								'action' => 'index',
							),
						),
					),
                    'ajax-quote' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'ajax-quote',
							'defaults' => array(
								'controller' => 'Invoice\Controller\Index',
								'action' => 'ajaxQuote',
							),
						),
					),
                   'ajax-invoice' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'ajax-invoice',
							'defaults' => array(
								'controller' => 'Invoice\Controller\Index',
								'action' => 'ajaxInvoice',
							),
						),
					),
					'newquotes' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'newquotes',
							'defaults' => array(
								'controller' => 'Invoice\Controller\Index',
								'action' => 'newquotes',
							),
						),
					),
					'newinvoice' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'newinvoice',
							'defaults' => array(
								'controller' => 'Invoice\Controller\Index',
								'action' => 'newinvoice',
							),
						),
					),
                    'deleteinvoice' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'deleteinvoice/[:id]',
							'defaults' => array(
								'controller' => 'Invoice\Controller\Index',
								'action' => 'deleteinvoice',
							),
						),
					),
					'deletequote' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'deletequote/[:id]',
							'defaults' => array(
								'controller' => 'Invoice\Controller\Index',
								'action' => 'deletequote',
							),
						),
					),
					'duplicateinvoice' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'duplicateinvoice/[:id]',
							'defaults' => array(
								'controller' => 'Invoice\Controller\Index',
								'action' => 'duplicateinvoice',
							),
						),
					),
					'duplicatequote' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'duplicatequote/[:id]',
							'defaults' => array(
								'controller' => 'Invoice\Controller\Index',
								'action' => 'duplicatequote',
							),
						),
					),
					'copytoinvoice' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'copytoinvoice/[:id]',
							'defaults' => array(
								'controller' => 'Invoice\Controller\Index',
								'action' => 'copytoinvoice',
							),
						),
					),
					'emailinvoice' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'emailinvoice',
							'defaults' => array(
								'controller' => 'Invoice\Controller\Index',
								'action' => 'emailinvoice',
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
			'Invoice\Model\InvoiceTable' => function($sm) {
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');
				$tableGateway = new TableGateway($config["dbPrefix"] . 'invoice', $dbAdapter, null, $resultSetPrototype);
				$table = new InvoiceTable($tableGateway);
				return $table;
			},
            'Invoice\Model\InvoiceItemTable' => function($sm) {
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');
				$tableGateway = new TableGateway($config["dbPrefix"] . 'invoice_item', $dbAdapter, null, $resultSetPrototype);
				$table = new InvoiceItemTable($tableGateway);
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
			'Invoice\Controller\Index' => 'Invoice\Controller\IndexController',
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
			'invoice/index/index' => __DIR__ . '/../view/invoice/index/index.phtml',
			'invoice/websites/index' => __DIR__ . '/../view/invoice/websites/index.phtml',
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
