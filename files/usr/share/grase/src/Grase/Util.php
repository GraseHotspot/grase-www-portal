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
    public static function RandomPassword($len)
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

    /* This function is a modified version of RandomPassword function */
    public static function RandomUsername($len)
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
}