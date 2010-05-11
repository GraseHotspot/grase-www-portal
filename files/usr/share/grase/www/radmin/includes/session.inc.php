<?php

//error_reporting(E_ALL|E_STRICT); // REMOVE FOR RELEASE (NOT IN USE DUE TO PEAR MODULES BRING UP LOTS OF ERROS
require_once "Auth.php";
require_once "MDB2.php";



/**
 * Include Auth_Container base class
 */
require_once 'Auth/Container.php';
require_once 'Auth/Container/MDB2.php';

function __autoload($class_name) {
    require_once './classes/' . $class_name . '.class.php';
}

require_once 'load_settings.inc.php';
require_once 'page_functions.inc.php';


   $mtime = microtime();
   $mtime = explode(" ",$mtime);
   $mtime = $mtime[1] + $mtime[0];
   $pagestarttime = $mtime; 

$userfile = $CONFIG['admin_users_passwd_file']; // OBSOLETE



function loginForm($username = null, $status = null, &$auth = null)
{
    global $smarty;
	$smarty->clear_assign('MenuItems');
	$smarty->clear_assign("LoggedInUsername");
    $smarty->assign('username', $username);
    
    switch($status)
    {
        case 0:
            break;
        case -1:
        case -2:
            $error = "Your session has expired. Please login again";
            AdminLog::getInstance()->log("Expired Session");
            break; 
        case -3:
            $error = "Incorrect Login.";
            AdminLog::getInstance()->log("Invalid Login");
            break;
        case -5:
            $errro = "Security Issue. Please login again";
            AdminLog::getInstance()->log("Security Issue With Login");
            break;
        default:
            $error = "Authentication Issue. Please report to Admin";
            AdminLog::getInstance()->log("Auth Issues: $status");
    }
    if(isset($error)) $smarty->assign("error", $error);
   	display_page('loginform.tpl');
   	exit();
}

$options = array(
    'dsn' => $DBs->getRadminDSN(),
    'cryptType' => 'sha1salt',
    'sessionName' => 'GRASE Radius Admin For Internet'
    );
    
$Auth = new Auth("MDB2_Salt", $options, "loginForm");

$Auth->setAdvancedSecurity(array(
    AUTH_ADV_USERAGENT => true,
    AUTH_ADV_IPCHECK   => true,
    AUTH_ADV_CHALLENGE => false
));
$Auth->setIdle(300);

$AdminLog =& AdminLog::getInstance($DBs->getRadminDB(), $Auth);

if($Auth->listUsers() == array())
{
    $Upgrade = new Upgrade();
    $Upgrade->upgradeAdminUsers($CONFIG['admin_users_passwd_file'], $DBs->getRadminDB()); //TODO If admin_user_file doesn't exist?
}
$Auth->start();
    
if (!$Auth->checkAuth())
{
    echo "Should never get here"; // THIS CODE SHOULD NEVER RUN
    exit();
}elseif(isset($_GET['logoff']))
{
    AdminLog::getInstance()->log("Log out");
    $Auth->logout();
    $Auth->start();
}else
{
    $smarty->assign("LoggedInUsername", $Auth->getUsername());
}

?>
