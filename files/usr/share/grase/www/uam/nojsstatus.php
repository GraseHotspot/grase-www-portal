<?php

require_once('includes/site.inc.php');

// MySQL call to radacct where IP address matches a session that is current, get username
// Show user details

// Meta refresh to update

$ipaddress = $_SERVER['REMOTE_ADDR'];

$user = DatabaseFunctions::getInstance()->getUserDetails(DatabaseFunctions::getInstance()->getRadiusUserByCurrentSession($ipaddress));

$smarty->register_modifier('bytes', array("Formatting", "formatBytes"));
$smarty->register_modifier('seconds', array("Formatting", "formatSec"));
$session = DatabaseFunctions::getInstance()->getRadiusSessionDetails(DatabaseFunctions::getInstance()->getRadiusIDCurrentSessionByUser($user['Username']));
//print_r($user);
//print_r($session);

$user['RemainingQuota'] = $user['MaxOctets'] - $user['AcctTotalOctets'];
$user['RemainingTime'] = $user['MaxAllSession'] - $user['TotalTimeMonth'];
$smarty->assign('user', $user);
$smarty->assign('session', $session);

$smarty->display('nojsstatus.tpl');

//print_r($user);

?>
