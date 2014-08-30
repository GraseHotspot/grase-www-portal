<?php

/* Copyright 2012 Timothy White */

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

$PAGE = 'paypalconfig';
require_once 'includes/pageaccess.inc.php';

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';

// Options for PayPal Payment Gateway
$singlepaypaloptions = array(
    'ticketselling' => array(
        "label" => T_("Allow Sell Tickets Automatically"),
        "description" => T_("When enabled, this allows users to purchase tickets automatically, via different online methods, e.g. PayPal"),
        "type" => "bool"),
    'paypalEnabled' => array(
        "label" => T_("Enable PayPal payment Gateway"),
        "description" => T_("Enable the PayPal payment gateway so you can sell users tickets via Paypal. Requires that Selling tickets is enabled"),
        "type" => "bool",
        "dependsOn" => 'ticketselling'),
    'paypalbrandname' => array(
        "label" => T_("Brand Name"),
        "description" => T_("The Brand Name that will appear on the PayPal site "),
        "type" => "string",
        "dependsOn" => 'paypalEnabled'),
    'paypalAPIusername' => array(
        "label" => T_("PayPal API Username"),
        "description" => T_("API Username"),
        "type" => "string",
        "dependsOn" => 'paypalEnabled'),
    'paypalAPIpassword' => array(
        "label" => T_("PayPal API Password"),
        "description" => T_("API Password"),
        "type" => "string",
        "dependsOn" => 'paypalEnabled'),
    'paypalAPIsignature' => array(
        "label" => T_("PayPal API Signature"),
        "description" => T_("API Signature"),
        "type" => "string",
        "dependsOn" => 'paypalEnabled'),                
    'paypalCurrency' => array(
        "label" => T_("PayPal Currency"),
        "description" => T_("Currency Code for PayPal API"),
        "type" => "string",
        "dependsOn" => 'paypalEnabled'),     
    );    
    
    
    
function load_paypaloptions()
{
    global $singlepaypaloptions, $Settings;

    // Load all Single option values from database

    foreach($singlepaypaloptions as $singleoption => $attributes)
    {
        $singlepaypaloptions[$singleoption]['value'] = 
            $Settings->getSetting($singleoption);
    }
}    

load_paypaloptions();   

$error = array();
$success = array();

if(isset($_POST['submit']))
{

    foreach($singlepaypaloptions as $singleoption => $attributes)
    {
        switch ($attributes['type'])
        {
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
                T_("%s PayPal config option update"),
                $attributes['label']);
        }
        
    }
    
    load_paypaloptions();
    
    if($singlepaypaloptions['paypalEnabled']['value'] == 'TRUE')
    {
        // Settings are enabled, verify
    // After loading paypal details we attempt to validate the details, if validation fails we disable the paypal enabled flag and show an error
$pp = new Paypal();
$pp->APIsettings($Settings);
$response = $pp->paypalApiRequest('GetBalance', array());
if(is_array($response) && $response['ACK'] == 'Success') { //Request successful

}



if(sizeof($error) > 0) $smarty->assign("error", $error);	
if(sizeof($success) > 0) $smarty->assign("success", $success);

    $smarty->assign("singlepaypaloptions", $singlepaypaloptions);
	display_page('paypalconfig.tpl');


?>
