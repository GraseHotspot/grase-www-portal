<?php
header("Content-Type: text/javascript; charset=utf-8");

require_once('includes/site.inc.php');

$jsfile = basename($_GET['js'], '.js');
$jsfilecontents = file_get_contents("js/$jsfile.js");

$search = array(
    "###SERVERIPADDRESS###",
    'Username is required',
    'Are you sure you want to disconnect now?',
    'Error loading generic login form',
    'Popup Blocked. Click link below to continue to your website and open the status window',
    'Logged In',
    );
$replace = array(
    $lanip,
    T_('Username is required'),
    T_('Are you sure you want to disconnect now?'),
    T_('Error loading generic login form'),
    T_('Popup Blocked. Click link below to continue to your website and open the status window'),
    T_('Logged In'),
    );
$jsfilecontents = str_replace($search, $replace, $jsfilecontents);

echo "$jsfilecontents";

?>
