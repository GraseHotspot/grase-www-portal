<?php

/* Copyright 2008 Timothy White */

require_once('includes/site.inc.php');

header("Content-Type: text/javascript;");

if($_GET['username']){

    $user = DatabaseFunctions::getInstance()->getUserDetails(mysql_real_escape_string($_GET['username'])); // TODO: sanitize INPUT
    
    $maxoctets = "";
    $timelimit = "";
    
    // TODO Show remaining time for hour/day limits
    // TODO Group limits
    if(isset($user['Max-Octets'])) $maxoctets = $user['Max-Octets'];
    if(isset($user['Max-All-Session'])) $timelimit = $user['Max-All-Session'];
    
    if(isset($user['GroupSettings']['MaxOctets']) && ! $maxoctets) $maxoctets = $user['GroupSettings']['MaxOctets'];
    if(isset($user['GroupSettings']['MaxSeconds']) && ! $timelimit) $timelimit = $user['GroupSettings']['MaxSeconds'];

    
?>
chilliJSON.reply({"version":"1.0","clientState":1,"user_details":{"monthlyusagelimit":"<? echo $maxoctets;?>","monthlytimelimit":"<? echo $timelimit;?>"}})
<?}else{?>
chilliJSON.reply({"version":"1.0","clientState":0})
<?}?>
