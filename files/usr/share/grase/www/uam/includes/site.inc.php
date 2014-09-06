<?php

/* Copyright 2008 Timothy White */

// Page loading time
   $mtime = microtime();
   $mtime = explode(" ",$mtime);
   $mtime = $mtime[1] + $mtime[0];
   $pagestarttime = $mtime; 

// Settings

require_once "MDB2.php";

function grase_autoload($class_name) {
    if( file_exists(__DIR__. '/../../radmin/classes/' . $class_name . '.class.php'))
    {
        include_once __DIR__. '/../../radmin/classes/' . $class_name . '.class.php';
    }
}

spl_autoload_register('grase_autoload');

//require('/var/www/radmin/includes/site_settings.inc.php');
require_once('../radmin/includes/load_settings.inc.php');
require_once('../radmin/includes/misc_functions.inc.php');

require("smarty3/SmartyBC.class.php");

/*
// put full path to Smarty.class.php
if(file_exists('/usr/share/php/smarty/libs/') && ! is_link('/usr/share/php/smarty/libs/'))
{
    // Debian bug http://bugs.debian.org/cgi-bin/bugreport.cgi?bug=514305
    // Remove this code once fixed?
    require_once('smarty/libs/Smarty.class.php');
}else
{
    require_once('smarty/Smarty.class.php');
}*/

require_once __DIR__.'/../../../vendor/autoload.php';
//require_once '../radmin/includes/block.t.php';

$smarty = new SmartyBC();

// TODO Detect browser settings and allow override of language?
\Grase\Locale::applyLocale($locale);

#$smarty->register_block('t', 'smarty_block_t');

$smarty->assign("Location", $location);
$smarty->assign("Support", array("link" => $support_link, "name" => $support_name));
$smarty->assign("website_name", $website_name);
$smarty->assign("website_link", $website_link);

$networkoptions = unserialize($Settings->getSetting("networkoptions"));
$lanip = $networkoptions['lanipaddress'];
$smarty->assign("serverip", $lanip);


custom_settings(array('hidefooter', 'hideheader', 'disableallcss', 'hidehelplink', 'hidelogoutbookmark'));

$logintitle = $Settings->getSetting('logintitle');
if($logintitle == '') $logintitle = "$location Hotspot";
$smarty->assign("logintitle", $logintitle);

// Load templates needed by all pages
load_templates(array('maincss'));

function load_templates($templates)
{
    global $Settings, $smarty;
    foreach($templates as $template)
    {
        $smarty->assign('tpl_'.$template, $Settings->getTemplate($template));
    }
}

function custom_settings($settings = array())
{
    global $smarty, $Settings;
    foreach($settings as $setting)
    {
        $smarty->assign($setting, $Settings->getSetting($setting) == 'TRUE'
                ? TRUE : FALSE);
    }
}

?>
