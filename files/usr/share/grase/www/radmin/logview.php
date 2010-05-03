<?
// perl -ne ' print if ( $_ ge "2008.3.20 23:11:24" && $_ le "2008.3.21 00:30" ) ' <access.log.4
//  perl -ne ' print if ( $_ ge "2008.3.20 23:11:24" && $_ le "2008.3.21 00:30" && /10.1.0.2/ ) ' <access.log.4|sed -r 's/(.*)\?[^\s]*(.*)/\1\2/'|grep POST
// $_GET['acctid']
// $_GET['starttime']
// $_GET['finishtime']
// $_GET['ipaddress']

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';

$domain_tally = array();
$domain_size = array();

function clean_dansguardian_log_array($loglines)
{
	foreach($loglines as $line)
	{
		set_time_limit(2);
		list($timestamp, $line) = split(" - ", $line, 2);
		list($ip, $address, $params) = split(" ", $line, 3);
		preg_match('@^(?:http://)?(www.|)([^/]*)(.*)@i', $address, $matches);
		$host = $matches[2];
		$query = $matches[3];		
		preg_match('@^(\*CACHED\*|)\s*([^\s]*)\s*(\d*)@i', $params, $matches);
		$cached = $matches[1];
		$action = $matches[2];
		$size = $matches[3];
		tally_domains($host, $size);
		tally_http_traffic($size);				
		$log[] = array("timestamp" => $timestamp, "URL" => $address, "host" => $host, "cached" => $cached, "request" => $action, "size" => formatBytes($size));
	}
	return $log;
}

function clean_squid_log_array($loglines)
{
	foreach($loglines as $line)
	{
		set_time_limit(2);
// "%9d.%03d %6d %s %s/%03d %d %s %s %s %s%s/%s %s"
// time elapsed remotehost code/status bytes method URL rfc931 peerstatus/peerhost type
		$timestamp = trim(substr($line, 0, 14));
		$elapsed = trim(substr($line, 14, 6));
		$restofline = trim(substr($line, 20));
		list($clientip, $status, $bytes, $method, $URL, $username, $peer, $type) = split(" ", $line, 8);
		preg_match('@^(?:http://)?(www.|)([^/]*)(.*)@i', $URL, $matches);
		$host = $matches[2];
		$query = $matches[3];		
		tally_domains($host, $size);
		tally_http_traffic($size);		
		$log[] = array("timestamp" => $timestamp, "URL" => $URL,"address" => $clientip, "username"=> $username, "host" => $host, "cached" => '', "request" => $method, "size" => formatBytes($bytes));
	}
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
	$domain_formatsize[$domain] = formatBytes($domain_size[$domain]);
}

function tally_http_traffic($size)
{
	global $http_traffic_size, $format_http_traffic_size;
	$http_traffic_size = $http_traffic_size + $size;
	$format_http_traffic_size = formatBytes($http_traffic_size);
}

function format_date($datestr)
{
	// Log format is YYYY.M.DD HH:MM:SS
	list($date, $time) = split(" ", $datestr);
	list($year, $month, $day) = split("-", $date);
	$year = intval($year);
	$month = intval($month);
	$day = intval($day);
	list($hour, $min, $sec) = split(":", $time);
	$hour = intval($hour);
	$time = "$hour:$min:$sec";
	return "$year.$month.$day $time";
}

	if(trim($_GET['acctid']) != '')
	{

		$session = getDBSessionAccounting($_GET['acctid']);
		//print_r($session);

		$starttime =  escapeshellcmd(format_date($session['AcctStartTime']));
		$finishtime =  escapeshellcmd(format_date($session['AcctStopTime']));
		if($finishtime == "0.0.0 0:00:00") $finishtime = '';else $finishtime = " && \$_ le \"$finishtime\" ";
		$ipaddress = escapeshellcmd($session['FramedIPAddress']);
		$username = $session['Username'];

		/* cat /var/log/dansguardian/access.log* |
			perl -ne ' print if ( $_ ge "2008.3.20 23:11:24" && $_ le "2008.3.21 00:30" && /10.1.0.2/ ) '
		 $command = "cat /var/log/dansguardian/access.log*  |
			 perl -ne ' print if ( \$_ ge \"$starttime\" && \$_ le \"$finishtime\" && /$ipaddress/ ) ' ";
		//echo "<pre>Log lines
		//";*/
		$command = "cat /var/log/dansguardian/access.log*  | perl -ne ' print if ( \$_ ge \"$starttime\" $finishtime && /$ipaddress/ ) ' ";
	}else
	{
		$error = "Invalid Acctid";
		$smarty->assign("error", $error);
	}
	if($command)
	{
		//echo $command;
		exec($command, $loglines) ;
		$log = clean_dansguardian_log_array($loglines);
		arsort($domain_tally, SORT_NUMERIC);
		arsort($domain_size, SORT_NUMERIC);
		//print_r($domain_tally);
		//print_r($log);
		//echo "</pre>";
	}

	$smarty->assign("loglines", $log);
	$smarty->assign("ipaddress", $ipaddress);
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

