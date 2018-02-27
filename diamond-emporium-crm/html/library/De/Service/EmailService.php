<?php

namespace De\Service;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Mime;
use Zend\View\Model\ViewModel as ViewModel;

class EmailService {
	
	public static function sendEmail($smtpDetails, $emailParams) {
		try {
			if (is_array ( $emailParams )) {
				$transport = new SmtpTransport ();
				$ssl = "";
				if(isset($smtpDetails['ssl']) ){
					$ssl = $smtpDetails['ssl'];
				}

				$options = array (
					'name' => $smtpDetails['name'],
					'host' => $smtpDetails['smtpserver'],
					'port' => $smtpDetails['port']
				);
			
				if ($smtpDetails['username']) {
					$options = array_merge($options, array(
						'connection_class' => $smtpDetails['auth'],
						'connection_config' => array (
								'username' => $smtpDetails['username'],
								'password' => $smtpDetails['password'],
								'ssl' => isset($smtpDetails['ssl']) ? $smtpDetails['ssl'] : ''
						)
					));
				}
								
				$transport->setOptions ( new SmtpOptions ( $options ) );
				
				$message = new Message ();
				if (isset ( $emailParams ['toEmail'] ) && $emailParams ['toEmail'] != '') {
					$message->addTo ( $emailParams ['toEmail'], $emailParams ['toName'] );
					$message->setFrom ( $smtpDetails['username'], $smtpDetails['name'] );
					$message->setSubject ( $emailParams ['subject'] );
					
					if(isset($emailParams ['additionalEmails']) && !empty($emailParams ['additionalEmails'])){
						foreach($emailParams ['additionalEmails'] as $key => $additionalEmails){
							if($key == 'to'){
								foreach($additionalEmails as $toEmail){
									$message->addTo($toEmail ['email'], $toEmail ['name']);
								}
							}elseif($key == 'cc'){
								foreach($additionalEmails as $ccEmail){
									$message->addCc($ccEmail ['email'], $ccEmail ['name']);
								}
							}elseif($key == 'bcc'){
								foreach($additionalEmails as $bccEmail){
									$message->addBcc($bccEmail ['email'], $bccEmail ['name']);
								}
							}
						}
					}
					
					$view       = new \Zend\View\Renderer\PhpRenderer();
					$resolver   = new \Zend\View\Resolver\TemplateMapResolver();
					$resolver->setMap(array(
						'mailTemplate' => __DIR__ . '/../../../public/templates/email/'.$emailParams ['template']
					));
					$view->setResolver($resolver);
				 	
					$viewModel  = new ViewModel();
					$viewModel->setTemplate('mailTemplate')->setVariables(array('msgholdename' => $emailParams ['toName'], 'data' => $emailParams ['message']));
				 	
					$bodyPart = new \Zend\Mime\Message();
					$view->render($viewModel);
					$bodyMessage    = new \Zend\Mime\Part($view->render($viewModel));
					$bodyMessage->type = 'text/html';
					$bodyPart->setParts(array($bodyMessage));
					
					$message->setBody ( $bodyPart );
					return $transport->send ( $message );
				}
			}
		} catch ( \Exception $e ) {
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public static function msgBodyBg($msg) {
		return '<table class="emailbodycls" bgcolor="#F0F8FF" border="0" cellpadding="3" cellspacing="3" width="100%">
			<tr><td valign="top">' . $msg . '</td></tr></table>';
	}
	
	public function sendEmailWithAttachments($smtpDetails, $emailParams, $attachments = null){
		try{
			$content  = new MimeMessage();
			
			/** Setting email template starts **/
			$view       = new \Zend\View\Renderer\PhpRenderer();
			$resolver   = new \Zend\View\Resolver\TemplateMapResolver();
			$resolver->setMap(array(
				'mailTemplate' => __DIR__ . '/../../../public/templates/email/'.$emailParams ['template']
			));
			$view->setResolver($resolver);
			
			$viewModel  = new ViewModel();
			$viewModel->setTemplate('mailTemplate')->setVariables(array('msgholdename' => $emailParams ['toName'], 'data' => $emailParams ['message']));
			
			$html = $view->render($viewModel);
			/** Setting email template ends **/
			
			$htmlPart = new MimePart($html);
			$htmlPart->type = 'text/html';
			$htmlPart->charset = 'utf-8';
				
			$content->setParts(array($htmlPart));
	
			$contentPart = new MimePart($content->generateMessage());
			//$contentPart->type = 'multipart/alternative;' . PHP_EOL . ' boundary="' . $content->getMime()->boundary() . '"';
			$contentPart->type = 'text/html';
			$contentPart->charset = 'utf-8';
				
			$body = new MimeMessage();
			$body->addPart($contentPart);
			
			foreach ($attachments as $thisAttachment) {
				$attachment = new MimePart($thisAttachment['content']);
				$attachment->filename = $thisAttachment['filename'];
				$attachment->type = Mime::TYPE_OCTETSTREAM;
				$attachment->encoding    = Mime::ENCODING_BASE64;
				$attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
				$body->addPart($attachment);
			}
			
			$message = new Message();
			$message->setEncoding('utf-8');
			
			$headers = $message->getHeaders();
			$headers->addHeaderLine('X-Mailer', 'PHP/'.phpversion());
			
			/* TODO: Don't hardcode domain */
			$messageId = '<' . time() .'-' . md5($smtpDetails['fromEmail'] . $emailParams ['toEmail']) . '@diamondemporium.com.au>';
			$headers->addHeaderLine('Message-ID', $messageId);
			
			$headers->addHeaderLine('X-Originating-IP',  '[' . $_SERVER['REMOTE_ADDR'] . ']');
			
			$headers->addHeaderLine('Errors-To', $smtpDetails['fromEmail']);
			$headers->addHeaderLine('Return-Path', $smtpDetails['fromEmail']);
			
			$message->addTo($emailParams ['toEmail'], $emailParams ['toName']);
			$message->addFrom($smtpDetails['fromEmail'], $smtpDetails['fromName']);
			$message->setSender($smtpDetails['fromEmail'], $smtpDetails['fromName']);
			$message->setSubject($emailParams ['subject']);
			$message->setBody($body);
	
			if(isset($emailParams ['additionalEmails']) && !empty($emailParams ['additionalEmails'])){
				foreach($emailParams ['additionalEmails'] as $key => $additionalEmails){
					if($key == 'to'){
						foreach($additionalEmails as $toEmail){
							$message->addTo($toEmail ['email'], $toEmail ['name']);
						}
					}elseif($key == 'cc'){
						foreach($additionalEmails as $ccEmail){
							$message->addCc($ccEmail ['email'], $ccEmail ['name']);
						}
					}elseif($key == 'bcc'){
						foreach($additionalEmails as $bccEmail){
							$message->addBcc($bccEmail ['email'], $bccEmail ['name']);
						}
					}
				}
			}
			
			$options = array (
				'name' => $smtpDetails['name'],
				'host' => $smtpDetails['smtpserver'],
				'port' => $smtpDetails['port']
			);
			
			if ($smtpDetails['username']) {
				$options = array_merge($options, array(
					'connection_class' => $smtpDetails['auth'],
					'connection_config' => array (
							'username' => $smtpDetails['username'],
							'password' => $smtpDetails['password'],
							'ssl' => isset($smtpDetails['ssl']) ? $smtpDetails['ssl'] : ''
					)
				));
			}
	
			$transport = new SmtpTransport();
			$transport->setOptions( new SmtpOptions( $options ) );
			$transport->send( $message );
			
		} catch ( \Exception $e ) {
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public function bindMsgData($template, $data){
		$html = new Zend_View();
    	$html->setScriptPath( APPLICATION_PATH . '/../public/templates/email/' );
		$html->data = $data;
		$body = $html->render($template);
		return $body;
	}
}
