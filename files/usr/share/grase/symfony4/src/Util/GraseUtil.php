<?php

namespace App\Util;

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

/**
 * GraseUtil class from V3. Handy util functions
 */
class GraseUtil
{
    /**
     * Generate a random password that is numeric (pin) of the set length
     *
     * @param $len int length of the resulting pin/password
     *
     * @return string
     */
    public static function randomNumericPassword($len)
    {
        $password = '';
        while (strlen($password) < $len) {
            $password .= rand(0, 9);
        }

        return $password;
    }

    /**
     * Generate random "pronounceable" password at least as long as the given length with all
     * lowercase letters
     *
     * @param $len int Password length minimum
     *
     * @return string
     */
    public static function randomLowercase($len)
    {
        $c = 'bcdfghjklmnprstvwz';
        $v = 'aeiou';
        $r = $c . $v;

        $password = '';
        while (strlen($password) < $len) {
            if (!rand(0, 1)) {
                $password .= $c[rand(0, strlen($c) - 1)];
                $password .= $v[rand(0, strlen($v) - 1)];
                $password .= $c[rand(0, strlen($c) - 1)];
            } else {
                $password .= $r[rand(0, strlen($r) - 1)];
                $password .= $r[rand(0, strlen($r) - 1)];
            }
        }

        return $password;
    }

    /**
     * Generate random "pronounceable" password at least as long as the given length with all
     * lowercase letters and some extra numbers thrown in
     *
     * NOTE: This function is based on http://snipplr.com/view/5444/random-pronounceable-passwords-generator/
     *
     * @see http://snipplr.com/view/5444/random-pronounceable-passwords-generator/
     *
     * @param $len int Password length minimum
     *
     * @return string
     */
    public static function randomPassword($len)
    {
        //$C = "BCDFGHJKLMNPRSTVWZ";
        $c = 'bcdfghjklmnprstvwz';
        $v = 'aeiou';
        //$V = "AEIOU";

        $password = '';
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

    /**
     * Modified version of randomPassword that gives us a nice username
     *
     * @param $len int
     *
     * @return string
     */
    public static function randomUsername($len)
    {
        $c = 'bcdfghjklmnprstvwz';
        $v = 'aeiou';
        $username = '';
        $syllables = 2; // Short due to username

        for ($i = 0; $i < ($len / $syllables); $i++) {
            if (rand(0, 1)) {
                if ($i + 1 < ($len / $syllables)) {
                    $username .= rand(1, 9);
                }
                $username .= $c[rand(0, strlen($c) - 1)];
                $username .= $v[rand(0, strlen($v) - 1)];
            } else {
                $username .= $v[rand(0, strlen($v) - 1)];
                $username .= $c[rand(0, strlen($c) - 1)];
                if ($i + 1 < ($len / $syllables)) {
                    $username .= rand(1, 9);
                }
            }
        }

        return $username;
    }

    /**
     * NETWORK Functions
     */

    /**
     * Based on default route try and work out the WAN network interface
     *
     * @return string
     */
    public static function getNetworkWANIF()
    {
        // Based on default route, get network interface that is the "gateway" (WAN) interface
        $defaultWanIf = 'eth0';

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
            if ($parms[1] == '00000000' && $parms[7] == '00000000') {
                //$default_gateway = $parms[2]; // Future use?
                $defaultWanIf = trim($parms[0]);
            }
        }

        return $defaultWanIf;
    }

    /**
     * Return a list of available LAN nework interfaces
     *
     * @param string $wanif
     *
     * @return array
     */
    public static function getAvailableLANIFS($wanif = '')
    {
        // Show all available network interfaces that we can be using for the LAN interface

        if ('' == $wanif) {
            $wanif = self::getNetworkWANIF();
        }
        $devs = file('/proc/net/dev');
        $lanifs = [];

        // Get rid of junk at start
        array_shift($devs);
        array_shift($devs);

        foreach ($devs as $dev) {
            $parms = explode(':', $dev, 2);
            if (stripos($parms[0], 'tun') !== false) {
                continue;
            }
            if (stripos($parms[0], 'lo') !== false) {
                continue;
            }
            if (trim($parms[0]) != $wanif) {
                $lanifs[] = trim($parms[0]);
            }
        }

        return $lanifs;
    }

    /**
     * Based on available LAN network interfaces, try and guess the default
     *
     * @TODO this function is in need of major updates to be useful in modern distros
     *
     * @return array
     */
    public static function getDefaultNetworkIFS()
    {
        $defaultWanIf = self::getNetworkWANIF();
        $lanifs = self::getAvailableLANIFS($defaultWanIf);
        $lanifsOrderPref = ['br0', 'wlan0', 'eth0', 'eth1'];
        $lanifs = array_intersect($lanifsOrderPref, $lanifs);
        if (count($lanifs) == 0) {
            // No valid lan interfaces in array, select next best
            if ('eth0' !== $defaultWanIf) {
                $defaultLanIf = 'eth0';
            } else {
                $defaultLanIf = 'eth1';
            }
        } else {
            // Valid options in lanifs, select top option
            $defaultLanIf = array_shift($lanifs);
        }

        return ['lanif' => $defaultLanIf, 'wanif' => $defaultWanIf];
    }

    /**
     * A version of intval to take a string number and convert it to an int, for integers bigger than the signed int limit
     *
     * @see http://stackoverflow.com/questions/990406/php-intval-equivalent-for-numbers-2147483647
     *
     * @param $value
     *
     * @return int|string|string[]|null
     */
    public static function bigIntVal($value)
    {
        $value = trim($value);
        if (ctype_digit($value)) {
            return $value;
        }
        $value = preg_replace('/[^0-9](.*)$/', '', $value);
        if (ctype_digit($value)) {
            return $value;
        }

        return 0;
    }

    /**
     * Run chilli_query to get a list of current DHCP leases
     *
     * @return bool|mixed
     */
    public static function getChilliLeases()
    {
        exec('sudo /usr/sbin/chilli_query -json list', $output, $return);

        if (0 === $return) {
            // Command worked
            return json_decode($output[0], true);
        }

        return false;
    }

    /**
     * Use chilli_query to force logout a session
     *
     * @param $mac
     *
     * @return bool
     */
    public static function logoutChilliSession($mac)
    {
        // Logout a specific MAC address
        $leases = GraseUtil::getChilliLeases();
        foreach ($leases['sessions'] as $session) {
            if ($session['macAddress'] == $mac && strlen($session['macAddress']) == 17) {
                exec('sudo /usr/sbin/chilli_query logout ' . $session['macAddress'], $output, $return);
                if (0 === $return) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Take a subnet mask (CIDR or as Mask) and ensure we return a mask if it's valid
     *
     * @param $mask
     *
     * @return int|string
     */
    public static function transformSubnetMask($mask)
    {
        if (strlen($mask) < 8 && is_numeric($mask) && $mask < 30 && $mask > 8) {
            // We have an int CIDR hopefully
            $mask = GraseUtil::CIDRtoMask($mask);
        }

        return $mask;
    }

    /**
     * Take a network CIDR and convert to a string netmask
     *
     * This function probably came out of PHP Docs or Stackoverflow
     *
     * @param $int
     *
     * @return string
     */
    public static function CIDRtoMask($int)
    {
        assert($int <= 32, 'CIDR Must be between 0 and 32');
        assert($int > 0, 'CIDR Must be between 0 and 32');

        return long2ip(-1 << (32 - (int) $int));
    }

    /**
     * Take a string mask and turn it into a CIDR
     *
     * https://www.php.net/manual/en/function.ip2long.php
     *
     * @param $mask string
     *
     * @return int|float If this returns a float, you didn't give us a proper mask
     */
    public static function maskToCIDR($mask)
    {
        $long = ip2long($mask);
        $base = ip2long('255.255.255.255');

        return 32 - log(($long ^ $base) + 1, 2);

        /* xor-ing will give you the inverse mask,
            log base 2 of that +1 will return the number
            of bits that are off in the mask and subtracting
            from 32 gets you the cidr notation */
    }
}
