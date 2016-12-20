<html>
<head>
<meta http-equiv="Content-Language" content="en">
<title>RBL check</title>
<meta charset="UTF-8">
<link rel="icon" href="favicon.ico" />
<link rel="stylesheet" type="text/css" href="/include/style.css">
<base target="_blank">
</head>
<body>
<h1>Are my IPs listed?</h1>
<?php
require_once('function.php');
require_once('linkify.php');
$jsonvalues = file_get_contents(__DIR__.'/result.json');
$allresult = json_decode($jsonvalues, true);

$datemod = new DateTime();
$datemod->setTimestamp(filemtime(__DIR__.'/result.json'));

printf('<p style="text-align: right">Updated at %s</p>',$datemod->format('d M Y - H:i:s T') );

foreach ( $allresult as $ip => $result ) {
	if ( empty($result) ) {
        	print "<p>The IP <b>$ip</b> is not listed anywhere.</p>";
        	continue;
	}

	$title = "Listing result for <b>$ip</b>";
	$content = array('List Name','Values','Reason');
	$fcontent = NULL;
	print '<table>';
	printTableHeader($title,$content,TRUE,$fcontent);
	foreach ( $result as $blname => $details ) {
        	if ( $blname == 'score' )
                	continue;
        	$rowspan = count ($details['values']);
        	$listtype = ($details['scores'][0] >0) ? 'statusListed' : 'statusWhiteListed';
        	printf ('<tr><td rowspan="%d">%s</td>', $rowspan, $blname);
        	printf ('<td id="%s">%s</td>', $listtype, $details['values'][0]);
        	printf ('<td rowspan="%d">%s</td></tr>', $rowspan, linkify($details['reason']));
        	for ($i=1; $i<$rowspan; $i++) {
                	$listtype = ($details['scores'][$i] >0) ? 'statusListed' : 'statusWhiteListed';
                	printf ('<tr><td id="%s">%s</td>', $listtype, $details['values'][$i]);
        	}
	}
	print '</table>';
}

?>
</body>
</html>
