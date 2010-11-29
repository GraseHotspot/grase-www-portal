#!/usr/bin/php -q
<?php

/* Copyright 2008 Timothy White */

/*
external_acl_type IPUser ttl=300 %SRC /var/www/radmin/scripts/squid_ip_user.php
external_acl_type UserGroup ttl=300 %EXT_USER /var/www/radmin/scripts/squid_user_group.php

# Hosts & domains that are denied to restricted users
acl Banned_Hosts dstdomain "/etc/squid/banned_hosts"
#acl Banned_Domains dstdomain "/etc/squid/banned_domains.txt"
#acl Banned_URLs url_regex "/etc/squid/banned_urls.txt"
#acl Banned_Extensions url_regex "/etc/squid/banned_extensions.txt"
acl Ministry_Banned_Domains dstdomain "/etc/squid/banned_domains_ministry"
acl Ministry_Banned_Regex_Domains dstdom_regex -i photos-.\.ak\.fbcdn\.net
acl Ministry_Banned_Regex_Domains dstdom_regex -i photos-.\.ll\.facebook\.com
acl Ministry_Banned_Regex_Domains dstdom_regex -i photos.\.hi5\.com


# Seemless automatic access based on IP address
# Access through the "IP User" external helper
acl Auth_User external IPUser
acl Staff external UserGroup Staff
acl Ministry external UserGroup Ministry

http_access allow Auth_User !Ministry
http_access deny Banned_Hosts
#http_access allow Ministry !Ministry_Banned_Domains !Ministry_Banned_Regex_Domains
http_access deny Ministry Ministry_Banned_Domains
http_access deny Ministry Ministry_Banned_Regex_Domains
http_access allow Auth_User Ministry

*/

chdir(dirname(__FILE__) . '/../');

require_once(dirname(__FILE__) . '/../includes/database_functions.inc.php');

//require_once("../includes/database_functions.inc.php");
$group = trim($argv[1]);

$fp = fopen('php://stdin', 'r');
while($data = trim(fgets($fp, 4096))){
//	echo "$IP ".convertRadacctIPtoUsername($IP)."\n";
	list($Username, $group) = split(" ", $data, 2);
	$usergroup=trim(getDBUserGroup($Username));
	if($usergroup == $group && $usergroup != ""){
		print "OK\n";
	}else{
		//print "OK\n";
		print "ERR message='User Group not permitted to access this site'\n";
	}
	file_put_contents("/tmp/usergroup", "$Username, $group, $usergroup\n", FILE_APPEND);
}


?>

