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
require_once('../radmin/includes/misc_functions.inc.php');

// put full path to Smarty.class.php
if(file_exists('/usr/share/php/smarty/libs/') && ! is_link('/usr/share/php/smarty/libs/'))
{
    // Debian bug http://bugs.debian.org/cgi-bin/bugreport.cgi?bug=514305
    // Remove this code once fixed?
    require_once('smarty/libs/Smarty.class.php');
}else
{
    require_once('smarty/Smarty.class.php');
}

require_once '../radmin/includes/smarty-gettext.php';

$smarty = new Smarty();

// TODO Detect browser settings and allow override of language?
apply_locale($locale);

$smarty->register_block('t', 'smarty_translate');

$smarty->assign("Location", $location);
$smarty->assign("pricemb", "$currency$pricemb");
$smarty->assign("Support", array("link" => $support_link, "name" => $support_name));
$smarty->assign("website_name", $website_name);
$smarty->assign("website_link", $website_link);

$networkoptions = unserialize($Settings->getSetting("networkoptions"));
$lanip = $networkoptions['lanipaddress'];
$smarty->assign("serverip", $lanip);


// Load login page settings from db and use defaults
/*$smarty->assign("hidefooter", $Settings->getSetting('hidefooter') == 'TRUE' ? TRUE : FALSE);
$smarty->assign("hideheader", $Settings->getSetting('hideheader') == 'TRUE' ? TRUE : FALSE);
$smarty->assign("disableallcss", $Settings->getSetting('disableallcss') == 'TRUE' ? TRUE : FALSE);
$smarty->assign("hidehelplink", $Settings->getSetting('hidehelplink') == 'TRUE' ? TRUE : FALSE);
$smarty->assign("hidelogoutbookmark", $Settings->getSetting('hidelogoutbookmark') == 'TRUE' ? TRUE : FALSE);*/

custom_settings(array('hidefooter', 'hideheader', 'disableallcss', 'hidehelplink', 'hidelogoutbookmark'));


$smarty->assign("logintitle", $Settings->getSetting('logintitle'));

// Load templates needed by all pages
load_templates(array('maincss'));


function apply_locale($newlocale)
{
    global $locale;
    $locale = $newlocale;

    Locale::setDefault($locale);
    $language =  locale_get_display_language($locale, 'en');
    $region = locale_get_display_region($locale);
    T_setlocale(LC_MESSAGES, $language);

    T_bindtextdomain("grase", "/usr/share/grase/locale");
    T_bind_textdomain_codeset("grase", "UTF-8");    
    T_textdomain("grase");
}

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
        $smarty->assign($setting, $Settings->getSetting($setting) == 'TRUE' ? TRUE : FALSE);    
    }
}

?>
