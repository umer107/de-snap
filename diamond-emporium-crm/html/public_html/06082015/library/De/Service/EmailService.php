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
						'connection_class' => $smtpDetails['auth'], 
						'port' => $smtpDetails['port'], 
						'connection_config' => array (
							'username' => $smtpDetails['username'], 
							'password' => $smtpDetails['password'],
							'ssl' => $ssl
						)
				);

				$transport->setOptions ( new SmtpOptions ( $options ) );
				
				$message = new Message ();
				if (isset ( $emailParams ['toEmail'] ) && $emailParams ['toEmail'] != '') {
					$message->addTo ( $emailParams ['toEmail'], $emailParams ['toName'] );
					$message->setFrom ( $smtpDetails['username'], $smtpDetails['name'] );
					$message->setSubject ( $emailParams ['subject'] );
					
					$view       = new \Zend\View\Renderer\PhpRenderer();
					$resolver   = new \Zend\View\Resolver\TemplateMapResolver();
					$resolver->setMap(array(
						'mailTemplate' => __DIR__ . '/../../../public/templates/email/'.$emailParams ['template']
					));
					$view->setResolver($resolver);
				 	
					$viewModel  = new ViewModel();
					$viewModel->setTemplate('mailTemplate')->setVariables(array('msgholdename' => $emailParams ['toName'], 'data' => $emailParams ['message']));
				 	
					$bodyPart = new \Zend\Mime\Message();
					//echo $view->render($viewModel);exit;
					$bodyMessage    = new \Zend\Mime\Part($view->render($viewModel));
					$bodyMessage->type = 'text/html';
					$bodyPart->setParts(array($bodyMessage));
					
					$message->setBody ( $bodyPart );
					$transport->send ( $message );
				}
			}
		} catch ( \Exception $e ) {echo $e->getMessage ();
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}
	
	public static function msgBodyBg($msg) {
		return '<table class="emailbodycls" bgcolor="#F0F8FF" border="0" cellpadding="3" cellspacing="3" width="100%">
			<tr><td valign="top">' . $msg . '</td></tr></table>';
	}
	
	/*public static function sendEmailWithAttachments($smtpDetails, $emailParams, $attachments = null) {
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
						'connection_class' => $smtpDetails['auth'], 
						'port' => $smtpDetails['port'], 
						'connection_config' => array (
							'username' => $smtpDetails['username'], 
							'password' => $smtpDetails['password'],
							'ssl' => $ssl
						)
				);

				$transport->setOptions ( new SmtpOptions ( $options ) );
				$message = new Message ();
				if (isset ( $emailParams ['toEmail'] ) && $emailParams ['toEmail'] != '') {
					$message->addTo ( $emailParams ['toEmail'], $emailParams ['toName'] );
					$message->setFrom ( $smtpDetails['username'], $smtpDetails['name'] );
					$message->setSubject ( $emailParams ['subject'] );
					//$message->setBody ( $emailParams ['message'] );					
					
					// HTML part
					$htmlPart           = new MimePart($emailParams ['html']);
					$htmlPart->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
					$htmlPart->type     = "text/html; charset=UTF-8";
				
					// Plain text part
					$textPart           = new MimePart($emailParams ['text']);
					$textPart->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
					$textPart->type     = "text/plain; charset=UTF-8";
					
					$body = new MimeMessage();
					if ($attachments) {
						// With attachments, we need a multipart/related email. First part
						// is itself a multipart/alternative message        
						$content = new MimeMessage();
						$content->addPart($textPart);
						$content->addPart($htmlPart);
				
						$contentPart = new MimePart($content->generateMessage());
						$contentPart->type = "multipart/alternative;\n boundary=\"" .$content->getMime()->boundary() . '"';
						$body->addPart($contentPart);
						$messageType = 'multipart/related';
				
						// Add each attachment
						foreach ($attachments as $thisAttachment) {
							$attachment = new MimePart($thisAttachment['content']);
							$attachment->filename    = $thisAttachment['filename'];
							$attachment->type        = Mime::TYPE_OCTETSTREAM;
							$attachment->encoding    = Mime::ENCODING_BASE64;
							$attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
							$body->addPart($attachment);
						}
				
					}
					
					// attach the body to the message and set the content-type
					$message->setBody($body);
					$message->getHeaders()->get('content-type')->setType($messageType);
					$message->setEncoding('UTF-8');
					
					return $transport->send ( $message );
				}
			}
		} catch ( \Exception $e ) {echo $e->getMessage ();
			\De\Log::logApplicationInfo ( "Caught Exception: " . $e->getMessage () . ' -- File: ' . __FILE__ . ' Line: ' . __LINE__ );
		}
	}*/
	
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
			
			$content->setParts(array($htmlPart));
	
			$contentPart = new MimePart($content->generateMessage());
			//$contentPart->type = 'multipart/alternative;' . PHP_EOL . ' boundary="' . $content->getMime()->boundary() . '"';
			$contentPart->type = 'text/html';
			
			foreach ($attachments as $thisAttachment) {
				$attachment = new MimePart($thisAttachment['content']);
				$attachment->filename = $thisAttachment['filename'];
				$attachment->type = Mime::TYPE_OCTETSTREAM;
				$attachment->encoding    = Mime::ENCODING_BASE64;
				$attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
			}
	
			$body = new MimeMessage();
			$body->setParts(array($contentPart, $attachment));
	
			$message = new Message();
			$message->setEncoding('utf-8')
			->addTo($emailParams ['toEmail'], $emailParams ['toName'])
			->addFrom($smtpDetails['fromEmail'], $smtpDetails['fromName'])
			->setSubject($emailParams ['subject'])
			->setBody($body);
			
			$options = array (
				'name' => $smtpDetails['name'], 
				'host' => $smtpDetails['smtpserver'], 
				'connection_class' => $smtpDetails['auth'], 
				'port' => $smtpDetails['port'], 
				'connection_config' => array (
					'username' => $smtpDetails['username'],
					'password' => $smtpDetails['password'],
					'ssl' => isset($smtpDetails['ssl']) ? $smtpDetails['ssl'] : ''
				)
			);
	
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