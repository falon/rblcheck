#!/usr/bin/php
<?php
openlog('rblcheck',LOG_PID,LOG_MAIL);
require_once('function.php');
$pslist=parse_ini_file('dnsbl.conf', TRUE);
$items=readList($pslist['lists']['list']);
$ips = parse_ini_file('mySMTP.conf');
foreach ( $ips['ip'] as $ip )
	$result["$ip"] = checkList($ip, $items, $alert);
file_put_contents(__DIR__.'/result.json',json_encode($result));

syslog (LOG_INFO, "The check for your SMTP servers has been written.");

if ( $alert ) {
	/* At least one IP is listed. I send a notification mail */
	$opt = parse_ini_file('email.conf', TRUE);
	syslog (LOG_EMERG, "At least one of your IP is blocklisted. See urgently at last report {$opt['others']['link']}");
	require_once('emailfunction.php');
	$message = sprintf("Hello, some of your IPs are blocklisted. I'm sorry, now you must work hard to get IP delisted.\r\n\r\n  See at %s", $opt['others']['link']);
	if ( emailSent ($opt['mail'], $message, $error) )
		syslog (LOG_INFO, $error);
	else	syslog (LOG_ALERT, $error);
}
	
exit(0);
?>
