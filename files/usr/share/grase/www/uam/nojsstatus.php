<?php

require_once('includes/site.inc.php');

// MySQL call to radacct where IP address matches a session that is current, get username
// Show user details

// Meta refresh to update

$ipaddress = $_SERVER['REMOTE_ADDR'];

$username = DatabaseFunctions::getInstance()->getRadiusUserByCurrentSession($ipaddress);

if($username != '')
{
    $user = DatabaseFunctions::getInstance()->getUserDetails($username);

    $session = DatabaseFunctions::getInstance()->getRadiusSessionDetails(DatabaseFunctions::getInstance()->getRadiusIDCurrentSessionByUser($user['Username']));
    //print_r($user);
    //print_r($session);

    $user['RemainingQuota'] = $user['MaxOctets'] - $user['AcctTotalOctets'];
    $user['RemainingTime'] = $user['MaxAllSession'] - $user['TotalTimeMonth'];
    $smarty->assign('user', $user);
    $smarty->assign('session', $session);
}else{
    $error = array("You don't appear to be logged in. If you have just logged in, try refreshing the page.<br/>
                    Otherwise, go back to the <a href='hotspot'>login form.</a>");
    $smarty->assign("error", $error);
}

    $smarty->register_modifier('bytes', array("Formatting", "formatBytes"));
    $smarty->register_modifier('seconds', array("Formatting", "formatSec"));

    $smarty->display('nojsstatus.tpl');

//print_r($user);

?>
