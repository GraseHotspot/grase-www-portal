<?php

// Take POST parameters
// Apply CHAP
// Redirect to login url

// Login url form 10.1.0.1:3990/login?logon?username=$username&response=$response&userurl=$userurl

$username = urlencode($_POST['username']);
$password = $_POST['password'];
$challenge = $_POST['challenge'];
$userurl = urlencode($_POST['userurl']);

if (! ( $username && $password && $challenge) )
{
    header("Location: http://10.1.0.1:3990/prelogin");
}
$response = md5("\0" . $password . $challenge);
$challenge = urlencode($challenge);
header("Location: http://10.1.0.1:3990/login?username=$username&response=$response&userurl=$userurl");
?>

