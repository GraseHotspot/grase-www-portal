#!/usr/bin/php -q
<?php

/* Copyright 2008 Timothy White */

/*
external_acl_type IPUser ttl=300 %SRC radmin/squid_ip_user.php

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

require_once __DIR__ . '/../../../vendor/autoload.php';

$NONINTERACTIVE_SCRIPT = true;



$fp = fopen('php://stdin', 'r');
while ($IP = trim(fgets($fp, 4096))) {
//	echo "$IP ".database_radacct_ip_to_username($IP)."\n";
    // TODO: See about converting this back to DB lookup
    //$username = chilli_ip_to_username($IP);
    $username = DatabaseFunctions::getInstance()->activeSessionUsername($IP);
    if ($username != "ERR" && $username) {
        print "OK user=$username\n";
    } else {
        //print "OK\n";
        print "ERR\n";
    }
}

// Old function. Hopefully DatabaseFunctions will do this for us now
function chilli_ip_to_username($IP)
{
    $current_sessions = `chilli_query list`;
    $current_sessions = split("\n", $current_sessions);
    foreach ($current_sessions as $session) {
        list($MAC_Address, $IP_Address, $InternalState, $SessionID, $AuthenticatedState, $Username, $Duration, $Idle, $URL) = split(" ", $session);
        if ($IP_Address == $IP && $AuthenticatedState == '1') {
            return $Username;
        }
    }
    return "";
}
