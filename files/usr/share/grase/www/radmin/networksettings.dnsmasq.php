<?php

/* Copyright 2011 Timothy White */

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

function __autoload($class_name) {
    require_once './classes/' . $class_name . '.class.php';
}


require_once 'includes/database_functions.inc.php';

    $networkoptions = unserialize($Settings->getSetting('networkoptions'));
    
    $lastchangets = $Settings->getSetting('lastnetworkconf');

print_r($networkoptions);

$lanip = $networkoptions['lanipaddress'];
$netmask = $networkoptions['networkmask'];

$networkip = long2ip(ip2long($lanip) & ip2long($netmask));

echo "#chilli_lanip $lanip\n";
echo "#chilli_network $network\n";
echo "#chilli_netmask $netmask\n";
echo "\n";
echo "no-resolv\n";
echo "strict-order\n";

foreach($networkoptions['dnsservers'] as $dnsserver)
{
    echo "server=$dnsserver\n";
}

if($networkoptions['opendnsbogusnxdomain'])
{   
    $bogusnxdomains = array('hit-nxdomain.opendns.com');
    // TODO: plugin hook here?
    foreach($bogusnxdomains as $domainname){
        foreach(gethostbynamel($domainname) as $ip)
        {
            echo "bogus-nxdomain=$ip\n";
        }
    }
}
?>


