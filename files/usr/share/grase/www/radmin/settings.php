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

/* TODO: most of this file is repetitive. Make it more like Chilli Settings, with arrays defining options and labels, and validation types, then do generic loop */

if (isset($_POST['submit'])) {
    $newLocationName = \Grase\Clean::text($_POST['locationname']);
    $newSupportContact = \Grase\Clean::text($_POST['supportcontact']);
    $newSupportLink = \Grase\Clean::text($_POST['supportlink']);
    $newMBOptions = clean_numberarray($_POST['mboptions']);
    $newTimeOptions = clean_numberarray($_POST['timeoptions']);
    $newBandwidthOptions = clean_numberarray($_POST['bwoptions']);
    $newLocale = \Grase\Clean::text($_POST['locale']);
    $newWebsiteName = \Grase\Clean::text($_POST['websitename']);
    $newWebsiteLink = \Grase\Clean::text($_POST['websitelink']);
    // Check for changed items

    if ($newLocationName != $location) {
        updateLocation($newLocationName);
    }
    if ($newSupportContact != $support_name) {
        updateSupportContactSetting($newSupportContact);
    }
    if ($newSupportLink != $support_link) {
        updateSupportLinkSetting($newSupportLink);
    }
    if ($newLocale != $locale) {
        updateLocaleSetting($newLocale);
    }
    if ($newWebsiteName != $website_name) {
        updateWebsiteName($newWebsiteName);
    }
    if ($newWebsiteLink != $website_link) {
        updateWebsiteLink($newWebsiteLink);
    }

    // New functions to file, dont do messy way like above. Value will always be valid, as the cleaning functions should make it a valid value. We should still check the value fits how we want it to (i.e. isn't empty). We don't need to check for error up update as when we have errors we'll never come back here
    $new2timeoptions = checkGroupsTimeDropdowns($newTimeOptions);
    if ($new2timeoptions != $newTimeOptions) {
        $error[] = T_("Some time options are still in use by current groups and have been added back in");
    }

    $new2mboptions = checkGroupsDataDropdowns($newMBOptions);
    if ($new2mboptions != $newMBOptions) {
        $error[] = T_("Some data options are still in use by current groups and have been added back in");
    }

    $new2bwoptions = checkGroupsBandwidthDropdowns($newBandwidthOptions);
    if ($new2bwoptions != $newBandwidthOptions) {
        $error[] = T_("Some bandwidth options are still in use by current groups and have been added back in");
    }

    if ($new2timeoptions != $time_options) {
        $Settings->setSetting('timeOptions', $new2timeoptions);
        $success[] = T_("Time Options Updated");
    }

    if ($new2mboptions != $mb_options) {
        $Settings->setSetting('mbOptions', $new2mboptions);
        $success[] = T_("Data Options Updated");
    }

    if ($new2bwoptions != $kbit_options) {
        $Settings->setSetting('kbitOptions', $new2bwoptions);
        $success[] = T_("Bandwidth Options Updated");
    }

    // Call validate&change functions for changed items
}

// TODO: Make a proper settings file?
load_global_settings(); // Reloads settings

$templateEngine->assign("location", $location);
$templateEngine->assign("mboptions", $mb_options);
$templateEngine->assign("timeoptions", $time_options);
$templateEngine->assign("bwoptions", $kbit_options);


// Locale stuff

$fmt = new NumberFormatter($locale, NumberFormatter::CURRENCY);
$templateEngine->assign("locale", $locale);
$templateEngine->assign("currency", $fmt->getSymbol(NumberFormatter::CURRENCY_SYMBOL));
$templateEngine->assign("language", locale_get_display_language($locale));
$templateEngine->assign("region", locale_get_display_region($locale));

$templateEngine->assign("support_name", $support_name);
$templateEngine->assign("support_link", $support_link);
$templateEngine->assign("website_name", $website_name);
$templateEngine->assign("website_link", $website_link);

$templateEngine->assign("available_languages", \Grase\Locale::getAvailableLanguages());

if (sizeof($error) > 0) {
    $templateEngine->assign("error", $error);
}
if (sizeof($success) > 0) {
    $templateEngine->assign("success", $success);
}

// Location

function updateLocation($location)
{
    global $error, $templateEngine, $Settings, $success;
    if ($location == "") {
        $error[] = T_("Location name not valid");
    } else {
        if ($Settings->setSetting('locationName', $location)) {
            $success[] = T_("Location name updated");
            AdminLog::getInstance()->log(T_("Location Name changed to") . " $location");
            $templateEngine->assign(
                "Title",
                $location . " - " . APPLICATION_NAME
            ); //TODO: remove need for this with setting reload function
        } else {
            $error[] = T_("Error Saving Location Name");
        }
    }
}

// Website
function updateWebsiteName($websiteName)
{
    global $error, $Settings, $success;
    if ($websiteName == "") {
        $error[] = T_("Website name not valid");
    } else {
        if ($Settings->setSetting('websiteName', $websiteName)) {
            $success[] = T_("Website name updated");
            AdminLog::getInstance()->log(T_("Website name updated"));
        } else {
            $error[] = T_("Error Saving Website Name");
        }
    }
}

function updateWebsiteLink($websiteLink)
{
    global $error, $Settings, $success;
    if ($websiteLink == "" || strpos($websiteLink, ' ') !== false) {
        $error[] = T_("Website link not valid");
    } else {
        if ($Settings->setSetting('websiteLink', $websiteLink)) {
            $success[] = T_("Website link updated");
            AdminLog::getInstance()->log(T_("Website link updated"));
        } else {
            $error[] = T_("Error Saving Website link");
        }
    }
}


// Data and Time selections

function updateLocaleSetting($locale)
{
    global $error, $Settings, $success;

    $newlocale = Locale::parseLocale($locale);

    // If ['language'] isn't set, then we can't pick a language, so whole Locale is invalid. Region part of Locale isn't as important as Language is. Could default to English if no langauge, so Region would work, but they could just append en_ to the locale themself
    if (isset($newlocale['language'])) {
        $locale = Locale::composeLocale($newlocale);
        if ($Settings->setSetting('locale', $locale)) {
            // Apply new locale so language displays correctly from now on
            \Grase\Locale::applyLocale($locale);

            $success[] = T_("Locale updated");
            AdminLog::getInstance()->log(T_("Locale updated to") . $locale);
        } else {
            $error[] = T_("Error updating Locale");
        }
    } else {
        $error[] = T_("Invalid Locale");
    }
}


// Support Contact
function updateSupportContactSetting($supportName)
{
    global $error, $Settings, $success;
    if ($supportName == "") {
        $error[] = T_("Support name not valid");
    } else {
        if ($Settings->setSetting('supportContactName', $supportName)) {
            $success[] = T_("Support name updated");
            AdminLog::getInstance()->log(T_("Support name updated"));
        } else {
            $error[] = T_("Error Saving Support Name");
        }
    }
}

function updateSupportLinkSetting($supportLink)
{
    global $error, $Settings, $success;
    if ($supportLink == "" || strpos($supportLink, ' ') !== false) {
        $error[] = T_("Support link not valid");
    } else {
        if ($Settings->setSetting('supportContactLink', $supportLink)) {
            $success[] = T_("Support link updated");
            AdminLog::getInstance()->log(T_("Support link updated"));
        } else {
            $error[] = T_("Error Saving Support link");
        }
    }
}

$templateEngine->displayPage('settings.tpl');
