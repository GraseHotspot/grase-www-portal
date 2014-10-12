<?php

// Load constants for access levels
require_once 'includes/constants.php';

// Load pages array of levels, key must match the $PAGE and the menubar keys
$PAGESACCESS = array(
    'main' => ALLLEVEL,
    'users' => NORMALLEVEL,
    'edituser' => POWERLEVEL | CREATEUSERLEVEL,
    'createuser' => POWERLEVEL | CREATEUSERLEVEL,
    'createtickets' => POWERLEVEL | CREATEUSERLEVEL,
    'createmachine' => POWERLEVEL | CREATEUSERLEVEL,
    'sessions' => NORMALLEVEL,
    'reports' => NORMALLEVEL | REPORTLEVEL,
    'dhcpleases' => POWERLEVEL | CREATEUSERLEVEL,
    'settings' => ADMINLEVEL,
    'uploadlogo' => ADMINLEVEL,
    'netconfig' => ADMINLEVEL,
    'chilliconfig' => ADMINLEVEL,
    'loginconfig' => ADMINLEVEL,
    'groups' => POWERLEVEL | ADMINLEVEL,
    'vouchers' => POWERLEVEL | ADMINLEVEL,
    'passwd' => ADMINLEVEL,
    'adminlog' => ADMINLEVEL,
    'logout' => ALLLEVEL,
    'login' => ALLLEVEL,
    'purchase_wizard' => ALLLEVEL,
);

// Default level
$ACCESS_LEVEL = ADMINLEVEL;

if (isset($PAGE) && isset($PAGESACCESS[$PAGE])) {
    $ACCESS_LEVEL = $PAGESACCESS[$PAGE];
}
