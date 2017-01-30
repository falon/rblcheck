# rblcheck
Check if IP has blocked with postscreen DNSBL.

- PHP 7 required (tested)
- Move style.css and ajaxsbmt.js in DOCUMENT_ROOT/include dir.
- Move dnsbl.conf-default in dnsbl.conf.
- Edit dnsbl.conf
  - inserting in [lists] the <b><a href="http://www.postfix.org/postconf.5.html#postscreen_dnsbl_sites">postscreen_dnsbl_sites</a></b>, using the same syntax present in main.cf.
  - inserting in [threshold][bl] the <b>postscreen_dnsbl_threshold</b>
  - inserting in [threshold][wl] the <b>postscreen_dnsbl_whitelist_threshold</b>


## Check at your SMTP servers
Are you an Email Administrator, or are you responsible of your own SMTP servers?
If you like you can take advantage from this tool also to check if some of your SMTP servers are blocklisted.
In this case the postscreen scores are ignored and your IPs are simply checked against list names and values.

- Move mySMTP.conf-default in mySMTP.conf.
  - insert here your SMTP servers IPs.
- Move email.conf-default in email.conf.
  - customize the alert email you receive if some of your SMTP server are blocklisted.
- Schedule computeMySMTP.php
  - You can configure a systemd service/timers as
	`/usr/lib/systemd/system/rblcheck.service`:
	```
	### SMTP Servers RBL Check ###
	#
	
	[Unit]
	Description=RBL check for your IPs
	After=syslog.target network.target

	[Service]
	User=root
	ExecStart=/var/www/html/postmaster/rblcheck/computeMySMTP.php
	```

	`/usr/lib/systemd/system/rblcheck.timer`:
	```
	### SMTP Servers RBL Check ######
	#
	
	[Unit]
	Description=RBL check for your IPs
	After=syslog.target network.target
	
	[Timer]
	OnCalendar=daily
	RandomizedDelaySec = 7200
	
	[Install]
	WantedBy=multi-user.target
	```

- See at http(s)://[...]/lookResult.php



Enjoy!
