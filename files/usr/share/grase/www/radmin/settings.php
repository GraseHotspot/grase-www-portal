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

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';

$error = array();
$success = array();

if(isset($_POST['submit']))
{
    $newlocationname    = trim(clean_text($_POST['locationname']));
    $newsupportcontact  = trim(clean_text($_POST['supportcontact']));    
    $newsupportlink     = trim(clean_text($_POST['supportlink']));
	$newpricemb         = trim(clean_text($_POST['pricemb']));
	$newpricetime       = trim(clean_text($_POST['pricetime']));
	$newcurrency        = trim(clean_text($_POST['currency']));    
	$newwebsitename     = trim(clean_text($_POST['websitename']));
	$newwebsitelink     = trim(clean_text($_POST['websitelink']));
	$newsellabledata    = trim(clean_text($_POST['sellable_data']));
	$newuseabledata     = trim(clean_text($_POST['useable_data']));	    
    // Check for changed items
    
    if($newlocationname != $location) update_location($newlocationname);
    if($newsupportcontact != $support_name) update_supportcontact($newsupportcontact);    
    if($newsupportlink != $support_link) update_supportlink($newsupportlink);    
    if($newpricemb != $pricemb) update_pricemb($newpricemb);
    if($newpricetime != $pricetime) update_pricetime($newpricetime);
    if($newcurrency != $currency) update_currency($newcurrency);
    if($newwebsitename != $website_name) update_websitename($newwebsitename);
    if($newwebsitelink != $website_link) update_websitelink($newwebsitelink);
    if($newsellabledata != $sellable_data) update_sellabledata($newsellabledata);
    if($newuseabledata != $useable_data) update_useabledata($newuseabledata);    
    // Call validate&change functions for changed items
}

// TODO: Make a proper settings file?
load_global_settings(); // Reloads settings

	$smarty->assign("location", $location);
	$smarty->assign("pricemb", $pricemb);
	$smarty->assign("pricetime", $pricetime);
	$smarty->assign("currency", $currency);
	$smarty->assign("dispcurrency", $CurrencySymbols[$currency]);
	$smarty->assign("sellable_data", $sellable_data);
	$smarty->assign("useable_data", $useable_data);
	$smarty->assign("support_name", $support_name);
	$smarty->assign("support_link", $support_link);
	$smarty->assign("website_name", $website_name);
	$smarty->assign("website_link", $website_link);
	
if(sizeof($error) > 0) $smarty->assign("error", $error);	
if(sizeof($success) > 0) $smarty->assign("success", $success);

//$old_error_level = error_reporting(1); // TODO: Don't have this catching stuff

// Location

function update_location($location)
{
    global $error, $smarty, $Settings, $success;
    if($location == "") $error[] = _("Location name not valid");
    else {
	    if($Settings->setSetting('locationName', $location))
	    {
		    $success[] = _("Location name updated");
		    AdminLog::getInstance()->log(_("Location Name changed to")." $new_location");
		    $smarty->assign("Title", $location . " - " . APPLICATION_NAME); //TODO: remove need for this with setting reload function
	    }
	    else
	    {
	        $error[] = _("Error Saving Location Name");
	    }    
    }
}

// Website
function update_websitename($websitename)
{
    global $error, $smarty, $Settings, $success;
    if($websitename == "") $error[] = _("Website name not valid");    
    else
    {
        if($Settings->setSetting('websiteName', $websitename))
        {
            $success[] = _("Website name updated");
			AdminLog::getInstance()->log(_("Website name updated"));        
        }
        else
        {
            $error[] = _("Error Saving Website Name");
        }
    }
}

function update_websitelink($websitelink)
{
    global $error, $smarty, $Settings, $success;
    if($websitelink == "" || strpos($websitelink, ' ') !== false) $error[] = _("Website link not valid");    
    else
    {
        if($Settings->setSetting('websiteLink', $websitelink))
        {
            $success[] = _("Website link updated");
			AdminLog::getInstance()->log(_("Website link updated"));        
        }
        else
        {
            $error[] = _("Error Saving Website link");
        }
    }
}

// Pricing

function update_pricemb($pricemb)
{
    global $error, $smarty, $Settings, $success;
	if($pricemb != "" && is_numeric($pricemb))
	{
		if($Settings->setSetting('priceMb', $pricemb))
		{
			$success[] = _("Price per Mb updated");
			AdminLog::getInstance()->log(_("Price per Mb updated"));
		}
		else
		{
			$error[] = _("Error saving Price per Mb");
		}
	}
	else
	{
		$error[] = _("Invalid Price per Mb");
	}
}	

function update_pricetime($pricetime)
{
    global $error, $smarty, $Settings, $success;
	if($pricetime != "" && is_numeric($pricetime))
	{
		if($Settings->setSetting('priceMinute', $pricetime))
		{
			$success[] = _("Price per Minute Updated");
			AdminLog::getInstance()->log(_("Price per Minute Updated"));
		}else
		{
			$error[] = _("Error saving Price per Minute");
		}
	}else
	{
		$error[] = _("Invalid Price per Minute");
	}
}

function update_currency($currency)
{
    global $error, $smarty, $Settings, $success, $CurrencySymbols;
	if($currency != "" && strlen($currency) < 4)
	{
		if($Settings->setSetting('currency', $currency))
		{
			$success[] = _("Currency updated");
			AdminLog::getInstance()->log(_("Currency updated to") ." ${CurrencySymbols[$currency]}");
		}
		else
		{
			$error[] = _("Error saving Currency");
		}
	}else
	{
		$error[] = _("Invalid Currency");
	}


}


// Data limits
function update_sellabledata($sellabledata)
{
    global $error, $smarty, $Settings, $success;
    if($sellabledata != "" && is_numeric($sellabledata))
    {
        if($Settings->setSetting('sellableData', $sellabledata))
        {
            $success[] = _("Sellable Data Limit Update");
			AdminLog::getInstance()->log(_("Sellable Data Limit Update"));        
        }
        else
        {
            $error[] = _("Error updating Sellable Data Limit");
        }
    }
    else
    {
        $error[] = _("Invalid value for Sellable Data");
    }
}

function update_useabledata($useabledata)
{
    global $error, $smarty, $Settings, $success;
    if($useabledata != "" && is_numeric($useabledata))
    {
        if($Settings->setSetting('useableData', $useabledata))
        {
            $success[] = _("Useable Data Limit Update");
			AdminLog::getInstance()->log(_("Useable Data Limit Update"));        
        }
        else
        {
            $error[] = _("Error updating Useable Data Limit");
        }
    }
    else
    {
        $error[] = _("Invalid value for Useable Data");
    }
}

// Support Contact
function update_supportcontact($supportname)
{
    global $error, $smarty, $Settings, $success;
    if($supportname == "") $error[] = _("Support name not valid");    
    else
    {
        if($Settings->setSetting('supportContactName', $supportname))
        {
            $success[] = _("Support name updated");
			AdminLog::getInstance()->log(_("Support name updated"));        
        }
        else
        {
            $error[] = _("Error Saving Support Name");
        }
    }
}

function update_supportlink($supportlink)
{
    global $error, $smarty, $Settings, $success;
    if($supportlink == "" || strpos($supportlink, ' ') !== false) $error[] = _("Support link not valid");    
    else
    {
        if($Settings->setSetting('supportContactLink', $supportlink))
        {
            $success[] = _("Support link updated");
			AdminLog::getInstance()->log(_("Support link updated"));        
        }
        else
        {
            $error[] = _("Error Saving Support link");
        }
    }
}

//error_reporting($old_error_level);
	//require('includes/site_settings.inc.php'); // ReRead settings
	display_page('settings.tpl');

?>


