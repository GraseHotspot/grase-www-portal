<?php

/* Copyright 2008 Timothy White */

require_once 'includes/session.inc.php';
require_once 'includes/database_functions.inc.php';

$Sysinfo = new SystemInformation();

$smarty->assign('Sysinfo', $Sysinfo);


$smarty->display('main.tpl');

?>

