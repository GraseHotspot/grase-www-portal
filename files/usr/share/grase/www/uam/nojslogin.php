<?php

// Take POST parameters
// Apply CHAP
// Redirect to login url

require_once('includes/site.inc.php');

// Login url form 10.1.0.1:3990/login?logon?username=$username&response=$response&userurl=$userurl

$username = urlencode($_POST['username']);
$password = $_POST['password'];
$challenge = $_POST['challenge'];
$userurl = urlencode($_POST['userurl']);
$ident = '00';

if (! ( $username && $password && $challenge) )
{
    header("Location: http://$lanip:3990/prelogin");
}
$hexchal = pack ("H32", $challenge);
$response = md5("\0" . $password . $hexchal);
//print md5($ident . String2Hex($password) . $hexchal);

$challenge = urlencode($challenge);
header("Location: http://$lanip:3990/login?username=$username&response=$response&userurl=$userurl");


/*function String2Hex($string){
    $hex='';
    for ($i=0; $i < strlen($string); $i++){
        $hex .= dechex(ord($string[$i]));
    }
    return $hex;
}*/

?>

