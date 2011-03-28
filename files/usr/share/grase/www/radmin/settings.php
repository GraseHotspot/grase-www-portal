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
    
    if($newlocationname != $location) update_location($newlocation);
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

	$smarty->assign("location", $location);
	$smarty->assign("pricemb", $pricemb);
	$smarty->assign("pricetime", $pricetime);
	$smarty->assign("currency", $currency);
	$smarty->assign("dispcurrency", $CurrencySymbols[$currency]);
	$smarty->assign("sellable_data", $sellable_data/1048576);
	$smarty->assign("useable_data", $useable_data/1048576);
	$smarty->assign("support_name", $support_name);
	$smarty->assign("support_link", $support_link);
	$smarty->assign("website_name", $website_name);
	$smarty->assign("website_link", $website_link);

//$old_error_level = error_reporting(1); // TODO: Don't have this catching stuff

// Location

function update_locationname($location)
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
	if($new_pricemb != "" && is_numeric($pricemb))
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
    global $error, $smarty, $Settings, $success;
	if($currency != "" && strlen($currency) < 4)
	{
		if($Settings->setSetting('currency', $currency))
		{
			$success[] = _("Currency updated");
			AdminLog::getInstance()->log(_("Currency updated to") ." ${CurrencySymbols[$new_currency]}");
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

    $error_data = false;
	if(isset($_POST['changedatasubmit']))
	{ 
		$error_data = "Data Limits not valid";
		$new_selldata = trim(clean_text($_POST['newsellable_data']));
		$new_usedata = trim(clean_text($_POST['newuseable_data']));
		if($new_selldata != "" && is_numeric($new_selldata) && $new_usedata != "" && is_numeric($new_usedata) )
		{
			if($Settings->setSetting('sellableData', $new_selldata*1048576) && $Settings->setSetting('useableData', $new_usedata*1048576)) // TODO: Make this octets properly and combine octets functions with other areas
			{
				$error_data = "Data Limits changed";
				AdminLog::getInstance()->log("Graph Data Limits changed");
				$smarty->assign("sellable_data", $new_selldata);
				$smarty->assign("useable_data", $new_usedata);
			}
		}
	}
	$smarty->assign("error_data", $error_data);

// Support Contact

    $error_support = false;
	if(isset($_POST['changesupportsubmit']))
	{ 
		$error_support = "Support Contact Details not valid";
		$new_supportname = trim(clean_text($_POST['newsupportname']));
		$new_supportlink = trim(clean_text($_POST['newsupportlink']));
		if($new_supportname != "" && $new_supportlink != "" && strpos($new_supportlink, ' ') === false)
		{
			if($Settings->setSetting('supportContactLink', $new_supportlink) && $Settings->setSetting('supportContactName', $new_supportname))
			{
				$error_support = "Support Contact Details Updated";
				AdminLog::getInstance()->log("Support Contact Details changed");
				$smarty->assign("support_name", $new_supportname);
				$smarty->assign("support_link", $new_supportlink);
			}
		}
	}
	$smarty->assign("error_support", $error_support);

//error_reporting($old_error_level);
	require('includes/site_settings.inc.php'); // ReRead settings
	display_page('settings.tpl');

?>


