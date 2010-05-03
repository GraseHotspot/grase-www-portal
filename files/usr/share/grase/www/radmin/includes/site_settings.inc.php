<?php

/**** Site Settings ****/
$Settings = new SettingsMySQL('', $DBs->getRadminDB());

if($Settings->getSetting('locationName') == "") // Assume old settings and need to upgrade
{
    $Settings->upgradeFromFiles();    
}

$location = $Settings->getSetting('locationName'); if($location == "") $location = "Default";
$pricemb = $Settings->getSetting('priceMb'); if($pricemb == "") $pricemb = 0.6;
$pricetime = $Settings->getSetting('priceMinute'); if($pricetime == "") $pricetime = 0.1;
$currency = $Settings->getSetting('currency'); if($currency == "") $currency = "R";
$sellable_data = $Settings->getSetting('sellableData'); if($sellable_data == "") $sellable_data = "2147483648"; //2Gb
$useable_data = $Settings->getSetting('userableData'); if($useable_data == "") $useable_data = "3221225472"; //3Gb
$support_name = $Settings->getSetting('supportContactName'); if($support_name == "") $support_name = "Tim White";
$support_link = $Settings->getSetting('supportContactLink'); if($support_link == "") $support_link = "http://purewhite.id.au/";

$website_link = $Settings->getSetting('websiteLink'); if($website_link == "") $website_link = "http://ywam.org/";
$website_name = $Settings->getSetting('websiteName'); if($website_name == "") $website_name = "YWAM";

/* */
// Real hostname
$realhostname = trim(file_get_contents('/etc/hostname'));



?>
