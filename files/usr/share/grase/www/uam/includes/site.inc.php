<?php

/* Copyright 2008 Timothy White */

// Page loading time
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$pagestarttime = $mtime;

// Settings

require_once "MDB2.php";

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once('../radmin/includes/site_settings.inc.php');
require_once('../radmin/includes/misc_functions.inc.php');


//require_once '../radmin/includes/block.t.php';

$smarty = new SmartyBC();

// TODO Detect browser settings and allow override of language?
\Grase\Locale::applyLocale($Settings->getSetting('locale'));

#$smarty->register_block('t', 'smarty_block_t');

$smarty->assign("Location", $Settings->getSetting('locationName'));
$smarty->assign(
    "Support",
    array(
        "link" => $Settings->getSetting('supportContactLink'),
        "name" => $Settings->getSetting('supportContactName')
    )
);
$smarty->assign("website_name", $Settings->getSetting('websiteName'));
$smarty->assign("website_link", $Settings->getSetting('websiteLink'));

$networkoptions = unserialize($Settings->getSetting("networkoptions"));
$lanIP = $networkoptions['lanipaddress'];
$smarty->assign("serverip", $lanIP);


custom_settings(array('hidefooter', 'hideheader', 'disableallcss', 'hidehelplink', 'hidelogoutbookmark'));

$logintitle = $Settings->getSetting('logintitle');
if ($logintitle == '') {
    $logintitle = $Settings->getSetting('locationName') . " Hotspot";
}
$smarty->assign("logintitle", $logintitle);

// Load templates needed by all pages
load_templates(array('maincss'));

function load_templates($templates)
{
    global $Settings, $smarty;
    foreach ($templates as $template) {
        $smarty->assign('tpl_' . $template, $Settings->getTemplate($template));
    }
}

function custom_settings($settings = array())
{
    global $smarty, $Settings;
    foreach ($settings as $setting) {
        $smarty->assign(
            $setting,
            $Settings->getSetting($setting) == 'TRUE' ? true : false
        );
    }
}
