<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AuthACL;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

class Module implements AutoloaderProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
		try{
			$eventManager        = $e->getApplication()->getEventManager();
			$moduleRouteListener = new ModuleRouteListener();
			$moduleRouteListener->attach($eventManager);
			
			$serviceManager = $e->getApplication()->getServiceManager();
			$viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
			
			$identity = $serviceManager->get('AuthService')->getIdentity();
			
			$viewModel->identity = $identity;
			
			$eventManager->attach('route', array($this, 'loadConfiguration'), 2);
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
    }
	
	public function loadConfiguration(MvcEvent $e)
	{
		try{
			$application   = $e->getApplication();
			$sm            = $application->getServiceManager();
			$eventManager  = $application->getEventManager();
			$sharedManager = $eventManager->getSharedManager();
			
			$router = $sm->get('router');
			$request = $sm->get('request');
			
			$matchedRoute = $router->match($request);
			if (null !== $matchedRoute) {
				$sharedManager->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch',
					function($e) use ($sm) {
						$sm->get('ControllerPluginManager')->get('AuthPlugin')->doAuthorization($e);
					},2
				);
				/*$eventManager->attach('dispatch',
					function($e) use ($sm) {
						$sm->get('ControllerPluginManager')->get('AuthPlugin')->doAuthorization($e);
					}, 2
				);*/
			}
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

    public function getConfig()
    {
		try{
			return include __DIR__ . '/config/module.config.php';
		}catch(Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
        
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
			'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
        );
    }
    
	public function getServiceConfig()
	{
		return array(
			'factories'=>array(
				
			),
		);
	}
}
