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

function net_get_wan_if()
{
    /* Based on default route, get network interface that is the "gateway" (WAN)
     * interface
     */
     
     $default_wanif = 'eth0';
     
     $routes = file('/proc/net/route');
     foreach($routes as $route)
     {
        $parms = explode("\t", $route);
        /*
            [0] => Iface
            [1] => Destination
            [2] => Gateway 
            [3] => Flags
            [4] => RefCnt
            [5] => Use
            [6] => Metric
            [7] => Mask
            [8] => MTU
            [9] => Window
            [10] => IRTT 
        */
        
        // Filter out tunnels and loopbacks
        if(stripos($parms[0], 'tun') !== FALSE)
            continue;
        if(stripos($parms[0], 'lo') !== FALSE)
            continue;
        
        // If destination and mask are 0.0.0.0 then this is a default route    
        if($parms[1] == "00000000" && $parms[7] == "00000000")
        {
            $default_gateway = $parms[2]; // Future use?
            $default_wanif = trim($parms[0]);
        }
     }
     
     return $default_wanif;
}

function available_lan_ifs($wanif = '')
{
    /* Show all available network interfaces that we can be using for the LAN
     * interface
     */
     if($wanif == '') $wanif = net_get_wan_if();
     $devs = file('/proc/net/dev');
     $lanifs = array();
     
     // Get rid of junk at start
     array_shift($devs);
     array_shift($devs);
     
     foreach($devs as $dev)
     {
        $parms = explode(":", $dev, 2);
        if(stripos($parms[0], 'tun') !== FALSE)
            continue;
        if(stripos($parms[0], 'lo') !== FALSE)
            continue;        
        //if(stripos($parms[0], 'vboxnet') !== FALSE)
        //    continue;            
        //var_dump(array(trim($parms[0]), $wanif, trim($parms[0]) == $wanif));
        if(trim($parms[0]) != $wanif)
            $lanifs[] = trim($parms[0]);
     }

     return $lanifs;
}

function default_net_ifs()
{
    $default_wanif = net_get_wan_if();
    $lanifs = available_lan_ifs($default_wanif);
    $lanifs_order_pref = array('br0', 'wlan0', 'eth0', 'eth1');
    $lanifs = array_intersect($lanifs_order_pref, $lanifs);
    if(count($lanifs) == 0)
    {
        // No valid lan interfaces in array, select next best
        if($default_wanif != 'eth0')
        {
            $default_lanif = 'eth0';
        }else{
            $default_lanif = 'eth1';
        }
    }else{
        // Valid options in lanifs, select top option
        $default_lanif = array_shift($lanifs);
    }
    
    return array('lanif' => $default_lanif, 'wanif' => $default_wanif);
}



?>
