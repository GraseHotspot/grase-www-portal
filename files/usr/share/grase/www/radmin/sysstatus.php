<HTML>
<HEAD>
<TITLE>System Monitor</TITLE>
<!-- CSS Stylesheet -->
<link rel="stylesheet" type="text/css" href="/hotspot.css" id="hotspot_css" />
<link rel="stylesheet" type="text/css" href="radmin.css" id="radmin_css" />
</head>
<body>
<h1>Server Monitor</h1>
<ul id="statusmon">

<?php
// TODO Make this use templates too
// ps -p 5437 -o lstart

function pid_running_time($pid)
{
		// Check if proc is running, and get start time
		$lstart = shell_exec('ps  -o lstart= -p '.$pid);
		// Work out how long it's been running
		if($lstart)
		{ // Running, calculate time
			$runningseconds = time() - strtotime($lstart);
			return $runningseconds;
		}
		return false;
		
}

function proc_running_time($pid)
{
		// Check if proc is running, and get start time
		$lstart = shell_exec("ps --sort pid -o lstart= -C $pid | head -n 1");
		// Work out how long it's been running
		if($lstart)
		{ // Running, calculate time
			$runningseconds = time() - strtotime($lstart);
			return $runningseconds;
		}
		return false;
		
}
$monitored_processes[] = array("label" => "Load", "load" => '');
$monitored_processes[] = array("label" => "Uptime", "uptime" => '');
$monitored_processes[] = array("label" => "*HotSpot (Captive Portal, CoovaChilli)", "procname" => "chilli", "pid" => "/var/run/chilli.pid");
$monitored_processes[] = array("label" => "*Webserver (Apache2)", "procname" => "apache2", "pid" => "/var/run/apache2.pid");
$monitored_processes[] = array("label" => "*Authentication (FreeRADIUS)", "procname" => "freeradius", "pid" => "/var/run/freeradius/freeradius.pid");
$monitored_processes[] = array("label" => "*Database (MySQL)", "procname" => "mysqld", "pid_error" => "/var/run/mysqld/mysqld.pid");
$monitored_processes[] = array("label" => "*Proxy (Squid)", "procname" => "squid", "pid" => "/var/run/squid.pid");
$monitored_processes[] = array("label" => "*Filter (Dansguardian)", "procname" => "dansguardian", "pid" => "/var/run/dansguardian.pid");
$monitored_processes[] = array("label" => "Ad Filter (Adzapper through Squid)", "procname" => "adzapper");
$monitored_processes[] = array("label" => "*DNS (Dnsmasq)", "procname" => "dnsmasq", "pid" => "/var/run/dnsmasq.pid");
$monitored_processes[] = array("label" => "*SSH", "procname" => "sshd", "pid" => "/var/run/sshd.pid");
$monitored_processes[] = array("label" => "*OpenVPN UDP", "procname" => "openvpn", "pid" => "/var/run/openvpn.client-udp.pid");
$monitored_processes[] = array("label" => "OpenVPN TCP", "procname" => "openvpn", "pid" => "/var/run/openvpn.client-tcp.pid");
$monitored_processes[] = array("label" => "*Postfix (Mail)", "procname" => "master");
#$monitored_processes[] = array("label" => "Email (IMAP/POP3, Dovecot)", "procname" => "dovecot", "pid_error" => "/var/run/dovecot/master.pid");
//$monitored_processes[] = array("label" => "Ejabberd", "procname" => "dnsmasq", "pid" => "/var/run/dnsmasq.pid");
#$monitored_processes[] = array("label" => "Monitor Bot", "procname" => "jimbo.py");
#$monitored_processes[] = array("label" => "Network Workgroup (Winbind)", "procname" => "winbind", "pid" => "/var/run/samba/winbindd.pid");
#$monitored_processes[] = array("label" => "*Internet (TrueTech)", "ping" => "trueserver");
$gateway_ip=shell_exec('/sbin/route -n |grep -o ^0\.0\.0\.0[[:space:]]*[^[:space:]]*| awk \'{print $2 }\'');
$monitored_processes[] = array("label" => "*Network (Gateway:$gateway_ip)", "ping" => $gateway_ip);
#route -n |grep -o ^0\.0\.0\.0[[:space:]]*[^[:space:]]*| awk '{print $2 }'
#$monitored_processes[] = array("label" => "*Internet (Open DNS)", "ping" => "208.67.220.220");
$monitored_processes[] = array("label" => "Internet (Google SA)", "ping" => "google.co.za");
$monitored_processes[] = array("label" => "Network (VPN Endpoint)", "ping" => "10.64.63.1");


foreach($monitored_processes as $proc)
{
	if($proc['pid'] && file_exists($proc['pid'])) // PID File
	{
		// Read file
		$pid = file_get_contents($proc['pid']);
		$stime = pid_running_time($pid);
		if($stime)
		{
			output_status($proc, 1, $stime);
		}else
		{
			output_status($proc, 0, "");
		}
	}elseif($proc['procname']) // Check if proc is running, etc
	{
		$stime = proc_running_time($proc['procname']);
		if($stime)
		{
			output_status($proc, 1, $stime);
		}else
		{
			output_status($proc, 0, "");
		}
	}elseif($proc['ping'])// Ping host to see if up
	{
		unset($output_dummy);
		exec('ping -c 3 -q '.$proc['ping'], $output_dummy, $return);
		$avg_time = shell_exec("echo ".array_pop($output_dummy)."|tail -n 1|cut -f 2 -d '='|cut -f 2 -d '/'");
		if($return == 1) output_status($proc, 0, ''); //Host down
		if($return == 2) output_status($proc, 0, '');// Error (-1)
		if($return == 0) output_status($proc, 1, $avg_time, 'ping');// Host up
	}elseif(isset($proc['uptime'])) // Uptime
	{
		// read in the uptime (using exec)
		$uptime = exec("cat /proc/uptime");
		$uptime = split(" ",$uptime);
		$uptimeSecs = $uptime[0];

		// get the static uptime
		$staticUptime = "Server Uptime: ".format_time($uptimeSecs);
		output_status($proc, 1, $uptimeSecs);
	}else
	{
		output_status($proc, -1, "");
	}
	flush();
}

function output_status($proc, $status, $time, $type='proc')
{
	$ftime = "";
	if($type == 'proc')
	{
		if($time) $ftime = format_time($time);
	}elseif($type == 'ping')
	{
		$ftime = "$time ms";
	}
	if($status ===1) $status = 'running';//$status = "<img src='images/green.png'/>Running";
	if($status === 0) $status = 'stopped';//$status = "<img src='images/red.png'/>Not Running";
	if($status === -1) $status = 'nomon'; //$status = "<img src='images/blue.png'/>No plugin for this monitor";
//	print "<tr><td>${proc['label']}</td><td>$status</td><td>$ftime</td></tr>\n";
	print "<li class='$status'> ${proc['label']}<br/><span class='time'>$ftime</span></li>\n";
}

function format_oldtime($sec)
{
	$hour = 0; $day = 0;
	$min = floor($sec/60);
	$sec = $sec - $min * 60;
	if($min > 60)
	{
		$hour = floor($min / 60);
		$min = $min - $hour * 60;
	}
	if($hour > 24)
	{
		$day = floor($hour / 24);
		$hour = $hour - $day * 24;
	}
	if($sec == 1) $sec = "1 Second"; else $sec = "$sec Seconds";
	if($min == 1) $min = "1 Minute"; else $min = "$min Minutes";
	if($hour == 1) $hour = "1 Hour"; else $hour = "$hour Hours";
	if($day == 1) $day = "1 Day"; else $day = "$day Days";		
	return "$day, $hour, $min, $sec";
}

function format_time($seconds)
{
  $secs = intval($seconds % 60);
  $mins = intval($seconds / 60 % 60);
  $hours = intval($seconds / 3600 % 24);
  $days = intval($seconds / 86400);
  
  if ($days > 0)
  {
    $uptimeString .= $days;
    $uptimeString .= (($days == 1) ? " day" : " days");
  }
  if ($hours > 0)
  {
    $uptimeString .= (($days > 0) ? ", " : "") . $hours;
    $uptimeString .= (($hours == 1) ? " hour" : " hours");
  }
  if ($mins > 0)
  {
    $uptimeString .= (($days > 0 || $hours > 0) ? ", " : "") . $mins;
    $uptimeString .= (($mins == 1) ? " minute" : " minutes");
  }
  if ($secs > 0)
  {
    $uptimeString .= (($days > 0 || $hours > 0 || $mins > 0) ? ", " : "") . $secs;
    $uptimeString .= (($secs == 1) ? " second" : " seconds");
  }
  return $uptimeString;
}

?>
</ul>
</body>
</html>
