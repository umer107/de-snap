<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

use Customer\Form\LeadForm;
use Customer\Form\CustomerForm;
use Customer\Form\PartnerForm;

use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;

use Customer\Model\LeadsTable;
use Customer\Model\ProductsTable;
use Customer\Model\CountryTable;
use Customer\Model\StatesTable;
use Customer\Model\CustomersTable;
use Customer\Model\GridViewTable;
use Customer\Model\HowHeardTypesTable;
use Customer\Model\EthnicityTable;
use Customer\Model\ProfessionsTable;
use Customer\Model\RingFingerTable;
use Customer\Model\RingSizeTable;
use Customer\Model\UsersTable;

use Customer\Form\LeadToCustomerForm;
use Customer\Form\NewCustomerForm;

return array(
    'router' => array(
        'routes' => array(
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /admin/:controller/:action
            'index' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Customer\Controller',
                        'controller'    => 'Customer\Controller\Index',
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
							'route'    => 'customers',
							'defaults' => array(
								'controller' => 'Customer\Controller\Index',
								'action'     => 'index',
							),
						),
					),
					'dashboard' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'dashboard',
							'defaults' => array(
								'controller' => 'Customer\Controller\Index',
								'action'     => 'dashboard',
							),
						),
					),
					/*'tasks' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'tasks',
							'defaults' => array(
								'controller' => 'Customer\Controller\Index',
								'action'     => 'tasks',
							),
						),
					),*/
					'savemygridview' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'savemygridview',
							'defaults' => array(
								'controller' => 'Customer\Controller\Index',
								'action'     => 'savemygridview',
							),
						),
					),
					'deletegridview' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'deletegridview',
							'defaults' => array(
								'controller' => 'Customer\Controller\Index',
								'action'     => 'deletegridview',
							),
						),
					),
					'editgridview' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'editgridview',
							'defaults' => array(
								'controller' => 'Customer\Controller\Index',
								'action'     => 'editgridview',
							),
						),
					),
					'ajaxcustomerslist' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'ajaxcustomerslist',
							'defaults' => array(
								'controller' => 'Customer\Controller\Index',
								'action'     => 'ajaxcustomerslist',
							),
						),
					),
					'customerdetails' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'customerdetails[/:id]',
							'defaults' => array(
								'controller' => 'Customer\Controller\Index',
								'action'     => 'customerdetails',
							),
							'constraints' => array(
								'id' => '[0-9]+',
							),
						),
					),
					'ajaxpartnerslookup' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'ajaxpartnerslookup[/:id]',
							'defaults' => array(
								'controller' => 'Customer\Controller\Index',
								'action'     => 'ajaxpartnerslookup',
							),
							'constraints' => array(
								//'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id' => '[0-9]+',
							),
						),
					),
					'ajaxsearchgrids' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'ajaxsearchgrids',
							'defaults' => array(
								'controller' => 'Customer\Controller\Index',
								'action'     => 'ajaxsearchgrids',
							),
						),
					),
					'ajaxrecordscount' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'ajaxrecordscount',
							'defaults' => array(
								'controller' => 'Customer\Controller\Index',
								'action'     => 'ajaxrecordscount',
							),
						),
					),
					'savecustomer' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'savecustomer',
							'defaults' => array(
								'controller' => 'Customer\Controller\Index',
								'action'     => 'savecustomer',
							),
						),
					),
					'deletecustomer' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'deletecustomer',
							'defaults' => array(
								'controller' => 'Customer\Controller\Index',
								'action'     => 'deletecustomer',
							),
						),
					),
					'savepartner' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'savepartner',
							'defaults' => array(
								'controller' => 'Customer\Controller\Index',
								'action'     => 'savepartner',
							),
						),
					),
					'unassignpartner' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'unassignpartner',
							'defaults' => array(
								'controller' => 'Customer\Controller\Index',
								'action'     => 'unassignpartner',
							),
						),
					),
					'createcustomer' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'createcustomer',
							'defaults' => array(
								'controller' => 'Customer\Controller\Index',
								'action'     => 'createcustomer',
							),
						),
					),
					'checkduplicate' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'checkduplicate[/:except_id]',
							'defaults' => array(
								'controller' => 'Customer\Controller\Index',
								'action'     => 'checkduplicate',
							),
						),
					),
					'checkviewexist' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'checkviewexist',
							'defaults' => array(
								'controller' => 'Customer\Controller\Index',
								'action'     => 'checkviewexist',
							),
						),
					),
					'upoadprofilephoto' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'upoadprofilephoto',
							'defaults' => array(
								'controller' => 'Customer\Controller\Index',
								'action'     => 'upoadprofilephoto',
							),
						),
					),
                ),
            ),
            'lead' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Customer\Controller',
                        'controller'    => 'Customer\Controller\Leads',
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
                    'leads' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'leads',
							'defaults' => array(
								'controller' => 'Customer\Controller\Leads',
								'action'     => 'index',
							),
						),
					),					
					'webleads' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'webleads',
							'defaults' => array(
								'controller' => 'Customer\Controller\Leads',
								'action'     => 'webleads',
							),
						),
					),
					'webtoleadsuccess' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'webtoleadsuccess',
							'defaults' => array(
								'controller' => 'Customer\Controller\Leads',
								'action'     => 'webtoleadsuccess',
							),
						),
					),
					'leadsgrid' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'ajaxgetleads',
							'defaults' => array(
								'controller' => 'Customer\Controller\Leads',
								'action'     => 'ajaxgetleads',
							),
						),
					),
					'ajaxcustomerslookup' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'ajaxcustomerslookup',
							'defaults' => array(
								'controller' => 'Customer\Controller\Leads',
								'action'     => 'ajaxcustomerslookup',
							),
						),
					),
					'ajaxoppcustomerslookup' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'ajaxoppcustomerslookup',
							'defaults' => array(
								'controller' => 'Customer\Controller\Leads',
								'action'     => 'ajaxoppcustomerslookup',
							),
						),
					),
					'ajaxcheckmobile' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'ajaxcheckmobile',
							'defaults' => array(
								'controller' => 'Customer\Controller\Leads',
								'action'     => 'ajaxcheckmobile',
							),
						),
					),
					'ajaxcheckemail' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'ajaxcheckemail',
							'defaults' => array(
								'controller' => 'Customer\Controller\Leads',
								'action'     => 'ajaxcheckemail',
							),
						),
					),					
					'ajaxcustomerfromlead' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'ajaxcustomerfromlead',
							'defaults' => array(
								'controller' => 'Customer\Controller\Leads',
								'action'     => 'ajaxcustomerfromlead',
							),
						),
					),					
					'leaddetails' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'leaddetails/:id',
							'defaults' => array(
								'controller' => 'Customer\Controller\Leads',
								'action'     => 'leaddetails',
							),
						),
					),
					'convertleadform' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'convertleadform',
							'defaults' => array(
								'controller' => 'Customer\Controller\Leads',
								'action'     => 'convertleadform',
							),
						),
					),
					'newleadform' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'newleadform',
							'defaults' => array(
								'controller' => 'Customer\Controller\Leads',
								'action'     => 'newleadform',
							),
						),
					),
                		'convertlead' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'convertlead',
							'defaults' => array(
								'controller' => 'Customer\Controller\Leads',
								'action'     => 'convertlead',
							),
						),
					),
					'leadopportunitylookup' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'leadopportunitylookup',
							'defaults' => array(
								'controller' => 'Customer\Controller\Leads',
								'action'     => 'leadopportunitylookup',
							),
						),
					),
					'matchedcustomer' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'matchedcustomer',
							'defaults' => array(
								'controller' => 'Customer\Controller\Leads',
								'action'     => 'matchedcustomer',
							),
						),
					),
					'sendmailtoleadowner' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => 'sendmailtoleadowner',
							'defaults' => array(
								'controller' => 'Customer\Controller\Leads',
								'action'     => 'sendmailtoleadowner',
							),
						),
					),
                	'updateleadstatus' => array(
                		'type' => 'Segment',
                		'options' => array(
                			'route'    => 'updateleadstatus',
                			'defaults' => array(
                				'controller' => 'Customer\Controller\Leads',
                				'action'     => 'updateleadstatus',
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
			'LeadToCustomerForm' => 'Customer\Form\LeadToCustomerForm',
        ),
        'invokables' => array(
			//'LeadToCustomerForm' => 'Customer\Form\LeadToCustomerForm',
		),
        'factories'=>array(
			'Customer\Model\LeadsTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'leads', $dbAdapter, null, $resultSetPrototype);
				$table = new LeadsTable($tableGateway);
				return $table;
			},
			'Customer\Model\ProductsTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'products', $dbAdapter, null, $resultSetPrototype);
				$table = new ProductsTable($tableGateway);
				return $table;
			},
			'Customer\Model\CountryTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'country', $dbAdapter, null, $resultSetPrototype);
				$table = new CountryTable($tableGateway);
				return $table;
			},
			'Customer\Model\StatesTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'states', $dbAdapter, null, $resultSetPrototype);
				$table = new StatesTable($tableGateway);
				return $table;
			},
			'Customer\Model\CustomersTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'customers', $dbAdapter, null, $resultSetPrototype);
				$table = new CustomersTable($tableGateway);
				return $table;
			},
			'Customer\Model\GridViewTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'gridview', $dbAdapter, null, $resultSetPrototype);
				$table = new GridViewTable($tableGateway);
				return $table;
			},
			'Customer\Model\HowHeardTypesTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'how_heard_lookup', $dbAdapter, null, $resultSetPrototype);
				$table = new HowHeardTypesTable($tableGateway);
				return $table;
			},
			'Customer\Model\EthnicityTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'ethnicity', $dbAdapter, null, $resultSetPrototype);
				$table = new EthnicityTable($tableGateway);
				return $table;
			},
			'Customer\Model\ProfessionsTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'professions', $dbAdapter, null, $resultSetPrototype);
				$table = new ProfessionsTable($tableGateway);
				return $table;
			},
			'Customer\Model\RingFingerTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'ring_finger', $dbAdapter, null, $resultSetPrototype);
				$table = new RingFingerTable($tableGateway);
				return $table;
			},
			'Customer\Model\RingSizeTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'ring_size', $dbAdapter, null, $resultSetPrototype);
				$table = new RingSizeTable($tableGateway);
				return $table;
			},
			'Customer\Model\UsersTable' =>  function($sm) {
        		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$resultSetPrototype = new HydratingResultSet();
				$config = $sm->get('Config');        
				$tableGateway = new TableGateway($config["dbPrefix"].'users', $dbAdapter, null, $resultSetPrototype);
				$table = new UsersTable($tableGateway);
				return $table;
			},
			'Customer\Form\LeadForm' => function($sm) {				
				return new LeadForm($sm);
			},
			'Customer\Form\CustomerForm' => function($sm) {		
				return new CustomerForm($sm);
			},
			'Customer\Form\PartnerForm' => function($sm) {		
				return new PartnerForm($sm);
			},
			'Customer\Form\NewCustomerForm' => function($sm) {		
				return new NewCustomerForm($sm);
			},
			'Customer\Form\LeadToCustomerForm' => function($sm) {		
				return new LeadToCustomerForm($sm);
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
			'Customer\Controller\Index' => 'Customer\Controller\IndexController',
			'Customer\Controller\Leads' => 'Customer\Controller\LeadsController',
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
            'customer/index/index' => __DIR__ . '/../view/customer/index/index.phtml',
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
