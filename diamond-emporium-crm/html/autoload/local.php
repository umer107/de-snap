<?php
/**
 * Local Configuration Override
 *
 * This configuration override file is for overriding environment-specific and
 * security-sensitive configuration information. Copy this file without the
 * .dist extension at the end and populate values as needed.
 *
 * @NOTE: This file is ignored from Git by default with the .gitignore included
 * in ZendSkeletonApplication. This is a good practice, as it prevents sensitive
 * credentials from accidentally being committed into version control.
 */

return array(
	'db' => array(
		'username' => 'openseedcrm',
		'password' => 'hfd7620odhs',
	),
	'smtp_details' => array(
		'smtpserver' => 'email-smtp.us-west-2.amazonaws.com',
		'auth' => 'login',
		'username' => 'AKIAIDPBTC2AJ2ITWKBQ',
		'password' => 'AkJq13Y6XxZ2dAEnmkGy9HKF1cgLkds6F3XN2eQa7d1i',
		'port' => '587',
		'ssl' => 'tls',
		'name' => 'DE-Administrator',
		'fromEmail' => 'gus@diamondemporium.com.au',
		'fromName' => 'DE-Administrator',
		//'ccEmail' => 'info@diamondemporium.com.au',
		'ccEmail' => 'phoenix.diamondemporium@yahoo.com.au',
		'ccName' => 'DE-Administrator',
	),
	'imap_details' => array(
		'imapserver' => 'imap.mail.yahoo.com',
		'username' => 'phoenix.diamondemporium@yahoo.com.au',
		'password' => 'diamond@123',
		'encryption' => 'ssl',
	),
	'dbPrefix' => 'de_',
	'recordsPerPage' => 20,
	'jsDateFormat' => 'dd/mm/yy',
	'phpDateFormat' => 'd/m/y',
	'phpDateTimeFormat' => 'd/m/y H:i',
	'formDateFormat' => 'd/m/Y',
	'formDateTimeFormat' => 'd/m/Y H:i',
	'documentRoot' => $_SERVER['DOCUMENT_ROOT'].'/public/',
	'inventory_items' => array(
		'diamond' => 'Diamond',
		'weddingring' => 'Wedding Ring',
		'engring' => 'Engagement Ring',
		'earring' => 'Earring',
		'pendant' => 'Pendant',
		'chain' => 'Chain',
		'misc' => 'Miscellaneous',
	),
	'milestones' => array(
		'1' => 'CAD',
		'2' => 'Prototype',
		'3' => 'Cast. Man',
		'4' => 'Workshop',
	),
	'job_types' => array(
		'1' => 'CAD Only',
		'2' => 'CAD & Render',
		'3' => 'Render Only',
	),
	'milestones_steps' => array(
		'1' => array('1' => 'Cad Requirements', '2' => 'Upload Approved Files', '3' => 'Set Client Review'),
		'2' => array('1' => 'Prototype Requirements', '2' => 'Client Review'),
		'3' => array('1' => 'Create a Manufacturing Job', '2' => 'DE Review'),
		'4' => array('1' => 'Create Production Line', '2' => 'DE Quality Control', '3' => 'Client Review'),
	),
);

