<?php

require_once('includes/site.inc.php');

// MySQL call to radacct where IP address matches a session that is current, get username
// Show user details

// Meta refresh to update

$ipaddress = $_SERVER['REMOTE_ADDR'];

$username = DatabaseFunctions::getInstance()->getRadiusUserByCurrentSession($ipaddress);

if ($username != '') {
    $user = DatabaseFunctions::getInstance()->getUserDetails($username);

    $session = DatabaseFunctions::getInstance()->getRadiusSessionDetails(DatabaseFunctions::getInstance()->getRadiusIDCurrentSessionByUser($user['Username']));
    //print_r($user);
    //print_r($session);

    /* Shared code with get_user_limits */
    $maxoctets = "";
    $timelimit = "";
    
    if (isset($user['Max-Octets'])) {
        $maxoctets = $user['Max-Octets'];
    }
    if (isset($user['Max-All-Session'])) {
        $timelimit = $user['Max-All-Session'];
    }
    
    if (isset($user['GroupSettings']['MaxOctets']) && ! $maxoctets) {
        $maxoctets = $user['GroupSettings']['MaxOctets'];
    }
    if (isset($user['GroupSettings']['MaxSeconds']) && ! $timelimit) {
        $timelimit = $user['GroupSettings']['MaxSeconds'];
    }
    /* */
    
    $user['MaxOctets'] = $maxoctets;
    $user['MaxAllSession'] = $maxtime;

    if ($maxoctets != "") {
        $user['RemainingQuota'] = $maxoctets - $user['AcctTotalOctets'];
    }
        
    if ($timelimit != "") {
        $user['RemainingTime'] = $timelimit - $user['TotalTimeMonth'];
    }
        
    $smarty->assign('user', $user);
    $smarty->assign('session', $session);
} else {
    $error = array("You don't appear to be logged in. If you have just logged in, try refreshing the page.<br/>
                    Otherwise, go back to the <a href='hotspot'>login form.</a>");
    $smarty->assign("error", $error);
}

    $smarty->register_modifier('bytes', array("\Grase\Util", "formatBytes"));
    $smarty->register_modifier('seconds', array("\Grase\Util", "formatSec"));

    $smarty->display('nojsstatus.tpl');

//print_r($user);
