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
$PAGE = 'settings';
require_once 'includes/pageaccess.inc.php';


require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';

$error = array();
$success = array();

/* TODO: most of this file is repetative. Make it more like Chilli Settings, with arrays defining options and labels, and validation types, then do generic loop */

if(isset($_POST['submit']))
{
    $newlocationname    = clean_text($_POST['locationname']);
    $newsupportcontact  = clean_text($_POST['supportcontact']);    
    $newsupportlink     = clean_text($_POST['supportlink']);
	$newpricemb         = clean_number($_POST['pricemb']);
	$newpricetime       = clean_number($_POST['pricetime']);
	//$newcurrency        = clean_text($_POST['currency']);
	$newlocale          = clean_text($_POST['locale']);
	$newwebsitename     = clean_text($_POST['websitename']);
	$newwebsitelink     = clean_text($_POST['websitelink']);
	//$newsellabledata    = clean_number($_POST['sellable_data']);
	//$newuseabledata     = clean_number($_POST['useable_data']);	    
    // Check for changed items
    
    if($newlocationname != $location) update_location($newlocationname);
    if($newsupportcontact != $support_name) update_supportcontact($newsupportcontact);    
    if($newsupportlink != $support_link) update_supportlink($newsupportlink);    
    if($newpricemb != $pricemb) update_pricemb($newpricemb);
    if($newpricetime != $pricetime) update_pricetime($newpricetime);
    //if($newcurrency != $currency) update_currency($newcurrency);
    if($newlocale != $locale) update_locale($newlocale);
    if($newwebsitename != $website_name) update_websitename($newwebsitename);
    if($newwebsitelink != $website_link) update_websitelink($newwebsitelink);
    //if($newsellabledata != $sellable_data) update_sellabledata($newsellabledata);
    //if($newuseabledata != $useable_data) update_useabledata($newuseabledata);    
    // Call validate&change functions for changed items
}

// TODO: Make a proper settings file?
load_global_settings(); // Reloads settings

	$smarty->assign("location", $location);
	$smarty->assign("pricemb", displayLocales($pricemb));
	$smarty->assign("pricetime", displayLocales($pricetime));
	//$smarty->assign("currency", $currency);
	
	// Locale stuff
	
    $fmt = new NumberFormatter( $locale, NumberFormatter::CURRENCY );
	$smarty->assign("locale", $locale);
    $smarty->assign("currency", $fmt->getSymbol(NumberFormatter::CURRENCY_SYMBOL));	
    $smarty->assign("language",  locale_get_display_language($locale));
    $smarty->assign("region", locale_get_display_region($locale));	
	
		
	//$smarty->assign("dispcurrency", $CurrencySymbols[$currency]);
	//$smarty->assign("sellable_data", $sellable_data);
	//$smarty->assign("useable_data", $useable_data);
	$smarty->assign("support_name", $support_name);
	$smarty->assign("support_link", $support_link);
	$smarty->assign("website_name", $website_name);
	$smarty->assign("website_link", $website_link);
	
	$smarty->assign("available_languages", get_available_languages());
	
if(sizeof($error) > 0) $smarty->assign("error", $error);	
if(sizeof($success) > 0) $smarty->assign("success", $success);

//$old_error_level = error_reporting(1); // TODO: Don't have this catching stuff

// Location

function update_location($location)
{
    global $error, $smarty, $Settings, $success;
    if($location == "") $error[] = T_("Location name not valid");
    else {
	    if($Settings->setSetting('locationName', $location))
	    {
		    $success[] = T_("Location name updated");
		    AdminLog::getInstance()->log(T_("Location Name changed to")." $location");
		    $smarty->assign("Title", $location . " - " . APPLICATION_NAME); //TODO: remove need for this with setting reload function
	    }
	    else
	    {
	        $error[] = T_("Error Saving Location Name");
	    }    
    }
}

// Website
function update_websitename($websitename)
{
    global $error, $smarty, $Settings, $success;
    if($websitename == "") $error[] = T_("Website name not valid");    
    else
    {
        if($Settings->setSetting('websiteName', $websitename))
        {
            $success[] = T_("Website name updated");
			AdminLog::getInstance()->log(T_("Website name updated"));        
        }
        else
        {
            $error[] = T_("Error Saving Website Name");
        }
    }
}

function update_websitelink($websitelink)
{
    global $error, $smarty, $Settings, $success;
    if($websitelink == "" || strpos($websitelink, ' ') !== false) $error[] = T_("Website link not valid");    
    else
    {
        if($Settings->setSetting('websiteLink', $websitelink))
        {
            $success[] = T_("Website link updated");
			AdminLog::getInstance()->log(T_("Website link updated"));        
        }
        else
        {
            $error[] = T_("Error Saving Website link");
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
			$success[] = T_("Price per MiB updated");
			AdminLog::getInstance()->log(T_("Price per MiB updated"));
		}
		else
		{
			$error[] = T_("Error saving Price per MiB");
		}
	}
	else
	{
		$error[] = T_("Invalid Price per MiB");
	}
}	

function update_pricetime($pricetime)
{
    global $error, $smarty, $Settings, $success;
	if($pricetime != "" && is_numeric($pricetime))
	{
		if($Settings->setSetting('priceMinute', $pricetime))
		{
			$success[] = T_("Price per Minute Updated");
			AdminLog::getInstance()->log(T_("Price per Minute Updated"));
		}else
		{
			$error[] = T_("Error saving Price per Minute");
		}
	}else
	{
		$error[] = T_("Invalid Price per Minute");
	}
}

function update_currency($currency)
{   // No longer needed
    global $error, $smarty, $Settings, $success, $CurrencySymbols;
	if($currency != "" && strlen($currency) < 4)
	{
		if($Settings->setSetting('currency', $currency))
		{
			$success[] = T_("Currency updated");
			AdminLog::getInstance()->log(T_("Currency updated to") ." ${CurrencySymbols[$currency]}");
		}
		else
		{
			$error[] = T_("Error saving Currency");
		}
	}else
	{
		$error[] = T_("Invalid Currency");
	}


}

function update_locale($locale)
{
	global $error, $smarty, $Settings, $success;

	//$locale = locale_accept_from_http($locale);
	$newlocale = Locale::parseLocale($locale);
	
	// If ['language'] isn't set, then we can't pick a language, so whole Locale is invalid. Region part of Locale isn't as important as Language is. Could default to English if no langauge, so Region would work, but they could just append en_ to the locale themself 
	if(isset($newlocale['language']))
	{
		$locale = Locale::composeLocale($newlocale);
		if($Settings->setSetting('locale', $locale))
		{
			// Apply new locale so language displays correctly from now on
			apply_locale($locale);

			$success[] = T_("Locale updated");
			AdminLog::getInstance()->log(T_("Locale updated to") . $locale);
		}
		else
		{
			$error[] = T_("Error updating Locale");
		}
	}else
	{
		$error[] = T_("Invalid Locale");
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
            $success[] = T_("Sellable Data Limit Update");
			AdminLog::getInstance()->log(T_("Sellable Data Limit Update"));        
        }
        else
        {
            $error[] = T_("Error updating Sellable Data Limit");
        }
    }
    else
    {
        $error[] = T_("Invalid value for Sellable Data");
    }
}

function update_useabledata($useabledata)
{
    global $error, $smarty, $Settings, $success;
    if($useabledata != "" && is_numeric($useabledata))
    {
        if($Settings->setSetting('useableData', $useabledata))
        {
            $success[] = T_("Useable Data Limit Update");
			AdminLog::getInstance()->log(T_("Useable Data Limit Update"));        
        }
        else
        {
            $error[] = T_("Error updating Useable Data Limit");
        }
    }
    else
    {
        $error[] = T_("Invalid value for Useable Data");
    }
}

// Support Contact
function update_supportcontact($supportname)
{
    global $error, $smarty, $Settings, $success;
    if($supportname == "") $error[] = T_("Support name not valid");    
    else
    {
        if($Settings->setSetting('supportContactName', $supportname))
        {
            $success[] = T_("Support name updated");
			AdminLog::getInstance()->log(T_("Support name updated"));        
        }
        else
        {
            $error[] = T_("Error Saving Support Name");
        }
    }
}

function update_supportlink($supportlink)
{
    global $error, $smarty, $Settings, $success;
    if($supportlink == "" || strpos($supportlink, ' ') !== false) $error[] = T_("Support link not valid");    
    else
    {
        if($Settings->setSetting('supportContactLink', $supportlink))
        {
            $success[] = T_("Support link updated");
			AdminLog::getInstance()->log(T_("Support link updated"));        
        }
        else
        {
            $error[] = T_("Error Saving Support link");
        }
    }
}

//error_reporting($old_error_level);
	//require('includes/site_settings.inc.php'); // ReRead settings
	display_page('settings.tpl');

?>


