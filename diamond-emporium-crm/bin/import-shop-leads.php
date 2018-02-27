#!/usr/bin/php
<?php

/*
 * TODO: pull this from the other config file.
 */
$DSN = 'mysql:dbname=openseedcrm;host=diamondemporium.cqgxfzfvcv8o.ap-southeast-2.rds.amazonaws.com';
$USR = 'openseedcrm';
$PWD = 'hfd7620odhs';

try {
	$dbh = new PDO ( $DSN, $USR, $PWD );
	// set the PDO error mode to exception
	$dbh->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	echo "Connected successfully\n";
} catch ( PDOException $e ) {
	die ($e->getMessage () . "\n");
}

try {
	$default_lead_owner = array_shift($dbh->query("select value from de_variable where name = 'shop.webform.default.lead.owner'")->fetch(PDO::FETCH_ASSOC));
} catch ( PDOException $e ) {
	die ($e->getMessage () . "\n");
}

echo "Default lead owner is $default_lead_owner\n";

try {
	$last_sid = array_shift($dbh->query("select value from de_variable where name = 'shop.webform.last.sid'")->fetch(PDO::FETCH_ASSOC));
} catch ( PDOException $e ) {
	die ($e->getMessage () . "\n");
}

echo "Last SID is $last_sid\n";

$select_sql = <<<EOT
select ws.sid,
wd_title.data as title,
wd_first.data as first_name,
wd_last.data as last_name,
wd_mobile.data as mobile,
wd_email.data as email,
wd_postcode.data as postcode,
wd_heard.data as heard,
wd_subject.data as subject,
wd_message.data as message,
wd_ref_product.data as ref_product,
null as state,
ws.submitted as created_date
FROM
diamondemporium.webform_submissions ws
left join diamondemporium.webform_submitted_data wd_title on wd_title.sid = ws.sid and wd_title.cid = 15
left join diamondemporium.webform_submitted_data wd_first on wd_first.sid = ws.sid and wd_first.cid = 1
left join diamondemporium.webform_submitted_data wd_last on wd_last.sid = ws.sid and wd_last.cid = 13
left join diamondemporium.webform_submitted_data wd_mobile on wd_mobile.sid = ws.sid and wd_mobile.cid = 14
left join diamondemporium.webform_submitted_data wd_email on wd_email.sid = ws.sid and wd_email.cid = 2
left join diamondemporium.webform_submitted_data wd_postcode on wd_postcode.sid = ws.sid and wd_postcode.cid = 6
left join diamondemporium.webform_submitted_data wd_heard on wd_heard.sid = ws.sid and wd_heard.cid = 7
left join diamondemporium.webform_submitted_data wd_subject on wd_subject.sid = ws.sid and wd_subject.cid = 9
left join diamondemporium.webform_submitted_data wd_message on wd_message.sid = ws.sid and wd_message.cid = 10
left join diamondemporium.webform_submitted_data wd_ref_product on wd_ref_product.sid = ws.sid and wd_ref_product.cid = 16
WHERE ws.nid = 106
AND ws.sid > ?
ORDER BY ws.sid
EOT;

$to_insert = array ();
try {
	$select = $dbh->prepare($select_sql);
	if ($select->execute(array($last_sid))) {
		while($row = $select->fetch(PDO::FETCH_ASSOC)) {
			$row['state'] = derive_state($row['postcode']);
			$row['heard'] = derive_how_heard($row['heard']);
			$row['subject'] = derive_product($row['subject']);
			$row['created_date'] = adjust_to_localtime($row['created_date']); 
			$to_insert[] = $row;
		}
	}
} catch (PDOException $e) {
	die ($e->getMessage () . "\n");
}

echo "Leads to insert: " . count($to_insert) . "\n";

$insert_sql = <<<EOT
insert into de_leads (
title, first_name, last_name, mobile, email, postcode, how_heard, product, looking_for, reference_product, state,
lead_source, lead_status, created_date, created_by,
lead_owner)
values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Web Form', 'Open', ?, 1, ?);
EOT;

try {
	$insert = $dbh->prepare($insert_sql);
	$update = $dbh->prepare("update de_variable set value = ? where name = 'shop.webform.last.sid'");
	foreach($to_insert as $row) {
		$sid = array_shift($row);
		$row[] = $default_lead_owner;
		$insert->execute(array_values($row));
		echo "Inserted $sid\n";
		$update->execute(array($sid));
	}
} catch (PDOException $e) {
	die ($e->getMessage () . "\n");
}

$dbh = null;

/*
 * Postcode derivation logic.
 * See: https://en.wikipedia.org/wiki/Postcodes_in_Australia
 *
 * CRM codes for states are:
 * '1', 'New South Wales', 'NSW'
 * '2', 'Queensland', 'QLD'
 * '3', 'South Australia', 'SA'
 * '4', 'Tasmania', 'TAS'
 * '5', 'Victoria', 'VIC'
 * '6', 'Western Australia', 'WA'
 * '7', 'Northern Territory', 'NT'
 * '8', 'Australian Capital Territory', 'ACT'
 */
function derive_state($postcode) {
	/*
	 * 'default' state is NSW.
	 * TODO: this probably isn't a good idea, but without this
	 * data with bad postcode doesn't get through.
	 */
	$state = 1;
	
	/* NSW */
	if ($postcode >= 1000 && $postcode <= 1999) $state = 1;
	if ($postcode >= 2000 && $postcode <= 2599) $state = 1;
	if ($postcode >= 2619 && $postcode <= 2899) $state = 1;
	if ($postcode >= 2921 && $postcode <= 2999) $state = 1;

	/* ACT */
	if ($postcode >= 0200 && $postcode <= 0299) $state = 8;
	if ($postcode >= 2600 && $postcode <= 2618) $state = 8;
	if ($postcode >= 2900 && $postcode <= 2920) $state = 8;
	
	/* VIC */
	if ($postcode >= 3000 && $postcode <= 3999) $state = 5;
	if ($postcode >= 8000 && $postcode <= 8999) $state = 5;

	/* QLD */
	if ($postcode >= 4000 && $postcode <= 4999) $state = 2;
	if ($postcode >= 9000 && $postcode <= 9999) $state = 2;
	
	/* SA */
	if ($postcode >= 5000 && $postcode <= 5799) $state = 3;
	if ($postcode >= 5800 && $postcode <= 5999) $state = 3;
	
	/* WA */
	if ($postcode >= 6000 && $postcode <= 6797) $state = 6;
	if ($postcode >= 6800 && $postcode <= 6999) $state = 6;
	
	/* TAS */
	if ($postcode >= 7000 && $postcode <= 7799) $state = 4;
	if ($postcode >= 7800 && $postcode <= 7999) $state = 4;
	
	/* NT */
	if ($postcode >= 0800 && $postcode <= 0899) $state = 7;
	if ($postcode >= 0900 && $postcode <= 0999) $state = 7;
	
	/*
	 * TODO: this is not currently exhaustive, but close enough
	 */

	return $state;
}

/*
 * Convert 'How heard'.
 * 
 * CRM values are:
 * '1', 'Google'
 * '2', 'Word of mouth'
 * '3', 'Facebook'
 * '4', 'The Knot'
 * '5', 'Returning Customer'
 * '6', 'Business Review Weekly'
 * '7', 'Australian Financial Review'
 * '8', 'Instagram'
 * '9', 'Sydney Morning Herald'
 * '10', 'Other'
 */

function derive_how_heard($how_heard) {
	switch($how_heard) {
		case 'Google': return 1;
		case 'Word of mouth': return 2;
		case 'Facebook': return 3;
		case 'The Knot': return 4;
		case 'Returning Customer': return 5;
		case 'Business Review Weekly': return 6;
		case 'Australian Financial Review': return 7;
		case 'Instagram': return 8;
		case 'Sydney Morning Herald': return 9;		
	}
	return 10;
}

/*
 * Convert 'subject' -> 'product'.
 * 
 * CRM values are:
 * '1', 'Engagement Ring'
 * '2', 'Wedding Band'
 * '3', 'Eternity Band'
 * '4', 'Loose Diamond'
 * '5', 'Earrings'
 * '6', 'Dress Rings'
 * '7', 'Pendant'
 * '8', 'Bracelet'
 * '9', 'Timepiece'
 * '10', 'Custom Jewellery'
 * '11', 'Loose Gemstone'
 * '12', 'Other'
 */

function derive_product($subject) {
	switch($subject) {
		case 'Engagement Ring': return 1;
		case 'Loose Diamond': return 4;
		case 'Wedding Ring': return 2;
		case 'Rings': return 6;
		case 'Earrings': return 5;
		case 'Pendants': return 7;
		case 'Bracelets': return 8;
		case 'Custom Jewellery': return 10;
		case 'Timepieces': return 9;
	}
	return 12;
}

/*
 * Adjust times from Drupal (UTC) to those suitable for DB insert.
 */

function adjust_to_localtime($utc) {
	$date = new DateTime("now", new DateTimeZone('Australia/NSW'));
	$date->settimestamp($utc);
	
	return $date->format('Y-m-d H:i:s');
}
