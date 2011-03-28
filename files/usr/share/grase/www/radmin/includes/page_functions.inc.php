<?php

/* Copyright 2008 Timothy White */

/*  This file is part of GRASE Hotspot.

    http://hotspot.purewhite.id.au/

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

require_once 'includes/database_functions.inc.php';
require_once 'includes/load_settings.inc.php';
require_once 'smarty_sortby.php';

function css_file_version()
{
	//reading stream
	$handle = fopen("radmin.css", "r");
	//read first line, TODO:  check if it's not empty, etc.
	$first_line = fgets ($handle);
	$second_line = fgets ($handle);
	fclose($handle);
	//extract revision number, chosen format: "/* $Rev: 1424314 $ */"
	$cssrevid = substr($first_line, 14, -3);
	$application_version = substr($second_line, 13, -3);
	return array($cssrevid, $application_version);
}

function createmenuitems()
{
	//	$menubar['id'] = array("href" => , "label" => );
	$menubar['main'] = array("href" => "./", "label" => "Status");
	$menubar['users'] = array("href" => "display", "label" => "Users");
	$menubar['createuser'] = array("href" => "newuser", "label" => "Create New User");
	$menubar['createtickets'] = array("href" => "newtickets", "label" => "Mass Create Users");	
	$menubar['sessions'] = array("href" => "sessions", "label" => "Monitor Sessions");
    $menubar['reports'] = array("href" => "reports", "label" => "Reports");
    $menubar['monthly_accounts'] = array("href" => "datausage", "label" => "Monthly Reports");
	$menubar['links'] = array("href" => "links", "label" => "Useful Links");	
	$menubar['passwd'] = array("href" => "passwd", "label" => "Admin Users" );
	$menubar['adminlog'] = array("href" => "adminlog", "label" => "Admin Log" );	
	$menubar['settings'] = array("href" => "settings", "label" => "Site Settings" );
	$menubar['uploadlogo'] = array("href" => "uploadlogo", "label" => "Site Logo" );	
	$menubar['logout'] = array("href" => "./?logoff", "label" => "Logoff" );
	return $menubar;
}

function createusefullinks()
{
	#$links['radmin'] = array("href" => "/radmin", "label" => "Internet User Administration (Radmin, RADIUS Administration)");
	#$links['dglog'] = array("href" => "/cgi-bin/dglog.pl", "label" => "Dansguardian Log Viewer, for checking logs for attempts to view blocked pages");
	#$links['munin'] = array("href" => "/munin", "label" => "Munin, System Monitor Graphs");	
	$links['sysstatus'] = array("href" => "/grase/radmin/sysstatus", "label" => "System Status");		
	return $links;
}

function datacosts()
{
	global $datacosts, $pricemb, $currency, $CurrencySymbols;
	$disp_currency = $CurrencySymbols[$currency];
	$datacosts[''] = '';
	$money_options = array($pricemb, 5, 10, 15, 20, 25, 30, 40, 50, 75, 100);
	foreach($money_options as $money)
	{
		$disp_money = number_format($money, 2);
		$data = round($money/$pricemb, 2);
		$datacosts["$data"] = "$disp_currency$disp_money ($data Mb)";
	}
	return $datacosts;
}

function timecosts()
{
	global $timecosts, $pricetime, $currency, $CurrencySymbols;
	$disp_currency = $CurrencySymbols[$currency];
	$timecosts[''] = '';
	//$pricemb = $price; // 60c/Mb
	$time_options = array(5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 60, 90, 120, 180);
	foreach($time_options as $time)
	{
		$cost = number_format(round($pricetime*$time, 2),2);
		$timecosts["$time"] = "$disp_currency$cost ($time mins)";
	}
	return $timecosts;
}

function gboctects()
{
    $gb_options = array(1, 2, 4, 5, 10, 100);
    foreach($gb_options as $gb)
    {
        $octects = $gb*1024*1048576;
        $label = "$gb Gb";
        $options[$octects] = $label;
    }
    return $options;
}

function usergroups()
{
	global $Usergroups;
	// TODO:  Move this stuff into database??
	$Usergroups["Visitors"] = "Visitors";
	$Usergroups["Students"] = "Students";
	$Usergroups["Staff"] = "Staff";
	$Usergroups["Ministry"] = "Ministry";
	$Usergroups[MACHINE_GROUP_NAME] = "Machine (Locked)";
	return $Usergroups;
}

function groupexpirys()
{
	global $Expiry;
	// TODO: Move this stuff into database??
	$Expiry["Staff"] = "+6 months";
	$Expiry["Ministry"] = "+6 months";
	$Expiry["Students"] = "+3 months";
	$Expiry["Visitors"] = "+1 months";
	$Expiry[MACHINE_GROUP_NAME] = "--";
	$Expiry[DEFAULT_GROUP_NAME] = "+1 months";
	return $Expiry;
}

function currency_symbols()
{
	global $CurrencySymbols;
	// TODO: install more locales and automate this?
	$CurrencySymbols['$'] = "$";
	$CurrencySymbols['R'] = "R";
	$CurrencySymbols['£'] = "&pound;";
	$CurrencySymbols['€'] = "&euro;";
	return $CurrencySymbols;
}

function display_page($template)
{
	global $smarty;
	assign_vars();
	return $smarty->display($template);
}

require_once 'smarty/Smarty.class.php';



$smarty = new Smarty;

$smarty->compile_check = true;
//$smarty->register_outputfilter('smarty_outputfilter_strip');
$smarty->register_modifier('bytes', array("Formatting", "formatBytes"));
$smarty->register_modifier('seconds', array("Formatting", "formatSec"));

list($cssrevid, $application_version)=css_file_version();
$smarty->assign("css_version", $cssrevid);
$smarty->assign("application_version", $application_version);
$smarty->assign("Application", APPLICATION_NAME);

$smarty->assign("RealHostname", $realhostname);

// Setup Menus
$smarty->assign("MenuItems", createmenuitems());
$smarty->assign("Usergroups", usergroups());


// Costs
$smarty->assign("CurrencySymbols", currency_symbols());
$smarty->assign("Datacosts", datacosts());
$smarty->assign("Timecosts", timecosts());

$smarty->assign('gbvalues', gboctects());


function assign_vars()
{
	global $smarty, $sellable_data, $useable_data, $used_data, $sold_data;
	global $location, $website_name, $website_link;

	// Data
	$total_sellable_data = $sellable_data; 
	$smarty->assign("TotalSellableData", $total_sellable_data);
	$sold_data =  getSoldData();
	$smarty->assign("SoldOctets", $sold_data);
	$smarty->assign("SellableOctets", $total_sellable_data - $sold_data);
	$smarty->assign("SoldOctetsPercent", $sold_data/($total_sellable_data)*100);

	$total_useable_data = $useable_data; 
	$smarty->assign("TotalUseableData", $total_useable_data);
	$used_data =  getUsedData();
	$smarty->assign("DataUsageOctets", $used_data);
	$smarty->assign("DataRemainingOctets", $total_useable_data - $used_data);
	$smarty->assign("DataUsagePercent", $used_data/($total_useable_data)*100);

    // Settings
    $smarty->assign("Title", $location . " - " . APPLICATION_NAME);
    $smarty->assign("website_name", $website_name);
    $smarty->assign("website_link", $website_link);
    

	// last months usage
	$used_data =  getMonthUsedData(); // TODO: make it get last month that data is for?
	$smarty->assign("LastM_DataUsageOctets", $used_data);
	$smarty->assign("LastM_DataRemainingOctets", $total_useable_data - $used_data);
	$smarty->assign("LastM_DataUsagePercent", $used_data/($total_useable_data)*100);
}


groupexpirys();
?>
