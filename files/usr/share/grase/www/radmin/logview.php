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

$domainTally = array();
$domainSize = array();
$domainFormatSize = array();
$formatHTTPTrafficSize = "";

// TODO: Create class that handles all the log processing

function cleanDansguardianLogArray($logLines)
{
    $log = array();
    foreach ($logLines as $line) {
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
        tallyDomains($host, $size);
        tallyHTTPTraffic($size);
        $log[] = array(
            "timestamp" => $timestamp,
            "URL" => $address,
            "host" => $host,
            "cached" => $cached,
            "request" => $action,
            "size" => \Grase\Util::formatBytes($size)
        );
    }
    return $log;
}

function cleanSquidLogArray($logLines)
{
    $log = array();
    foreach ($logLines as $line) {
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
        tallyDomains($host, $bytes);
        tallyHTTPTraffic($bytes);
        $log[] = array(
            "timestamp" => date('Y-m-d H:i:s', $timestamp),
            "URL" => $URL,
            "address" => $clientip,
            "username" => $username,
            "host" => $host,
            "cached" => '',
            "request" => $method,
            "size" => \Grase\Util::formatBytes($bytes)
        );
    }

    // The time consuming part is done, no more than 5s should be needed now
    set_time_limit(5);
    return $log;
}

function tallyDomains($domain, $size)
{
    global $domainTally, $domainSize, $domainFormatSize;
    if (array_key_exists($domain, $domainTally)) {
        $domainTally[$domain]++;
        $domainSize[$domain] += $size;
    } else {
        $domainTally[$domain] = 1;
        $domainSize[$domain] = $size;
    }
    $domainFormatSize[$domain] = \Grase\Util::formatBytes($domainSize[$domain]);
}

function tallyHTTPTraffic($size)
{
    global $HTTPTrafficSize, $formatHTTPTrafficSize;
    $HTTPTrafficSize = $HTTPTrafficSize + $size;
    $formatHTTPTrafficSize = \Grase\Util::formatBytes($HTTPTrafficSize);
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

function buildPerlCommand($conditions)
{
    $perlCommand = "perl -ne ' print if ( %s )'";
    $perlArgs = array();

    $startArg = ' $_ ge "%s" ';
    $finishArg = ' $_ le "%s" ';
    $IPArg = ' /%s/ ';

    foreach ($conditions as $condition => $value) {
        switch ($condition) {
            case "starttime":
                $perlArgs[] = sprintf($startArg, $value);
                break;
            case "finishtime":
                $perlArgs[] = sprintf($finishArg, $value);
                break;
            case "ipaddress":
                $perlArgs[] = sprintf($IPArg, $value);
                break;
        }

    }

    return sprintf($perlCommand, implode(' && ', $perlArgs));
}


/* Start Page Logic */

if (trim($_GET['acctid']) != '') {
    $session = DatabaseFunctions::getInstance()->getRadiusSessionDetails($_GET['acctid']);

    // Build up components for perl matching command
    $conditions['starttime'] = escapeshellcmd(format_unixtime($session['AcctStartTime']));
    $finishTime = escapeshellcmd(format_unixtime($session['AcctStopTime']));

    if ($finishTime != "0.0.0 0:00:00" && $finishTime != '') {
        $conditions['finishtime'] = $finishTime;
    }

    $conditions['ipaddress'] = escapeshellcmd($session['FramedIPAddress']);

    /* We use a perl command as it's faster and easier to code the
     * matching logic than trying to process the entire log file in PHP */
    $perlCommand = buildPerlCommand($conditions);

    $username = $session['Username'];

    $command = "gunzip -fc /var/log/squid3/access.log*  | $perlCommand ";
} else {
    $error = "Invalid Acctid";
    $templateEngine->assign("error", $error);
}
if ($command) {
    $logLines = array();
    exec($command, $logLines);
    $log = cleanSquidLogArray($logLines);
    arsort($domainTally, SORT_NUMERIC);
    arsort($domainSize, SORT_NUMERIC);
}

$templateEngine->assign("loglines", $log);
$templateEngine->assign("ipaddress", $conditions['ipaddress']);
$templateEngine->assign("username", $username);
$templateEngine->assign("session", $session);
$templateEngine->assign("domain_tally", $domainTally);
$templateEngine->assign("domain_size", $domainSize);
$templateEngine->assign("domain_formatsize", $domainFormatSize);
$templateEngine->assign("http_traffic_size", $formatHTTPTrafficSize);
$templateEngine->displayPage('log.tpl');
