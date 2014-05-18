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

$PAGE = 'loginconfig';
require_once 'includes/pageaccess.inc.php';

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';

$error = array();
$success = array();

// Options for login Config that can be more than 1
$multiloginoptions = array(
    /*'uamallowed' => array(
        "label" => T_("Walled Garden allowed hosts"),
        "description" => T_("IP's and Hostnames that are accesible without logging in. DNS Lookup is only done at startup time so not suitable for domains with Round Robin IP Addresses"),
        "type" => "string"),
    'uamdomain' => array(
        "label" => T_("Walled Garden allowed domains"),
        "description" => T_("Domains (and their subdomains) that are accesible without logging in."),
        "type" => "string"),           */
    );
    
// Options for login Config that can only be one
$singleloginoptions = array(
    'hideheader' => array(
        "label" => T_("Login Screen Title"),
        "description" => T_("Hide Title (header) from login screen"),
        "type" => "bool"),
    'hidefooter' => array(
        "label" => T_("Login Screen Footer"),
        "description" => T_("Hide footer from login screen. Please consider adding a link back to http://grasehotspot.org if you are hiding the footer"),
        "type" => "bool"),
    'hidelogoutbookmark' => array(
        "label" => T_("Logout Bookmark"),
        "description" => T_("Hide Bookmark logout link"),
        "type" => "bool"),        
    'hidehelplink' => array(
        "label" => T_("Help Link"),
        "description" => T_("Hide Help and Information link from login page (still shows in footer if footer is enabled)"),
        "type" => "bool"),
    'disablejavascript' => array(
        "label" => T_("Disable Javascript Login"),
        "description" => T_("Force all logins to be through the less secure non-javascript method"),
        "type" => "bool"),  
    'disableallcss' => array(
        "label" => T_("Disable All Default CSS"),
        "description" => T_("All css files will be excluded from the login pages, and only the css below (Main CSS) will be used"),
        "type" => "bool"),          
        
    'logintitle' => array(
        "label" => T_("Page Title"),
        "description" => T_("The page title that is displayed on the login page"),
        "type" => "text"),              

     'autocreategroup' => array(
        "label" => T_("Free Login Group"),
        "description" => T_("The group to create 'Free Login' users in. Leave blank to disable free logins"),
        "type" => "text"),              
   
    );


// Templates    
$templateoptions = array(
    'maincss' => array(
        "label" => T_("Main CSS"),
        "description" => T_("Cascading style sheet that is applied to all portal pages (use !important to override a style if your settings here don't seem to work, it may be that the builtin css has a more specific selector than your one here, look at radmin.css for id's and classes)"),
        "type" => "css"),
    'helptext' => array(
        "label" => T_("Help and Information Page"),
        "description" => T_("Help and Information page contents"),
//        "location" => "div id: tpl_helptext",
        "type" => "html"),
    'loginhelptext' => array(
        "label" => T_("Login Help HTML"),
        "description" => T_("Help text (and HTML) displayed on login page above login form"),
//        "location" => "div id: tpl_loginhelptext",
        "type" => "html"),        
        
    'belowloginhtml' => array(
        "label" => T_("HTML Below login form"),
        "description" => T_("HTML to insert below login form"),
        "type" => "html"),        
    'loggedinnojshtml' => array(
        "label" => T_("Logged In HTML"),
        "description" => T_("HTML for successful login when not using javascript"),
        "type" => "html"),        

    'termsandconditions' => array(
        "label" => T_("Terms and Conditions"),
        "description" => T_("Terms and Conditions of use (HTML)"),
        "type" => "html"),
);           

    
load_loginoptions();   

if(isset($_POST['submit']))
{
    
    foreach($singleloginoptions as $singleoption => $attributes)
    {
        switch ($attributes['type'])
        {
            default:
            case "string":
                $postvalue = trim(clean_text($_POST[$singleoption]));
                break;
            case "int":
                $postvalue = trim(clean_int($_POST[$singleoption]));
                break;
            case "number":
                $postvalue = trim(clean_number($_POST[$singleoption]));
                break;
            case "bool":
                if(isset($_POST[$singleoption]))
                    $postvalue = 'TRUE';
                else
                    $postvalue = 'FALSE';
                break;
                
        }
        
        if($postvalue != $attributes['value'])
        {
            // Update options in database
            $Settings->setSetting($singleoption, $postvalue);

            $success[] = sprintf(
                T_("%s login config option update"),
                $attributes['label']);
        }
        
    }
    
    foreach($templateoptions as $templateoption => $attributes)
    {
        /*switch ($attributes['type'])
        {
            default:
            case "string":
                $postvalue = trim(clean_text($_POST[$singleoption]));
                break;
            case "int":
                $postvalue = trim(clean_int($_POST[$singleoption]));
                break;
            case "number":
                $postvalue = trim(clean_number($_POST[$singleoption]));
                break;
            case "bool":
                if(isset($_POST[$singleoption]))
                    $postvalue = 'TRUE';
                else
                    $postvalue = 'FALSE';
                break;
                
        }*/
        // TODO: check that length isn't longer than maximum database length as it will be truncated
        $postvalue = trim($_POST[$templateoption]);
        
        if($postvalue != $attributes['value'])
        {
            // Update options in database
            $Settings->setTemplate($templateoption, $postvalue);

            $success[] = sprintf(
                T_("%s login config option update"),
                $attributes['label']);
        }
        
    }    
    
    // Call validate&change functions for changed items
    load_loginoptions(); // Reload due to changes in POST    
}

	

function load_loginoptions()
{
    global $multiloginoptions, $singleloginoptions, $templateoptions, $Settings;
    // Load all Multi option values from database 

    /*foreach($multiloginoptions as $multioption => $attributes)
    {
        $multiloginoptions[$multioption]['value'] = 
            DatabaseFunctions::getInstance()->getPortalConfigMulti($multioption);
    }*/

    // Load all Single option values from database

    foreach($singleloginoptions as $singleoption => $attributes)
    {
        $singleloginoptions[$singleoption]['value'] = 
            $Settings->getSetting($singleoption);
    }
    
    // Load all templates
    foreach($templateoptions as $template => $attributes)
    {
        $templateoptions[$template]['value'] =
            $Settings->getTemplate($template);
    }
}

if(sizeof($error) > 0) $smarty->assign("error", $error);	
if(sizeof($success) > 0) $smarty->assign("success", $success);

    $smarty->assign("singleloginoptions", $singleloginoptions);
    $smarty->assign("templateoptions", $templateoptions);    
    $smarty->assign("multiloginoptions", $multiloginoptions);    
	display_page('loginconfig.tpl');

?>


