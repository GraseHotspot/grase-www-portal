#!/usr/bin/php -q
<?php

/* Copyright 2008 Timothy White */

/*
external_acl_type IPUser ttl=300 %SRC /home/ywamadmin/public_html/radmin/squid_ip_user.php

# Hosts & domains that are denied to restricted users
acl Banned_Hosts dst "/etc/squid/banned_hosts"
#acl Banned_Domains dstdomain "/etc/squid/banned_domains.txt"
#acl Banned_URLs url_regex "/etc/squid/banned_urls.txt"
#acl Banned_Extensions url_regex "/etc/squid/banned_extensions.txt"


# Seemless automatic access based on IP address
# Access through the "IP User" external helper
acl Auth_User external IPUser

http_access allow Auth_User
http_access deny Banned_Hosts

00-C0-26-2F-A7-91-dev
*/


chdir(__DIR__ . '/../');

function __autoload($class_name) {
    require_once './classes/' . $class_name . '.class.php';
}

$NONINTERACTIVE_SCRIPT = TRUE;
require_once 'includes/database_functions.inc.php';

//require_once("database_functions.inc.php");


$fp = fopen('php://stdin', 'r');
while($IP = trim(fgets($fp, 4096))){
//	echo "$IP ".database_radacct_ip_to_username($IP)."\n";
    // TODO: See about converting this back to DB lookup
	//$username = chilli_ip_to_username($IP);
	$username = DatabaseFunctions::getInstance()->activeSessionUsername($IP);
	if($username != "ERR" && $username){
		print "OK user=$username\n";
	}else{
		//print "OK\n";
		print "ERR\n";
	}
}

// Old function. Hopefully DatabaseFunctions will do this for us now
function chilli_ip_to_username($IP){
	$current_sessions = `chilli_query list`;
	$current_sessions = split("\n", $current_sessions);
	foreach($current_sessions as $session){
		list($MAC_Address, $IP_Address, $InternalState, $SessionID, $AuthenticatedState, $Username, $Duration, $Idle, $URL) = split(" ", $session);
		if($IP_Address == $IP && $AuthenticatedState == '1') return $Username;
	}
	return "";
}

//$ sudo chilli_query list
//00-1A-73-C9-44-4B 10.1.0.3 dnat 49796f6c00000001 0 00-1A-73-C9-44-4B-dev 0/0 0/0 -
//$ sudo chilli_query list
//00-1A-73-C9-44-4B 10.1.0.3 pass 49796f6c00000001 1 office 5/0 5/0 -
/*

MAC Address, IP Address, internal chilli state (dnat, pass, etc), the session id (used in Acct-Session-ID), authenticated status (1 authorized, 0 not), user-name used during login, duration / max duration, idle time / max idle time, and the original URL. 

$ sudo radwho -r
00-07-95-C7-54-55-dev,00-07-95-C7-54-55-dev,shell,S2,Fri 09:41,10.1.0.1,10.1.0.5
mella,mella,shell,S3,Fri 11:59,10.1.0.1,10.1.0.7
00-01-29-21-E8-2E-dev,00-01-29-21-E8-2E-dev,shell,S4,Fri 09:55,10.1.0.1,10.1.0.8

$ sudo chilli_query list
00-C0-26-2F-A7-91 10.1.0.9 dnat 4979999b00000005 0 00-C0-26-2F-A7-91-dev 0/0 0/0 -
00-01-29-21-E8-2E 10.1.0.8 pass 4979780200000004 1 00-01-29-21-E8-2E-dev 9461/0 88/900 -
00-90-27-C2-5C-97 10.1.0.7 dnat 49799cca00000003 0 mella 0/0 0/0 http://www.yahoo.com/
00-07-95-C7-54-55 10.1.0.5 pass 497974a700000002 1 00-07-95-C7-54-55-dev 10320/0 107/900 -
00-1A-73-C9-44-4B 10.1.0.4 dnat 497972e000000001 0 00-1A-73-C9-44-4B-dev 0/0 0/0 -

00-C0-26-2F-A7-91 10.1.0.9 dnat 4979999b00000005 0 00-C0-26-2F-A7-91-dev 0/0 0/0 http://archive.ubuntu.com/ubuntu/project/ubuntu-archive-keyring.gpg
00-01-29-21-E8-2E 10.1.0.8 pass 4979780200000004 1 00-01-29-21-E8-2E-dev 11227/0 71/900 -
00-90-27-C2-5C-97 10.1.0.7 pass 49799cca00000003 1 veronika 359/15505386 27/900 http://www.fnb.co.za/
00-07-95-C7-54-55 10.1.0.5 pass 497974a700000002 1 00-07-95-C7-54-55-dev 12086/0 20/900 -
00-1A-73-C9-44-4B 10.1.0.4 dnat 497972e000000001 0 00-1A-73-C9-44-4B-dev 0/0 0/0 -

$ sudo radwho
Login      Name              What  TTY  When      From      Location
00-07-95-C 00-07-95-C7-54-55 shell S2   Fri 09:41 10.1.0.1  10.1.0.5
veronika   veronika          shell S3   Fri 12:56 10.1.0.1  10.1.0.7
00-01-29-2 00-01-29-21-E8-2E shell S4   Fri 09:55 10.1.0.1  10.1.0.8

radmin@ywamserver:~$ chilli_query list
00-C0-26-2F-A7-91 10.1.0.9 pass 4979999b00000005 1 khaya 350/2616422 3/900 http://1.1.1.1/
00-01-29-21-E8-2E 10.1.0.8 pass 4979780200000004 1 00-01-29-21-E8-2E-dev 26582/0 345/900 -
00-90-27-C2-5C-97 10.1.0.7 pass 49799cca00000003 1 veronika 15714/15505386 361/900 http://www.fnb.co.za/
00-07-95-C7-54-55 10.1.0.5 pass 497974a700000002 1 00-07-95-C7-54-55-dev 27441/0 369/900 -
radmin@ywamserver:~$ sudo radwho
Login      Name              What  TTY  When      From      Location
00-07-95-C 00-07-95-C7-54-55 shell S2   Fri 09:41 10.1.0.1  10.1.0.5
veronika   veronika          shell S3   Fri 12:56 10.1.0.1  10.1.0.7
00-01-29-2 00-01-29-21-E8-2E shell S4   Fri 09:55 10.1.0.1  10.1.0.8
khaya      khaya             shell S5   Fri 17:12 10.1.0.1  10.1.0.9



*/

?>

