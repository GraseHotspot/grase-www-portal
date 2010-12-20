<?php

/* Copyright 2008 Timothy White */

// Page loading time
   $mtime = microtime();
   $mtime = explode(" ",$mtime);
   $mtime = $mtime[1] + $mtime[0];
   $pagestarttime = $mtime; 

// Settings

require_once "MDB2.php";

function __autoload($class_name) {
    require_once '../radmin/classes/' . $class_name . '.class.php';    
}

//require('/var/www/radmin/includes/site_settings.inc.php');
require_once('../radmin/includes/load_settings.inc.php');

// put full path to Smarty.class.php
require_once('smarty/Smarty.class.php');
$smarty = new Smarty();

$smarty->assign("Location", $location);
$smarty->assign("pricemb", "$currency$pricemb");
$smarty->assign("Support", array("link" => $support_link, "name" => $support_name));
$smarty->assign("website_name", $website_name);
$smarty->assign("website_link", $website_link);

?>
