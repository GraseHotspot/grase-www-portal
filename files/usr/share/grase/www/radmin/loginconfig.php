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
    'hidefooter' => array(
        "label" => T_("Login Screen Footer"),
        "description" => T_("Hide footer from login screen"),
        "type" => "bool"),
    'disablejavascript' => array(
        "label" => T_("Disable Javascript"),
        "description" => T_("Force all logins to be through the less secure non-javascript method"),
        "type" => "bool"),  
        
    'logintitle' => array(
        "label" => T_("Page Title"),
        "description" => T_("The page title that is displayed on the login page"),
        "type" => "text"),              
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
    
    foreach($multiloginoptions as $multioption => $attributes)
    {
        $postvalue = array();
        foreach($_POST[$multioption] as $value)
        {
            switch ($attributes['type'])
            {
                case "string":
                    $postvalue[] = clean_text($value);
                    break;
                case "int":
                    $postvalue[] = clean_int($value);
                    break;
                case "number":
                    $postvalue[] = clean_number($value);
                    break;
                    
            }
        
//        if($postvalue != $attributes['value'])
//        {
//        }
        }
        $postvalue = array_filter($postvalue);
        sort($postvalue);        
        sort($attributes['value']);
     
        if($postvalue != $attributes['value'])
        {
            DatabaseFunctions::getInstance()->delPortalConfig($multioption);
            foreach($postvalue as $value)
            {
                DatabaseFunctions::getInstance()->setPortalConfigMulti($multioption, $value);
            }
            $success[] = sprintf(
                T_("%s portal config option update"),
                $attributes['label']);
                

        
        }

        
    }

    // Update last change timestamp if we actually changed something
    if(sizeof($success) > 0)
        $Settings->setSetting('lastchangeportalconf', time());
        
    // Call validate&change functions for changed items
    load_loginoptions(); // Reload due to changes in POST    
}

	

function load_loginoptions()
{
    global $multiloginoptions, $singleloginoptions, $Settings;
    // Load all Multi option values from database 

    foreach($multiloginoptions as $multioption => $attributes)
    {
        $multiloginoptions[$multioption]['value'] = 
            DatabaseFunctions::getInstance()->getPortalConfigMulti($multioption);
    }

    // Load all Single option values from database

    foreach($singleloginoptions as $singleoption => $attributes)
    {
        $singleloginoptions[$singleoption]['value'] = 
            $Settings->getSetting($singleoption);
    }
}

    
//    DatabaseFunctions::getInstance()->setPortalConfigSingle('macpasswd', 'passwords');
//    DatabaseFunctions::getInstance()->setPortalConfigSingle('defidletimeout', '600');
//    DatabaseFunctions::getInstance()->setPortalConfigMulti('uamallowed', 'google.com.au');    

    
if(sizeof($error) > 0) $smarty->assign("error", $error);	
if(sizeof($success) > 0) $smarty->assign("success", $success);

    $smarty->assign("singleloginoptions", $singleloginoptions);
    $smarty->assign("multiloginoptions", $multiloginoptions);    
	display_page('loginconfig.tpl');

?>


