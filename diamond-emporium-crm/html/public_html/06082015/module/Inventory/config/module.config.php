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
use Inventory\Model\LookupTable;
use Inventory\Model\DiamondTable;
use Inventory\Model\ConsignTable;
use Inventory\Model\WeddingringTable;
use Inventory\Model\EngagementringTable;
use Inventory\Model\EarringTable;
use Inventory\Model\PendantTable;
use Inventory\Model\MiscellaneousTable;
use Inventory\Model\ChainTable;
use Inventory\Form\DiamondInventoryForm;
use Inventory\Form\ConsignInventoryForm;
use Inventory\Form\WeddingringInventoryForm;
use Inventory\Form\EngagementringInventoryForm;
use Inventory\Form\EarringInventoryForm;
use Inventory\Form\PendantInventoryForm;
use Inventory\Form\MiscellaneousInventoryForm;
use Inventory\Form\ChainInventoryForm;
use Inventory\Model\WebsiteLinksTable;
use Inventory\Model\WebsiteCategoryTable;

return array(
	'router' => array(
		'routes' => array(
			// The following is a route to simplify getting started creating
			// new controllers and actions without needing to create a new
			// module. Simply drop new controllers in, and you can access them
			// using the path /admin/:controller/:action
			'inventory' => array(
				'type' => 'Literal',
				'options' => array(
					'route' => '/',
					'defaults' => array(
						'__NAMESPACE__' => 'Inventory\Controller',
						'controller' => 'Inventory\Controller\Inventory',
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
							'route' => 'inventory',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Index',
								'action' => 'index',
							),
						),
					),
					'savediamond' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'savediamond',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Index',
								'action' => 'savediamond',
							),
						),
					),
					'ajaxsupplierslookup' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'ajaxsupplierslookup',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Index',
								'action' => 'ajaxsupplierslookup',
							),
						),
					),
					'ajaxgetdiamonds' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'ajaxgetdiamonds',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Index',
								'action' => 'ajaxgetdiamonds',
							),
						),
					),
					'uploadfile' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'uploadfile',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Index',
								'action' => 'uploadfile',
							),
						),
					),
					'saveconsign' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'saveconsign',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Index',
								'action' => 'saveconsign',
							),
						),
					),
					'validateowner' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'validateowner',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Index',
								'action' => 'validateowner',
							),
						),
					),
					'consignform' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'consignform',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Index',
								'action' => 'consignform',
							),
						),
					),
					'getconsigndetails' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'getconsigndetails',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Index',
								'action' => 'getconsigndetails',
							),
						),
					),
					'diamonddetails' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'diamonddetails/:id',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Index',
								'action' => 'diamonddetails',
							),
						),
					),
					'deletediamond' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'deletediamond',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Index',
								'action' => 'deletediamond',
							),
						),
					),
					'inventory-weddingring' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'inventory-weddingring',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Weddingring',
								'action' => 'index',
							),
						),
					),
					'saveweddingring' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'saveweddingring',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Weddingring',
								'action' => 'saveweddingring',
							),
						),
					),
					'ajaxgetweddingrings' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'ajaxgetweddingrings',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Weddingring',
								'action' => 'ajaxgetweddingrings',
							),
						),
					),
					'weddingringdetails' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'weddingringdetails/:id',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Weddingring',
								'action' => 'weddingringdetails',
							),
						),
					),
					'deleteadditional' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'deleteadditional',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Weddingring',
								'action' => 'deleteadditional',
							),
						),
					),
					'getadditionallist' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'getadditionallist',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Weddingring',
								'action' => 'getadditionallist',
							),
						),
					),
					'deleteweddingring' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'deleteweddingring',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Weddingring',
								'action' => 'deleteweddingring',
							),
						),
					),
					'inventory-engagementring' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'inventory-engagementring',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Engagementring',
								'action' => 'index',
							),
						),
					),
					'saveengagementring' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'saveengagementring',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Engagementring',
								'action' => 'saveengagementring',
							),
						),
					),
					'ajaxgetengagementrings' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'ajaxgetengagementrings',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Engagementring',
								'action' => 'ajaxgetengagementrings',
							),
						),
					),
					'engagementringdetails' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'engagementringdetails/:id',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Engagementring',
								'action' => 'engagementringdetails',
							),
						),
					),
					'deleteengagementring' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'deleteengagementring',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Engagementring',
								'action' => 'deleteengagementring',
							),
						),
					),
					'inventory-earring' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'inventory-earring',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Earring',
								'action' => 'index',
							),
						),
					),
					'saveearring' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'saveearring',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Earring',
								'action' => 'saveearring',
							),
						),
					),
					'ajaxgetearrings' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'ajaxgetearrings',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Earring',
								'action' => 'ajaxgetearrings',
							),
						),
					),
					'earringdetails' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'earringdetails/:id',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Earring',
								'action' => 'earringdetails',
							),
						),
					),
					'deleteearring' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'deleteearring',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Earring',
								'action' => 'deleteearring',
							),
						),
					),
					'inventory-pendant' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'inventory-pendant',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Pendant',
								'action' => 'index',
							),
						),
					),
					'savependant' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'savependant',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Pendant',
								'action' => 'savependant',
							),
						),
					),
					'ajaxgetpendants' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'ajaxgetpendants',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Pendant',
								'action' => 'ajaxgetpendants',
							),
						),
					),
					'pendantdetails' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'pendantdetails/:id',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Pendant',
								'action' => 'pendantdetails',
							),
						),
					),
					'deletependant' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'deletependant',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Pendant',
								'action' => 'deletependant',
							),
						),
					),
					'inventory-miscellaneous' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'inventory-miscellaneous',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Miscellaneous',
								'action' => 'index',
							),
						),
					),
					'savemiscellaneous' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'savemiscellaneous',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Miscellaneous',
								'action' => 'savemiscellaneous',
							),
						),
					),
					'ajaxgetmiscellaneous' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'ajaxgetmiscellaneous',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Miscellaneous',
								'action' => 'ajaxgetmiscellaneous',
							),
						),
					),
					'miscellaneousdetails' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'miscellaneousdetails/:id',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Miscellaneous',
								'action' => 'miscellaneousdetails',
							),
						),
					),
					'deletemiscellaneous' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'deletemiscellaneous',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Miscellaneous',
								'action' => 'deletemiscellaneous',
							),
						),
					),
					'inventory-chain' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'inventory-chain',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Chain',
								'action' => 'index',
							),
						),
					),
					'savechain' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'savechain',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Chain',
								'action' => 'savechain',
							),
						),
					),
					'ajaxgetchain' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'ajaxgetchain',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Chain',
								'action' => 'ajaxgetchain',
							),
						),
					),
					'chaindetails' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'chaindetails/:id',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Chain',
								'action' => 'chaindetails',
							),
						),
					),
					'deletechain' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'deletechain',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Chain',
								'action' => 'deletechain',
							),
						),
					),
					'websitelinks' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'websitelinks',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Websites',
								'action' => 'index',
							),
						),
					),
					'uploadmultiplefile' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'uploadmultiplefile',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Index',
								'action' => 'uploadmultiplefile',
							),
						),
					),
					'unlinkfile' => array(
						'type' => 'Segment',
						'options' => array(
							'route' => 'unlinkfile',
							'defaults' => array(
								'controller' => 'Inventory\Controller\Index',
								'action' => 'unlinkfile',
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
			'InventoryForm' => 'Inventory\Form\InventoryForm',
		),
		'invokables' => array(
		//'LeadToCustomerForm' => 'Customer\Form\LeadToCustomerForm',
		),
		'factories' => array(
			'Inventory\Model\LookupTable' => function($sm) {
				$table = new LookupTable($sm);
				return $table;
			},
			'Inventory\Model\DiamondTable' => function($sm) {
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');
				$tableGateway = new TableGateway($config["dbPrefix"] . 'inventory_diamonds', $dbAdapter, null, $resultSetPrototype);
				$table = new DiamondTable($tableGateway);
				return $table;
			},
			'Inventory\Model\ConsignTable' => function($sm) {
				$table = new ConsignTable($sm);
				return $table;
			},
			'Inventory\Model\WebsiteLinksTable' => function($sm) {
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');
				$tableGateway = new TableGateway($config["dbPrefix"] . 'website_links', $dbAdapter, null, $resultSetPrototype);
				$table = new WebsiteLinksTable($tableGateway);
				return $table;
			},
			'Inventory\Model\WebsiteCategoryTable' => function($sm) {
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');
				$tableGateway = new TableGateway($config["dbPrefix"] . 'website_categories', $dbAdapter, null, $resultSetPrototype);
				$table = new WebsiteCategoryTable($tableGateway);
				return $table;
			},
			'Inventory\Form\DiamondInventoryForm' => function($sm) {
				return new DiamondInventoryForm($sm);
			},
			'Inventory\Form\ConsignInventoryForm' => function($sm) {
				return new ConsignInventoryForm($sm);
			},
			'Inventory\Model\WeddingringTable' => function($sm) {
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');
				$tableGateway = new TableGateway($config["dbPrefix"] . 'inventory_wedding_rings', $dbAdapter, null, $resultSetPrototype);
				$table = new WeddingringTable($tableGateway);
				return $table;
			},
			'Inventory\Form\WeddingringInventoryForm' => function($sm) {
				return new WeddingringInventoryForm($sm);
			},
			'Inventory\Model\EngagementringTable' => function($sm) {
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');
				$tableGateway = new TableGateway($config["dbPrefix"] . 'inventory_engagement_rings', $dbAdapter, null, $resultSetPrototype);
				$table = new EngagementringTable($tableGateway);
				return $table;
			},
			'Inventory\Form\EngagementringInventoryForm' => function($sm) {
				return new EngagementringInventoryForm($sm);
			},
			'Inventory\Model\EarringTable' => function($sm){
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');
				$tableGateway = new TableGateway($config["dbPrefix"] . 'inventory_ear_rings', $dbAdapter, null, $resultSetPrototype);
				$table = new EarringTable($tableGateway);
				return $table;
			},
			'Inventory\Form\EarringInventoryForm' => function($sm){
				return new EarringInventoryForm($sm);
			},
			'Inventory\Model\PendantTable' => function($sm){
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');
				$tableGateway = new TableGateway($config["dbPrefix"] . 'inventory_pendants', $dbAdapter, null, $resultSetPrototype);
				$table = new PendantTable($tableGateway);
				return $table;
			},
			'Inventory\Form\PendantInventoryForm' => function($sm){
				return new PendantInventoryForm($sm);
			},
			'Inventory\Model\MiscellaneousTable' => function($sm){
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');
				$tableGateway = new TableGateway($config["dbPrefix"] . 'inventory_miscellaneous', $dbAdapter, null, $resultSetPrototype);
				$table = new MiscellaneousTable($tableGateway);
				return $table;
			},
			'Inventory\Form\MiscellaneousInventoryForm' => function($sm){
				return new MiscellaneousInventoryForm($sm);
			},
			'Inventory\Model\ChainTable' => function($sm){
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');
				$tableGateway = new TableGateway($config["dbPrefix"] . 'inventory_chain', $dbAdapter, null, $resultSetPrototype);
				$table = new ChainTable($tableGateway);
				return $table;
			},
			'Inventory\Form\ChainInventoryForm' => function($sm){
				return new ChainInventoryForm($sm);
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
			'Inventory\Controller\Index' => 'Inventory\Controller\IndexController',
			'Inventory\Controller\Websites' => 'Inventory\Controller\WebsitesController',
			'Inventory\Controller\Weddingring' => 'Inventory\Controller\WeddingringController',
			'Inventory\Controller\Engagementring' => 'Inventory\Controller\EngagementringController',
			'Inventory\Controller\Earring' => 'Inventory\Controller\EarringController',
			'Inventory\Controller\Pendant' => 'Inventory\Controller\PendantController',
			'Inventory\Controller\Miscellaneous' => 'Inventory\Controller\MiscellaneousController',
			'Inventory\Controller\Chain' => 'Inventory\Controller\ChainController',
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
			'inventory/index/index' => __DIR__ . '/../view/inventory/index/index.phtml',
			'inventory/websites/index' => __DIR__ . '/../view/inventory/websites/index.phtml',
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
