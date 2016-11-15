<?php
require_once('function.php');
$pslist=parse_ini_file('dnsbl.conf', TRUE);
$items=readlist($pslist['lists']['list']);
$ip = $_POST['ip'];
$result = checkList($ip, $items);

if ( empty($result) ) {
        print "<p>The IP <b>$ip</b> is not listed.</p>";
        exit();
}

$status = 'Not listed';
$listtype = NULL;
if ( $result['score'] >= $pslist['threshold']['bl'] ) {
	$listtype = 'statusListed';
	$status = 'This ip is currently blocklisted';
}
if ( $result['score'] <= $pslist['threshold']['wl'] ) {
        $listtype = 'statusWhiteListed';
	$status = 'This ip is currently whitelisted';
}


$title = "Listing result for <b>$ip</b>";
$content = array('List Name','Values','Scores','Score','Reason');
$fcontent = sprintf('<div id="%s">RANK: %s : %s.',$listtype, $result['score'], $status);
print '<table>';
printTableHeader($title,$content,TRUE,$fcontent);
foreach ( $result as $blname => $details ) {
	if ( $blname == 'score' )
		continue;
	$rowspan = count ($details['values']);
	$listtype = ($details['scores'][0] >0) ? 'statusListed' : 'statusWhiteListed';
	printf ('<tr><td rowspan="%d">%s</td>', $rowspan, $blname);
	printf ('<td id="%s">%s</td><td id="%s">%s</td>', $listtype, $details['values'][0], $listtype, $details['scores'][0]);
        printf ('<td rowspan="%d">%s</td>', $rowspan, $details['score']);
        printf ('<td rowspan="%d">%s</td></tr>', $rowspan, linkify($details['reason']));
	for ($i=1; $i<$rowspan; $i++) {
		$listtype = ($details['scores'][$i] >0) ? 'statusListed' : 'statusWhiteListed';
		printf ('<tr><td id="%s">%s</td>', $listtype, $details['values'][$i]);
		printf ('<td id="%s">%s</td></tr>', $listtype, $details['scores'][$i]);
	}
}
print '</table>';
?>
