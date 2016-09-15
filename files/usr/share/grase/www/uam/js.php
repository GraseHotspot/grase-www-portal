<?php
header("Content-Type: text/javascript; charset=utf-8");

require_once('includes/site.inc.php');

$uamIP = (empty($_GET['uamip'])) ? $lanIP : $_GET['uamip'];
$uamPort = (empty($_GET['uamport'])) ? 3990 : $_GET['uamport'];

$jsfile = basename($_GET['js'], '.js');
$jsfilecontents = file_get_contents("js/$jsfile.js");

$search = array(
    "###UAMIPADDRESS###",
    "###UAMPORT###",
    'Username is required',
    'Are you sure you want to disconnect now?',
    'Error loading generic login form',
    'Popup Blocked. Click link below to continue to your website and open the status window',
    'Logged In',
    'Click to open the status window and continue to your site',
    'No response from TOS server',
    'Unable to get secure challenge',
    'Already logged in. Aborting login attempt',
    'Server Timed Out. Please try again',
    'Both username and password are needed',
    'Login Failed due to server error. Please try again',
    'Incorrect response from TOS server. Please notify system admin',
    'TOS login failed due to server error. Please try again',
    'Login successful',
    'Continue to your site',
    'Unknown clientState found in JSON reply',
    'Failed to logoff. Please try again',

    );
$replace = array(
    $uamIP,
    $uamPort,
    T_('Username is required'),
    T_('Are you sure you want to disconnect now?'),
    T_('Error loading generic login form'),
    T_('Popup Blocked. Click link below to continue to your website and open the status window'),
    T_('Logged In'),
    T_('Click to open the status window and continue to your site'),
    T_('No response from TOS server'),
    T_('Unable to get secure challenge'),
    T_('Already logged in. Aborting login attempt'),
    T_('Server Timed Out. Please try again'),
    T_('Both username and password are needed'),
    T_('Login Failed due to server error. Please try again'),
    T_('Incorrect response from TOS server. Please notify system admin'),
    T_('TOS login failed due to server error. Please try again'),
    T_('Login successful'),
    T_('Continue to your site'),
    T_('Unknown clientState found in JSON reply'),
    T_('Failed to logoff. Please try again'),

    );
$jsfilecontents = str_replace($search, $replace, $jsfilecontents);

echo "$jsfilecontents";

?>
