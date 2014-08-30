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
$PAGE = 'settings';
require_once 'includes/pageaccess.inc.php';


require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';

$error = array();
$success = array();

/* TODO: most of this file is repetative. Make it more like Chilli Settings, with arrays defining options and labels, and validation types, then do generic loop */

if(isset($_POST['submit']))
{
        $newlocationname    = clean_text($_POST['locationname']);
        $newsupportcontact  = clean_text($_POST['supportcontact']);    
        $newsupportlink     = clean_text($_POST['supportlink']);
//	$newpricemb         = clean_number($_POST['pricemb']);
//	$newpricetime       = clean_number($_POST['pricetime']);
	$newmboptions       = clean_numberarray($_POST['mboptions']);
	$newtimeoptions     = clean_numberarray($_POST['timeoptions']);
	$newbwoptions       = clean_numberarray($_POST['bwoptions']);	
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
//    if($newpricemb != $pricemb) update_pricemb($newpricemb);
    //if($newtimeoptions != $time_options) update_timeoptions($newtimeoptions);
    if($newlocale != $locale) update_locale($newlocale);
    if($newwebsitename != $website_name) update_websitename($newwebsitename);
    if($newwebsitelink != $website_link) update_websitelink($newwebsitelink);
    
    // New functions to file, dont do messy way like above. Value will always be valid, as the cleaning functions should make it a valid value. We should still check the value fits how we want it to (i.e. isn't empty). We don't need to check for error up update as when we have errors we'll never come back here
    $new2timeoptions = checkGroupsTimeDropdowns($newtimeoptions);
    if($new2timeoptions != $newtimeoptions) $error[] = T_("Some time options are still in use by current groups and have been added back in");
    
    $new2mboptions = checkGroupsDataDropdowns($newmboptions);
    if($new2mboptions != $newmboptions) $error[] = T_("Some data options are still in use by current groups and have been added back in");
    
    $new2bwoptions = checkGroupsBandwidthDropdowns($newbwoptions);
    if($new2bwoptions != $newbwoptions) $error[] = T_("Some bandwidth options are still in use by current groups and have been added back in");        
    
    if($new2timeoptions != $time_options)
    {
        $Settings->setSetting('timeOptions', $new2timeoptions);
        $success[] = T_("Time Options Updated");
    }
    
    if($new2mboptions != $mb_options)
    {
        $Settings->setSetting('mbOptions', $new2mboptions);
        $success[] = T_("Data Options Updated");
    }
    
    if($new2bwoptions != $kbit_options)
    {
        $Settings->setSetting('kbitOptions', $new2bwoptions);
        $success[] = T_("Bandwidth Options Updated");
    }

    // Call validate&change functions for changed items
}

// TODO: Make a proper settings file?
load_global_settings(); // Reloads settings

	$smarty->assign("location", $location);
//	$smarty->assign("pricemb", displayLocales($pricemb));
//	$smarty->assign("pricetime", displayLocales($pricetime));
	$smarty->assign("mboptions", $mb_options);
	$smarty->assign("timeoptions", $time_options);	
	$smarty->assign("bwoptions", $kbit_options);		
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

/*
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
*/

// Data and Time selections

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


