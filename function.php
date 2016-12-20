<?php

function version() {
	return "1.0";
}

function easyCheck($range, $value) {
/* Perform simple comparison if you don't require a postscreen regex */
	if ( strpos($range,'[') === FALSE ) {
		if ($range == $value) return TRUE;
		else return FALSE;
	}
	return NULL;
}



function ipInRange($iprange, $ip) {
/* Check Postscreen range */
	/* Shortcut */
	if ( !is_null( $check = easyCheck($iprange, $ip) ) )
		return $check;
	/* Hard work */	
	$re = '/(?:(?P<part>\d{1,3})|(?P<part2>\[\d{1,3}(?:\.{2}|\;)\d{1,3}\]))/';
	if ( preg_match_all($re, $iprange, $matches) === FALSE )
		return FALSE;
	$rangevet = $matches[0];
	$ipvet=explode('.',$ip);
	if ( count($rangevet) != 4 ) return FALSE;
	if ( count($ipvet) != 4 ) return FALSE;
	$inrange = FALSE;
	for ($i=0;$i<4;$i++) {
		if ( easyCheck($rangevet[$i], $ipvet[$i]) )
                	$inrange = TRUE;
		else {
			if ( preg_match('/\[(?P<start>\d{1,3})(?:\.\.|\;)(?P<stop>\d{1,3})\]/',$rangevet[$i],$opt) === 1 ) {
				if ( strpos($rangevet[$i], '..') !== FALSE ) {
					if ( ($ipvet[$i] <= $opt['stop']) AND ($ipvet[$i] >= $opt['start']) )
						$inrange = TRUE;
					else
						$inrange = FALSE;
				}
				else
					if ( strpos($rangevet[$i], ';') ) {
						if (( $ipvet[$i] == $opt['start'] ) OR ( $ipvet[$i] == $opt['stop'] ))
							$inrange = TRUE;
						else
							$inrange = FALSE;
					}
			}
		}
		if (!$inrange)
			return FALSE;
	}
	return TRUE;
}		


function readList($list) {
/* Return ordered list postscreen DNSBL array, FALSE on parse error */
	$regexp= '/(?P<name>[\.\w]+)(?:\=(?P<value>[\d+\.\[\;\]]+)){0,1}(?:\*(?P<score>(\-){0,1}\d+)){0,1}/';
	$dnsbl= array();
	foreach ( $list as $item ) {
		if ( preg_match($regexp,$item,$found)=== FALSE )
			return FALSE;
		$err= preg_last_error();
		if ($err != PREG_NO_ERROR) 
			syslog(LOG_ERR,"Error reading line <$item>: $err");
		if ( empty($found['score']) )
			$found['score'] = 1;
		$dnsbl[] = array('name' => $found['name'], 'value' => $found['value'], 'score' => $found['score']);
	}
	return $dnsbl;
}


function update(&$array,$value, $score) {
/* Used only in checkList */
	$array['values'][] = $value;
	$array['scores'][] = $score;
	return TRUE;
}



function checkList($ip, $list, &$alarm=FALSE) {

	$return=array();
	if ( !filter_var($ip, FILTER_VALIDATE_IP, array('flags'=> FILTER_FLAG_IPV4)) )
		return $return;

	require_once 'vendor/autoload.php';
	$dnsbl = new \DNSBL\DNSBL(array(
		'blacklists' => array_column($list, 'name')
	));
	$alarm = FALSE; /* Will be TRUE if ANY is blocklisted somewhere */
	if ( $dnsbl->isListed($ip, TRUE) )  {
		$return['score'] = 0;
   		$result = $dnsbl->getDetails($ip, TRUE);
		foreach ( $result as $blname => $detail ) {
			$keys=array_keys(array_column($list,'name'),$blname);
			foreach ($keys as $key) {
				$update = FALSE;
				foreach ( $detail as $this ) {
					if ( isset($this['ip']) ) { /* Is listed with value $this['ip']! */
						if ( empty($list[$key]['value']) ) 
							$update = update($return["$blname"],$this['ip'],$list[$key]['score']);
						else {
							if (ipInRange($list[$key]['value'],$this['ip']))
								$update = update($return["$blname"],$this['ip'],$list[$key]['score']);
						}
					}
					if ( $update ) { /* The reason and the name */
						$return["$blname"]['name'] = $list[$key]['name'];
						if  ( (!$alarm) AND ($list[$key]['score']>0) )
							$alarm = TRUE;
						if ( isset($this['txt']) )
							$return["$blname"]['reason'] = $this['txt'];
					}
				}
				if ( $update ) {
					if ( !isset($return["$blname"]['score']) )
						$return["$blname"]['score'] = $list[$key]['score'];
					else
						$return["$blname"]['score'] += $list[$key]['score'];
					$return['score'] += $list[$key]['score'];
				}
			}
		}
	}
	return $return;
}		




/* WEB */
function printTableHeader($title,$content,$footer=FALSE,$fcontent) {
        print <<<END
<caption>$title</caption>
<thead>
<tr>
END;
        $cols = count($content);
        for ($i=0; $i<$cols; $i++)
                print '<th>'.$content[$i].'</th>';
        print '</tr></thead>';
        if ($footer) {
                print '<tfoot><tr>';
                print "<th colspan=\"$cols\">".$fcontent.'</th>';
                print '</tr></tfoot>';
        }
        return TRUE;
}


?>
