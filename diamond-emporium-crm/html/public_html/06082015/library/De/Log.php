<?php
/**
 * Log class which is used to maintain the application level logs. This is a default class for zend library.
 * @author sreedhark
 * @package GAP
 */
namespace De;

final class Log {
	/*
	 * private variable to access through out the class
	 */
	private static $_config;
	private static $_logger;
	
	/*
	 * Error level constants
	 */
	CONST INFO = 1;
	CONST ERR = 4;
	CONST ELIGIBILITY = 5;
	/*
	 * Application Environment constants 
	 */
	CONST PRODUCTION = 'production';
	
	/**
	 * Used to log the application level info into applicationinfo.log file.
	 * @param STRING $message
	 */
	public static function logApplicationInfo($message = null) {
		
		$auth = new \Zend\Authentication\AuthenticationService();		
		$userData = $auth->getIdentity();
		
		if ($userData != null)
			if (! empty ( $userData['email'] ))
				$message .= " - " . $userData['email'];
			else
				$message .= " - Guest";
		else
			$message .= " - Guest";
			
		$log = new \Zend\Log\Logger();
		$writer = new \Zend\Log\Writer\Stream('./data/logs/application.log');
		$log->addWriter($writer);
		$log->info($message);
	}
	
}