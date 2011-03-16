<?php

require_once('includes/site.inc.php');

$loginurl = parse_url($_GET['loginurl']);
$query = $loginurl['query'];
parse_str($query, $uamopts);

/* Important parts of uamopts
    * challenge
    * userurl
    * res
    
*/    

if(!isset($uamopts['res']))
{
    // Redirect to prelogin
	header("Location: http://10.1.0.1:3990/prelogin");
}

// Already been through prelogin
$jsloginlink = "http://10.1.0.1/grase/uam/mini?$query";
$nojsloginlink = $_GET['loginurl'];

switch($uamopts['res'])
{
    case 'already':
        if $uamopts['userurl'] header("Location: $userurl");
        // Fall through to welcome page?
        break;
    
    case 'failed':
        // Login failed? Show error and display login again
        break; // Fall through?
        
    case 'notyet':
    case 'logoff':
        // Display login
        setup_login_form()
        break;
        
    case 'success':
        //Logged in. Try popup and redirect to userurl
        break;        
        
}


function setup_login_form()
{
    global $smarty, $uamopts;
    $smarty->assign("user_url", $uamopts['userurl']);
    $smarty->assign("challenge", $uamopts['$challenge']);
    $smarty->assign("RealHostname", trim(file_get_contents('/etc/hostname')));
}

$smarty->display('portal.tpl');


?>

