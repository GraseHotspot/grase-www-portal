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
        //$C = "BCDFGHJKLMNPRSTVWZ";
        $c = "bcdfghjklmnprstvwz";
        $v = "aeiou";
        //$V = "AEIOU";

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
                //$default_gateway = $parms[2]; // Future use?
                $default_wanif = trim($parms[0]);
            }
        }
        return $default_wanif;
    }

    public static function getAvailableLANIFS($wanif = '')
    {
        // Show all available network interfaces that we can be using for the LAN interface

        if ($wanif == '') {
            $wanif = self::getNetworkWANIF();
        }
        $devs = file('/proc/net/dev');
        $lanifs = array();

        // Get rid of junk at start
        array_shift($devs);
        array_shift($devs);

        foreach ($devs as $dev) {
            $parms = explode(":", $dev, 2);
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

    public static function getDefaultNetworkIFS()
    {
        $default_wanif = self::getNetworkWANIF();
        $lanifs = self::getAvailableLANIFS($default_wanif);
        $lanifs_order_pref = array('br0', 'wlan0', 'eth0', 'eth1');
        $lanifs = array_intersect($lanifs_order_pref, $lanifs);
        if (count($lanifs) == 0) {
            // No valid lan interfaces in array, select next best
            if ($default_wanif != 'eth0') {
                $default_lanif = 'eth0';
            } else {
                $default_lanif = 'eth1';
            }
        } else {
            // Valid options in lanifs, select top option
            $default_lanif = array_shift($lanifs);
        }

        return array('lanif' => $default_lanif, 'wanif' => $default_wanif);
    }

    /* TODO: check where this code came from */
    public static function fileUploadErrorCodeToMessage($error_code)
    {
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
            case 2:
                return T_('Uploaded Image was too big');

            case UPLOAD_ERR_PARTIAL:
                return T_('Error In Uploading');

            case UPLOAD_ERR_NO_FILE:
                return T_('No file was uploaded');

            case UPLOAD_ERR_NO_TMP_DIR:
                return T_('Missing a temporary folder');

            case UPLOAD_ERR_CANT_WRITE:
                return T_('Failed to write file to disk');

            case UPLOAD_ERR_EXTENSION:
                return T_('File upload stopped by extension');

            default:
                return T_('Unknown upload error');
        }
    }

    // bigintval taken from http://stackoverflow.com/questions/990406/php-intval-equivalent-for-numbers-2147483647
    public static function bigIntVal($value)
    {
        $value = trim($value);
        if (ctype_digit($value)) {
            return $value;
        }
        $value = preg_replace("/[^0-9](.*)$/", '', $value);
        if (ctype_digit($value)) {
            return $value;
        }
        return 0;
    }

    // Functions from old Formatting class
    public static function formatBytes($bytes = 0)
    {
        $kb = 1024;
        $mb = $kb * 1024;
        $gb = $mb * 1024;

        if (!isset($bytes)) {
            return "";
        } // Unlimited needs to display as blank

        $bytes = $bytes + 0; // Should never be needed now as unlimited ^^

        if ($bytes >= $gb) {
            $output = Locale::localeNumberFormat(
                sprintf("%01.2f", $bytes / $gb)
            ) . " GiB";
        } elseif ($bytes >= $mb) {
            $output = Locale::localeNumberFormat(
                sprintf("%01.2f", $bytes / $mb)
            ) . " MiB";
        } elseif ($bytes >= $kb) {
            $output = Locale::localeNumberFormat(
                sprintf("%01.0f", $bytes / 1024)
            ) . " KiB";
        } elseif ($bytes == 1) {
            $output = Locale::localeNumberFormat($bytes) . " B";
        } else {
            $output = Locale::localeNumberFormat($bytes) . " B";
        }

        return $output;
    }

    public static function formatBits($bits = 0)
    {
        $kb = 1024;
        $mb = $kb * 1024;
        $gb = $mb * 1024;

        if (!isset($bits)) {
            return "";
        } // Unlimited needs to display as blank

        $bits = $bits + 0; // Should never be needed now as unlimited ^^

        if ($bits >= $gb) {
            $output = Locale::localeNumberFormat(
                sprintf("%01.2f", $bits / $gb)
            ) . " Gibit/s";
        } elseif ($bits >= $mb) {
            $output = Locale::localeNumberFormat(
                sprintf("%01.2f", $bits / $mb)
            ) . " Mibit/s";
        } elseif ($bits >= $kb) {
            $output = Locale::localeNumberFormat(
                sprintf("%01.0f", $bits / 1024)
            ) . " Kibit/s";
        } elseif ($bits == 1) {
            $output = Locale::localeNumberFormat($bits) . " bit/s";
        } else {
            $output = Locale::localeNumberFormat($bits) . " bit/s";
        }

        return $output;
    }

    public static function formatSec($seconds = 0)
    {
        $minutes = intval($seconds / 60 % 60);
        $hours = intval($seconds / 3600 % 24);
        $days = intval($seconds / 86400);
        $seconds = intval($seconds % 60);
        if ($days < 1) {
            return sprintf(
                "%02d:%02d:%02d",
                $hours,
                $minutes,
                $seconds
            );
        }
        if ($days == 1) {
            return sprintf(
                "%dd %02d:%02d:%02d",
                $days,
                $hours,
                $minutes,
                $seconds
            );
        }
        return sprintf("%dd %02d:%02d:%02d", $days, $hours, $minutes, $seconds);
    }
}
