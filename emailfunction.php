<?php
function emailSent ($param, $message, &$error) {
/*
	$param['to'] - recipient commas separated
	$param['mailfrom'] - sender envelope address
	$param['from'] - sender From header
	$param['replyto'] - Reply-To header
	$param['subject'] - Subject
	$param['prio'] - Importance header 	*/

	if ( empty($param['to']) ) {
		$error = 'No mail sent, because no recipient set.';
		return FALSE;
	}
        $date = new DateTime();
        $now = $date->getTimestamp();
        $today = $date->format('r');
        $uniqid = md5(uniqid($now,1));

        $header="From: {$param['from']}\r\n".
                "Reply-To: {$param['replyto']}\r\n".
                "Importance: {$param['prio']}\r\n".
                "MIME-Version:1.0\r\n".
                "Message-ID: <$uniqid@".gethostname().">\r\n".
                "Date: $today\r\n".
                "Content-Type: text/plain; charset=utf-8\r\n".
                "Content-Transfer-Encoding: 7bit\r\n".
                "X-Mailer: PHP/" . phpversion()."\r\n";

        /* Mgs */
        $message = wordwrap ( $message, 75 , "\r\n" ); /* Ensure the line is less than 76 chars */

        if ( strlen(ini_get("safe_mode"))< 1) {
                $old_mailfrom = ini_get("sendmail_from");
                ini_set("sendmail_from", $param['mailfrom']);
                $parameter = sprintf("-oi -f %s", $param['mailfrom']);
                $result = (mail($param['to'], $param['subject'], $message,$header,$parameter));
                if (isset($old_mailfrom))
                        ini_set("sendmail_from", $old_mailfrom);
        }
        else 
                $result = (mail($param['to'], $param['subject'], $message,$header));
	if ( $result )
		$error= sprintf('Mail successfully sent to <%s>', $param['to']);
	else	$error= sprintf('Error writing to <%s>', $param['to']);
        return $result;
}
