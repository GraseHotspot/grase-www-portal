<?php

namespace Grase;

    /* Copyright 2008-2014 Timothy White */

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

class Util
{
    // NOTE: This function is based on http://snipplr.com/view/5444/random-pronounceable-passwords-generator/
    public static function randomPassword($len)
    {
        $C = "BCDFGHJKLMNPRSTVWZ";
        $c = "bcdfghjklmnprstvwz";
        $v = "aeiou";
        $V = "AEIOU";

        $password = "";
        $syllables = 3;

        for ($i = 0; $i < ($len / $syllables); $i++) {
            if (!rand(0, 1)) {

                $password .= $c[rand(0, strlen($c) - 1)];
                $password .= $v[rand(0, strlen($v) - 1)];
                $password .= $c[rand(0, strlen($c) - 1)];
                if ($i + 1 < ($len / $syllables)) {
                    $password .= rand(1, 9);
                }
                if ($i + 1 < ($len / $syllables)) {
                    $password .= rand(1, 9);
                }
                if ($i + 1 < ($len / $syllables)) {
                    $password .= rand(1, 9);
                }
            } else {
                $password .= $c[rand(0, strlen($c) - 1)];
                $password .= $v[rand(0, strlen($v) - 1)];
                $password .= $c[rand(0, strlen($c) - 1)];
            }
        }
        if (strlen($password) < $len + 3) {
            $password .= rand(1, 9);
        }
        if (strlen($password) < $len + 3) {
            $password .= rand(1, 9);
        }
        if (strlen($password) < $len + 3) {
            $password .= rand(1, 9);
        }

        return $password;
    }

    /* This function is a modified version of randomPassword function */
    public static function randomUsername($len)
    {
        // It's the calling functions responsability to check the username doesn't already exist
        $c = "bcdfghjklmnprstvwz";
        $v = "aeiou";
        $password = "";
        $syllables = 2; // Short due to username

        for ($i = 0; $i < ($len / $syllables); $i++) {
            if (rand(0, 1)) {
                if ($i + 1 < ($len / $syllables)) {
                    $password .= rand(1, 9);
                }
                $password .= $c[rand(0, strlen($c) - 1)];
                $password .= $v[rand(0, strlen($v) - 1)];
            } else {
                $password .= $v[rand(0, strlen($v) - 1)];
                $password .= $c[rand(0, strlen($c) - 1)];
                if ($i + 1 < ($len / $syllables)) {
                    $password .= rand(1, 9);
                }
            }
        }
        return $password;
    }

    /* NOTE: This function is from Smarty Docs http://www.smarty.net/docs/en/tips.dates.tpl */
    public static function makeTimeStamp($year = '', $month = '', $day = '')
    {
        if (empty($year)) {
            $year = strftime('%Y');
        }
        if (empty($month)) {
            $month = strftime('%m');
        }
        if (empty($day)) {
            $day = strftime('%d');
        }

        return mktime(0, 0, 0, $month, $day, $year);
    }

    // Network Functions
    public static function getNetworkWANIF()
    {
        // Based on default route, get network interface that is the "gateway" (WAN) interface
        $default_wanif = 'eth0';

        $routes = file('/proc/net/route');
        foreach ($routes as $route) {
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
            if (stripos($parms[0], 'tun') !== false) {
                continue;
            }
            if (stripos($parms[0], 'lo') !== false) {
                continue;
            }

            // If destination and mask are 0.0.0.0 then this is a default route
            if ($parms[1] == "00000000" && $parms[7] == "00000000") {
                $default_gateway = $parms[2]; // Future use?
                $default_wanif = trim($parms[0]);
            }
        }
       return $default_wanif;
    }

    public static function getAvailableLANIFS($wanif = '')
    {
        // Show all available network interfaces that we can be using for the LAN interface

        if($wanif == '') $wanif = self::getNetworkWANIF();
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
            if(trim($parms[0]) != $wanif)
                $lanifs[] = trim($parms[0]);
        }

        return $lanifs;
    }

    public static function getDefaultNetworkIFS()
    {
        $default_wanif = self::getNetworkWANIF();
        $lanifs = self::getAvailableLANIFS($default_wanif);
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
}