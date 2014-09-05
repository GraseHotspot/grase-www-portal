<?php

/* Copyright 2008 Timothy White */

/*  This file is part of GRASE Hotspot.

    http://grasehotspot.org/

    GRASE Hotspot is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    GRASE Hotspot is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GRASE Hotspot.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once __DIR__ . '/../../../vendor/autoload.php';

$Radmin = new \Grase\Database\Database('/etc/grase/radmin.conf');
$Settings = new \Grase\Database\Radmin($Radmin);

load_global_settings();

function load_global_settings()
{
    global $Settings, $location, $pricemb, $pricetime, $support_name,
           $support_link, $website_link, $website_name, $locale, $mb_options,
           $time_options, $kbit_options, $DEMO_SITE;

    $location = $Settings->getSetting('locationName');
    if ($location == "") {
        $location = "Default";
    }
    $pricemb = $Settings->getSetting('priceMb');
    if ($pricemb == "") {
        $pricemb = 0.6;
    }
    $pricetime = $Settings->getSetting('priceMinute');
    if ($pricetime == "") {
        $pricetime = 0.1;
    }
    $support_name = $Settings->getSetting('supportContactName');
    if ($support_name == "") {
        $support_name = "Tim White";
    }
    $support_link = $Settings->getSetting('supportContactLink');
    if ($support_link == "") {
        $support_link = "http://grasehotspot.com/";
    }

    $website_link = $Settings->getSetting('websiteLink');
    if ($website_link == "") {
        $website_link = "http://grasehotspot.org/";
    }
    $website_name = $Settings->getSetting('websiteName');
    if ($website_name == "") {
        $website_name = "GRASE Hotspot Project";
    }

    $mb_options = $Settings->getSetting('mbOptions');
    if ($mb_options == "") {
        $mb_options = "10 50 100 250 500 1024 2048 4096 102400";
    }
    $time_options = $Settings->getSetting('timeOptions');
    if ($time_options == "") {
        $time_options = "5 10 20 30 45 60 90 120 180 240 600 6000";
    }
    $kbit_options = $Settings->getSetting('kbitOptions');
    if ($kbit_options == "") {
        $kbit_options = "64 128 256 512 1024 1536 2048 4096 8192";
    }

    $locale = $Settings->getSetting('locale');
    if ($locale == '') {
        $locale = "en_AU";
    }

    // Allow extra things on Demo site (piwik tracking of admin interface)
    $DEMO_SITE = $Settings->getSetting('demosite');
}

/* PHP No longer correctly gets the timezone from the system. Try to set it */

$tzfile = trim(file_get_contents('/etc/timezone'));

if ($tzfile) {
    date_default_timezone_set($tzfile);
} // TODO Need to catch error here?
else {
    date_default_timezone_set(@date_default_timezone_get());
}

?>
