# rblcheck
Check if IP has blocked with postscreen DNSBL.

- Move dnsbl.conf-default in dnsbl.conf.
- Edit dnsbl.conf
  - inserting in [lists] the <b><a href="http://www.postfix.org/postconf.5.html#postscreen_dnsbl_sites">postscreen_dnsbl_sites</a></b>, using the same syntax present in main.cf.
  - inserting in [threshold][bl] the <b>postscreen_dnsbl_threshold</b>
  - inserting in [threshold][wl] the <b>postscreen_dnsbl_whitelist_threshold</b>

Enjoy!
