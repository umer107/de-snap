<?php
/**
 * Authenicate users
 */
namespace AuthACL\Controller\Plugin;
  
use Zend\Mvc\Controller\Plugin\AbstractPlugin,
    Zend\Session\Container as SessionContainer,
    Zend\Permissions\Acl\Acl,
    Zend\Permissions\Acl\Role\GenericRole as Role,
    Zend\Permissions\Acl\Resource\GenericResource as Resource;
	
use Zend\Mvc\MvcEvent;
     
class AuthPlugin extends AbstractPlugin
{
	private function getAclRole($authService) {
		//setting ACL...
		$acl = new Acl();
			
		$roles = include 'module.acl.roles.php';
		$allResources = array();
		foreach ($roles as $role => $resources) {
				
			$role = new \Zend\Permissions\Acl\Role\GenericRole($role);
			$acl -> addRole($role);
		
			$allResources = array_merge($resources, $allResources);
		
			//adding resources
			foreach ($resources as $resource) {
				// Edit 4
				if(!$acl ->hasResource($resource))
					$acl -> addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
			}
		
			//adding restrictions
			foreach ($resources as $resource) {
				$acl -> allow($role, $resource);
			}
		}
			
		if($authService->hasIdentity()){
			$identity = $authService->getIdentity();
			if($identity['role_id'] == 1) {
				$role = 'superadmin';
			} else if($identity['role_id'] == 2) {
				$role = 'admin';
			} else if($identity['role_id'] == 3) {
				$role = 'partner';
			} else if($identity['role_id'] == 4) {
				$role = 'customer';
			}
		}else{
			$role = 'anonymous';
		}
		
		return array($acl, $role);
	}
	
	/**
	 * Authenticate users and manage Access Control
	 */
    public function doAuthorization(MvcEvent $e)
    {
		try{
			$app = $e->getApplication();
			$sm  = $app->getServiceManager();
			$authService = $sm->get('AuthService');

			list ($acl, $role) = $this->getAclRole($authService);
			
			$params = $e -> getRouteMatch() -> getParams();
			$resource = $params["controller"]."::".$params["action"];
			
			$isAlowed = false;
			
			if ($acl->hasResource($resource) && $acl->isAllowed($role, $resource)) {
				$isAlowed = true;
			}
			
			if(!$isAlowed){
				if($authService->hasIdentity() && $params["action"] == 'index'){
					//$this->getController()->redirect()->toUrl( '/dashboard' );
					
					header('location: /dashboard');
					exit;
				}elseif(!$authService->hasIdentity()){
					//$this->getController()->redirect()->toUrl( '/' );
					
					header('location: /');
					exit;
				}else{
					$response = $e->getResponse();
					//location to page or what ever
					$response->getHeaders()->addHeaderLine('Location', $e->getRequest()->getBaseUrl().'/404');
					$response->setStatusCode(404);
					exit;
				}
			}else{
				
				$config = $sm->get('Config');
				
				$viewModel = $e->getViewModel();
				$viewModel->setVariables(array(
					'authService' => $authService,
					'matchedRouteName' => $e->getRouteMatch()->getMatchedRouteName(),
					'config' => $config,
				));
			}
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	/*
	 * Check is the resource accessible.
	 * TODO: not sure this is the right way to go about this.
	 */
	public function checkResource($authService, $resource) {
		$isAllowed = false;

		list ($acl, $role) = $this->getAclRole($authService);
		if ($acl->hasResource($resource) && $acl->isAllowed($role, $resource)) {
			$isAllowed = true;
		}
		
    	return $isAllowed;
    }
}
