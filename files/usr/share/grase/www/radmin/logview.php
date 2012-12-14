<?php

/* Copyright 2008 Timothy White */

// perl -ne ' print if ( $_ ge "2008.3.20 23:11:24" && $_ le "2008.3.21 00:30" ) ' <access.log.4
//  perl -ne ' print if ( $_ ge "2008.3.20 23:11:24" && $_ le "2008.3.21 00:30" && /10.1.0.2/ ) ' <access.log.4|sed -r 's/(.*)\?[^\s]*(.*)/\1\2/'|grep POST
// $_GET['acctid']
// $_GET['starttime']
// $_GET['finishtime']
// $_GET['ipaddress']

$PAGE = 'sessions';
require_once 'includes/pageaccess.inc.php';

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';

$domain_tally = array();
$domain_size = array();
$domain_formatsize = array();
$format_http_traffic_size = "";

// TODO: Create class that handles all the log processing

function clean_dansguardian_log_array($loglines)
{
	foreach($loglines as $line)
	{
		set_time_limit(2);
		list($timestamp, $line) = explode(" - ", $line, 2);
		list($ip, $address, $params) = explode(" ", $line, 3);
		preg_match('@^(?:http://)?(www.|)([^/]*)(.*)@i', $address, $matches);
		$host = $matches[2];
		$query = $matches[3];		
		preg_match('@^(\*CACHED\*|)\s*([^\s]*)\s*(\d*)@i', $params, $matches);
		$cached = $matches[1];
		$action = $matches[2];
		$size = $matches[3];
		tally_domains($host, $size);
		tally_http_traffic($size);				
		$log[] = array("timestamp" => $timestamp, "URL" => $address, "host" => $host, "cached" => $cached, "request" => $action, "size" => Formatting::formatBytes($size));
	}
	return $log;
}

function clean_squid_log_array($loglines)
{
    $log = array();
	foreach($loglines as $line)
	{
	    // Processing log file can take time, give us 2seconds per line
		set_time_limit(2);
// "%9d.%03d %6d %s %s/%03d %d %s %s %s %s%s/%s %s"
// time elapsed remotehost code/status bytes method URL rfc931 peerstatus/peerhost type
		$timestamp = trim(substr($line, 0, 14));
		$elapsed = trim(substr($line, 14, 6));
		$restofline = trim(substr($line, 21));
		list($clientip, $status, $bytes, $method, $URL, $username, $peer, $type) = explode(" ", $restofline, 8);
		preg_match('@^(?:http://)?(www.|)([^/]*)(.*)@i', $URL, $matches);
		$host = $matches[2];
		$query = $matches[3];		
		tally_domains($host, $bytes);
		tally_http_traffic($bytes);		
		$log[] = array("timestamp" => date('Y-m-d H:i:s',$timestamp), "URL" => $URL,"address" => $clientip, "username"=> $username, "host" => $host, "cached" => '', "request" => $method, "size" => Formatting::formatBytes($bytes));
	}
	
	// The time consuming part is done, no more than 5s should be needed now
	set_time_limit(5);	
	return $log;
}

function tally_domains($domain, $size)
{
	global $domain_tally, $domain_size, $domain_formatsize;
	if(array_key_exists($domain, $domain_tally))
	{
		$domain_tally[$domain] ++;
		$domain_size[$domain] += $size;		
	}else
	{
	 	$domain_tally[$domain] = 1;
		$domain_size[$domain] = $size;			 	
	}
	$domain_formatsize[$domain] = Formatting::formatBytes($domain_size[$domain]);
}

function tally_http_traffic($size)
{
	global $http_traffic_size, $format_http_traffic_size;
	$http_traffic_size = $http_traffic_size + $size;
	$format_http_traffic_size = Formatting::formatBytes($http_traffic_size);
}

function format_date($datestr)
{
	// Log format is YYYY.M.DD HH:MM:SS
	list($date, $time) = explode(" ", $datestr);
	list($year, $month, $day) = explode("-", $date);
	$year = intval($year);
	$month = intval($month);
	$day = intval($day);
	list($hour, $min, $sec) = explode(":", $time);
	$hour = intval($hour);
	$time = "$hour:$min:$sec";
	return "$year.$month.$day $time";
}

function format_unixtime($datestr)
{
    // Log format is unix time with milliseconds
    return strtotime($datestr);

}

function build_perl_command($conditions)
{
    $perlcommand = "perl -ne ' print if ( %s )'";
    $perlargs = array();
    
    $startarg = ' $_ ge "%s" ';
    $finisharg = ' $_ le "%s" ';
    $iparg = ' /%s/ ';
    
    foreach($conditions as $condition => $value)
    {
        switch ($condition)
        {
            case "starttime":
                $perlargs[] = sprintf($startarg , $value);
                break;
            case "finishtime":
                $perlargs[] = sprintf($finisharg , $value);            
                break;
            case "ipaddress":
                $perlargs[] = sprintf($iparg , $value);            
                break;       
        }
    
    }
    
    $completeperlcommand = sprintf($perlcommand, implode(' && ', $perlargs));
    
    return $completeperlcommand;
}


/* Start Page Logic */

	if(trim($_GET['acctid']) != '')
	{

		$session = getDBSessionAccounting($_GET['acctid']);
		//print_r($session);


        // Build up components for perl matching command
		$conditions['starttime'] =  escapeshellcmd(format_unixtime($session['AcctStartTime']));		
		$finishtime =  escapeshellcmd(format_unixtime($session['AcctStopTime']));		

		if($finishtime != "0.0.0 0:00:00" && $finishtime != '')
		{
		    $conditions['finishtime'] = $finishtime;
		}

		$conditions['ipaddress'] = escapeshellcmd($session['FramedIPAddress']);
		
		/* We use a perl command as it's faster and easier to code the
		 * matching logic than trying to process the entire log file in PHP */
		$perlcommand = build_perl_command($conditions);
		
		
		$username = $session['Username'];

		/* cat /var/log/dansguardian/access.log* |
			perl -ne ' print if ( $_ ge "2008.3.20 23:11:24" && $_ le "2008.3.21 00:30" && /10.1.0.2/ ) '
		 $command = "cat /var/log/dansguardian/access.log*  |
			 perl -ne ' print if ( \$_ ge \"$starttime\" && \$_ le \"$finishtime\" && /$ipaddress/ ) ' ";
		//echo "<pre>Log lines
		//";*/
		// TODO: FIXME: Ensure this is squid3 logs
		// DONE: Add www-data to proxy group so can access logs, ensure logs rotate not compressed
		$command = "gunzip -fc /var/log/squid3/access.log*  | $perlcommand ";
	}else
	{
		$error = "Invalid Acctid";
		$smarty->assign("error", $error);
	}
	if($command)
	{
	    $loglines = array();
		//echo $command;
		exec($command, $loglines) ;
		$log = clean_squid_log_array($loglines);
		arsort($domain_tally, SORT_NUMERIC);
		arsort($domain_size, SORT_NUMERIC);
		//print_r($domain_tally);
		//print_r($log);
		//echo "</pre>";
	}

	$smarty->assign("loglines", $log);
	$smarty->assign("ipaddress", $conditions['ipaddress']);
	$smarty->assign("username", $username);	
	$smarty->assign("session", $session);
	$smarty->assign("domain_tally", $domain_tally);
	$smarty->assign("domain_size", $domain_size);
	$smarty->assign("domain_formatsize", $domain_formatsize);
	$smarty->assign("http_traffic_size", $format_http_traffic_size);
	display_page('log.tpl');


// $loglines);
//print_r($loglines)l

?>

