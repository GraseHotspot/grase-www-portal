<?php

/* Copyright 2011 Timothy White */

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

$PAGE = 'netconfig';
require_once 'includes/pageaccess.inc.php';

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';

require_once 'includes/network_functions.inc.php';

$error = array();
$success = array();

// Options for Chilli Config that can be more than 1
$multinetworkoptions = array(
    'dnsservers' => array(
        "label" => T_("DNS Servers"),
        "description" => T_("IP Addresses of DNS Servers. All clients will use the gateway as the DNS server which will use the addresses listed here to do DNS lookups. Dnsmasq WILL NOT get default servers from DHCP or /etc/resolv.conf and will default to OpenDNS Family Shield"),
        "type" => "ip"),
    'bogusnx' => array(
        "label" => T_("Bogus NXDOMAIN"),
        "description" => T_("IP Addresses of Bogus NXDOMAIN returns. All DNS replies that contain these ip address will be transformed into a NXDOMAIN result"),
        "type" => "ip"),        
    );
    
// Options for Chilli Config that can only be one
$singlenetworkoptions = array(
    'lanipaddress' => array(
        "label" => T_("LAN IP Address"),
        "description" => T_("The server IP address that is used on the LAN side (Coova-Chilli) of the network. This will be the gateway address for all clients, as well as the DNS server the clients access. For default Squid config this should be a private ip address."),
        "type" => "ip",
        "required" => "true"),
    /*'network' => array(
        "label" => T_("LAN Network Address"),
        "description" => T_("Network address to use for clients network. (i.e. 192.168.0.0)"),
        "type" => "ip",
        "required" => "true"),*/
    'networkmask' => array(
        "label" => T_("LAN Network Mask"),
        "description" => T_("Network mask to use for clients network. (i.e. 255.255.255.0). DHCP range and network address will be calculated from this and the LAN IP Address."),
        "type" => "ip",
        "required" => "true"),  
    'opendnsbogusnxdomain' => array(
        "label" => T_("Bogus NXDOMAIN (OpenDNS)"),
        "description" => T_("Some DNS Providers return bogus NXDOMAIN to redirect you to their search engine. Block the bogus ip's and return a real NXDOMAIN for OpenDNS."),
        "type" => "bool"),
    /* TODO: Move printing options to own settings page */
    'printSSID' => array(
	"label" => T_("Wireless Network Name (SSID)"),
	"description" => T_("Wireless Network Name that clients connect to. Currently does not modify any settings but if set will print on the tickets"),
	"type" => "string"),
    'printGroup' => array(
	"label" => T_("Print Ticket Type"),
	"description" => T_("Print the ticket type (Group name) on tickets"),
	"type" => "bool"),
    'printExpiry' => array(
	"label" => T_("Print Ticket Expiry"),
	"description" => T_("Print the expiry on tickets"),
	"type" => "bool"),	
    );    

$wanif = array(net_get_wan_if());
$lanifs = available_lan_ifs($wanif[0]);


// Options for Chilli Config that can only be one but selected from a list
$selectnetworkoptions = array(
    'lanif' => array(
        "label" => T_("LAN Network Interface"),
        "description" => T_("The Network Interface that is connected to the LAN of the Hotspot (the side the clients connect to)"),
        "type" => "string",
        "required" => "true",
        "options" => $lanifs),
    'wanif' => array(
        "label" => T_("WAN Network Interface"),
        "description" => T_("The Network Interface that is connected to the WAN of the Hotspot (the side the internet is connected to)"),
        "type" => "string",
        "required" => "true",
        "options" => $wanif),
        
    );    

    
load_networkoptions();   

if(isset($_POST['submit']))
{

    $networkoptions = array();
    
    foreach($singlenetworkoptions as $singleoption => $attributes)
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
            case "ip":
                $postvalue = long2ip(ip2long(trim($_POST[$singleoption])));
                break;
            case "bool":
                $postvalue = isset($_POST[$singleoption]);
                break;
                
        }
        
        $networkoptions[$singleoption] = $postvalue;
        
    }
    
    foreach($selectnetworkoptions as $selectoption => $attributes)
    {
        switch ($attributes['type'])
        {
            case "string":
                $postvalue = trim(clean_text($_POST[$selectoption]));
                // TODO Validate from list of valid vars
                break;
            /*
            case "int":
                $postvalue = trim(clean_int($_POST[$singleoption]));
                break;
            case "number":
                $postvalue = trim(clean_number($_POST[$singleoption]));
                break;
            case "ip":
                $postvalue = long2ip(ip2long(trim($_POST[$singleoption])));
                break;
            case "bool":
                $postvalue = isset($_POST[$singleoption]);
                break;
            */
                
        }
        
        $networkoptions[$selectoption] = $postvalue;
        
    }    
    
    foreach($multinetworkoptions as $multioption => $attributes)
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
                case "ip":
                    if(trim($value))
                        $postvalue[] = long2ip(ip2long(trim($value)));
                    break;                     
                    
            }
        
//        if($postvalue != $attributes['value'])
//        {
//        }
        }
        $postvalue = array_filter($postvalue);
        
        $networkoptions[$multioption] = $postvalue;
     
    }
    
    // TODO: validate network settings
    
    $Settings->setSetting('networkoptions', serialize($networkoptions));

    // Update last change timestamp if we actually changed something
    //if(sizeof($success) > 0)
        $Settings->setSetting('lastnetworkconf', time());
        
    // Call validate&change functions for changed items
    load_networkoptions(); // Reload due to changes in POST    
}

	

function load_networkoptions()
{
    global $multinetworkoptions, $singlenetworkoptions, $selectnetworkoptions, $Settings;
    // Load all Multi option values from database 
    
    $networkoptions = unserialize($Settings->getSetting('networkoptions'));

    foreach($multinetworkoptions as $multioption => $attributes)
    {
        $multinetworkoptions[$multioption]['value'] = $networkoptions[$multioption];
    }
    
    // Load all Single option values from database

    foreach($singlenetworkoptions as $singleoption => $attributes)
    {
        $singlenetworkoptions[$singleoption]['value'] = $networkoptions[$singleoption];
    }
    
    // Load all Single Selection option values from database

    foreach($selectnetworkoptions as $selectoption => $attributes)
    {
        $selectnetworkoptions[$selectoption]['value'] = $networkoptions[$selectoption];
    }
    
        
}

    // Check when /etc/chilli/local.conf was last updated and compare to $Settings->gettSetting('lastchangechilliconfig');
    $localconfts = filemtime('/etc/dnsmasq.d/01-grasehotspot');
    $lastchangets = $Settings->getSetting('lastnetworkconf');
    if($localconfts < $lastchangets)
    {
        $error[] = T_("Changes pending Reload");
    }else{
        $success[] = T_("Settings match running config");
    }
    
    $smarty->assign("networkconfigstatus", date('r',$localconfts));
    $smarty->assign("lastnetworkconfigstatus", date('r',$lastchangets));            
    
if(sizeof($error) > 0) $smarty->assign("error", $error);	
if(sizeof($success) > 0) $smarty->assign("success", $success);

    $smarty->assign("singlenetworkoptions", $singlenetworkoptions);
    $smarty->assign("selectnetworkoptions", $selectnetworkoptions);    
    $smarty->assign("multinetworkoptions", $multinetworkoptions);    
	display_page('netconfig.tpl');

?>


