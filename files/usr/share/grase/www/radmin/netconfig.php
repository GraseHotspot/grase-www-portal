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

$error = array();
$success = array();

// Options for Chilli Config that can be more than 1
$multiNetworkOptions = array(
    'dnsservers' => array(
        "label" => T_("DNS Servers"),
        "description" => T_(
            "IP Addresses of DNS Servers. All clients will use the gateway as the DNS server which will use the
            addresses listed here to do DNS lookups. Dnsmasq WILL NOT get default servers from DHCP or /etc/resolv.conf
            and will default to OpenDNS Family Shield"
        ),
        "type" => "ip"
    ),
    'bogusnx' => array(
        "label" => T_("Bogus NXDOMAIN"),
        "description" => T_(
            "IP Addresses of Bogus NXDOMAIN returns. All DNS replies that contain these ip address will be transformed
            into a NXDOMAIN result"
        ),
        "type" => "ip"
    ),
);

// Options for Chilli Config that can only be one
$singleNetworkOptions = array(
    'lanipaddress' => array(
        "label" => T_("LAN IP Address"),
        "description" => T_(
            "The server IP address that is used on the LAN side (Coova-Chilli) of the network. This will be the gateway
            address for all clients, as well as the DNS server the clients access. For default Squid config this should
            be a private ip address."
        ),
        "type" => "ip",
        "required" => "true"
    ),
    /*'network' => array(
        "label" => T_("LAN Network Address"),
        "description" => T_("Network address to use for clients network. (i.e. 192.168.0.0)"),
        "type" => "ip",
        "required" => "true"),*/
    'networkmask' => array(
        "label" => T_("LAN Network Mask"),
        "description" => T_(
            "Network mask to use for clients network. (i.e. 255.255.255.0). DHCP range and network address will be
            calculated from this and the LAN IP Address."
        ),
        "type" => "ip",
        "required" => "true"
    ),
);

$wanif = array(\Grase\Util::getNetworkWANIF());
$lanifs = \Grase\Util::getAvailableLANIFS($wanif[0]);

// Options for Chilli Config that can only be one but selected from a list
$selectNetworkOptions = array(
    'lanif' => array(
        "label" => T_("LAN Network Interface"),
        "description" => T_(
            "The Network Interface that is connected to the LAN of the Hotspot (the side the clients connect to)"
        ),
        "type" => "string",
        "required" => "true",
        "options" => $lanifs
    ),
    'wanif' => array(
        "label" => T_("WAN Network Interface"),
        "description" => T_(
            "The Network Interface that is connected to the WAN of the Hotspot (the side the internet is connected to)"
        ),
        "type" => "string",
        "required" => "true",
        "options" => $wanif
    ),

);


loadNetworkOptions();

if (isset($_POST['submit'])) {
    $networkOptions = array();
    foreach ($singleNetworkOptions as $singleOption => $attributes) {
        switch ($attributes['type']) {
            case "string":
                $postValue = trim(\Grase\Clean::text($_POST[$singleOption]));
                break;
            case "int":
                $postValue = trim(clean_int($_POST[$singleOption]));
                break;
            case "number":
                $postValue = trim(clean_number($_POST[$singleOption]));
                break;
            case "ip":
                $postValue = long2ip(ip2long(trim($_POST[$singleOption])));
                break;
            case "bool":
                $postValue = isset($_POST[$singleOption]);
                break;
        }
        $networkOptions[$singleOption] = $postValue;
    }

    foreach ($selectNetworkOptions as $selectOption => $attributes) {
        switch ($attributes['type']) {
            case "string":
                $postValue = trim(\Grase\Clean::text($_POST[$selectOption]));
                // TODO Validate from list of valid vars
                break;
        }
        $networkOptions[$selectOption] = $postValue;
    }

    foreach ($multiNetworkOptions as $multiOption => $attributes) {
        $postValue = array();
        foreach ($_POST[$multiOption] as $value) {
            switch ($attributes['type']) {
                case "string":
                    $postValue[] = \Grase\Clean::text($value);
                    break;
                case "int":
                    $postValue[] = clean_int($value);
                    break;
                case "number":
                    $postValue[] = clean_number($value);
                    break;
                case "ip":
                    if (trim($value)) {
                        $postValue[] = long2ip(ip2long(trim($value)));
                    }
                    break;
            }
        }
        $postValue = array_filter($postValue);
        $networkOptions[$multiOption] = $postValue;
    }

    // TODO: validate network settings
    $Settings->setSetting('networkoptions', serialize($networkOptions));

    // Update last change timestamp if we actually changed something
    //if(sizeof($success) > 0)
    $Settings->setSetting('lastnetworkconf', time());

    // Call validate&change functions for changed items
    // Reload due to changes in POST
    loadNetworkOptions();
}

function loadNetworkOptions()
{
    global $multiNetworkOptions, $singleNetworkOptions,
           $selectNetworkOptions, $Settings;

    // Load all Multi option values from database
    $networkOptions = unserialize($Settings->getSetting('networkoptions'));
    foreach ($multiNetworkOptions as $multioption => $attributes) {
        $multiNetworkOptions[$multioption]['value'] = $networkOptions[$multioption];
    }

    // Load all Single option values from database
    foreach ($singleNetworkOptions as $singleoption => $attributes) {
        $singleNetworkOptions[$singleoption]['value'] = $networkOptions[$singleoption];
    }

    // Load all Single Selection option values from database
    foreach ($selectNetworkOptions as $selectoption => $attributes) {
        $selectNetworkOptions[$selectoption]['value'] = $networkOptions[$selectoption];
    }
}

// Check when /etc/chilli/local.conf was last updated and compare to $Settings->getSetting('lastnetworkconf');
$localConfTimestamp = filemtime('/etc/dnsmasq.d/01-grasehotspot');
$lastChangedTimestamp = $Settings->getSetting('lastnetworkconf');
if ($localConfTimestamp < $lastChangedTimestamp) {
    $error[] = T_("Changes pending Reload");
} else {
    $success[] = T_("Settings match running config");
}

$templateEngine->assign("networkconfigstatus", date('r', $localConfTimestamp));
$templateEngine->assign("lastnetworkconfigstatus", date('r', $lastChangedTimestamp));

if (sizeof($error) > 0) {
    $templateEngine->assign("error", $error);
}
if (sizeof($success) > 0) {
    $templateEngine->assign("success", $success);
}


$templateEngine->assign("singlenetworkoptions", $singleNetworkOptions);
$templateEngine->assign("selectnetworkoptions", $selectNetworkOptions);
$templateEngine->assign("multinetworkoptions", $multiNetworkOptions);
$templateEngine->displayPage('netconfig.tpl');
