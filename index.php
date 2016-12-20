<html>
<head>
<meta http-equiv="Content-Language" content="en">
<title>RBL check</title>
<meta charset="UTF-8">
<link rel="icon" href="favicon.ico" />
<link rel="stylesheet" type="text/css" href="/include/style.css">
<script  src="/include/ajaxsbmt.js" type="text/javascript"></script>
<!--Load the Ajax API-->
<base target="_blank">
</head>
<body>
<h1>Postscreen DNSBL Checker</h1>
<form method="POST" name="QueryDef" action="result.php" onSubmit="xmlhttpPost('result.php', 'QueryDef', 'List', '<img src=\'/include/pleasewait.gif\'>', true); return false;">
<table style="float: left">
<?php
	require_once('function.php');
        printTableHeader('RBL Query',array(NULL,NULL),TRUE,'Postscreen DNSBL check');
?>
<tr>
<?php
$ip = getenv('HTTP_CLIENT_IP')?:
getenv('HTTP_X_FORWARDED_FOR')?:
getenv('HTTP_X_FORWARDED')?:
getenv('HTTP_FORWARDED_FOR')?:
getenv('HTTP_FORWARDED')?:
getenv('REMOTE_ADDR');
?>
<td>IP</td><td><input maxlength="255" value="" type="text" name="ip" placeholder="<?php echo $ip; ?>"
                required pattern="^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$"
                title="Look at your syntax. You must insert a valid IPv4 address."
                required>
<input type="submit"
       style="position: absolute; left: -9999px; width: 1px; height: 1px;"
       tabindex="-1" />
</td>
</tr>
</table>
</form>
<div id="List" style="clear:left;"></div>
<p><a href="lookResult.php" target="_blank">Check if your SMTP servers are fine</a>.</p>
<h6>Postscreen DNSBL checker. Postscreen is a module of <a href="http://www.postfix.org" target="_blank">Postfix</a>. HTML5 browser needed. Ver. <?php echo version(); ?></h6>
</body>
</html>
