<?php
//module/Admin/src/SanAuth/Model/MyAuthStorage.php
namespace AuthACL\Model;
 
use Zend\Authentication\Storage;
 
class MyAuthStorage extends Storage\Session
{
	/**
	 * Store authentication credentials in cookie
	 */
	public function setRememberMe($rememberMe = 0, $time = 1209600){
		try{
			if ($rememberMe == 1) {
				$this->session->getManager()->rememberMe($time);
			}
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}

	/**
	 * Remove authentication credentials from cookie
	 */     
	public function forgetMe(){
		try{
			$this->session->getManager()->forgetMe();
		}catch(\Exception $e){
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
}