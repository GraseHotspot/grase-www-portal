<?php

/* Copyright 2008 Timothy White */

function iOS_workaround($userurl)
{

/* Inform iOS that internet is working, so it doesn't try auto login. If this failes, then block */

    if ($userurl == 'http://www.apple.com/library/test/success.html')
    {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2//EN">
<HTML>
<HEAD>
	<TITLE>Success</TITLE>
</HEAD>
<BODY>
Success
</BODY>
</HTML>
<?php    
        exit  ();
    }

/* Following snippet from http://forums.mactalk.com.au/31/66812-iphone-3-0-wireless-captive-portal-support.html
 * This snippit is supposed to prevent iOS from trying to use the portal in this way */
  if (preg_match ("/CaptiveNetworkSupport/", $_SERVER["HTTP_USER_AGENT"])) {
    header ("HTTP/1.0 400 Bad Request");
    exit ();
  }
  
}  
/* */

require_once('includes/site.inc.php');



//
$loginurl = parse_url($_GET['loginurl']);
$query = $loginurl['query'];
parse_str($query, $uamopts);

iOS_workaround($uamopts['userurl']);

if(isset($_GET['loginurl'])){
	$loginlink = "http://10.1.0.1/grase/uam/mini?$query";
	$loginlink2 = $_GET['loginurl'];
}else{
	$loginlink = "http://10.1.0.1/grase/uam/mini";
	$loginlink2 = "http://10.1.0.1:3990/prelogin";
}

$smarty->assign("user_url", $uamopts['userurl']);

$smarty->assign("loginlink", $loginlink);
$smarty->assign("loginlink2", $loginlink2);
$smarty->assign("RealHostname", trim(file_get_contents('/etc/hostname')));

if(isset($_GET['help'])){
	$smarty->display('help.tpl');
}else{
	$smarty->display('welcome.tpl');
}

?>

