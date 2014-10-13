<?php
/* Copyright 2014 Timothy White */

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
namespace Grase;


class Validate {

    public static function numericLimit($limit) {
        return $limit && is_numeric($limit);
    }
    //sprintf(T_("Invalid value '%s' for Data Limit"),$limit)

    public static function recurrenceInterval($interval, $recurrenceIntervals)
    {
        return isset($recurrenceIntervals[$interval]);
        //sprintf(T_("Invalid recurrence interval '%s'"), $recurrence);
    }

    public static function bandwidthOptions($kBits, $options) {
        return isset($options[$kBits]);
        //sprintf(T_("Invalid Bandwidth Limit '%s'"), $kbits);
    }

    public static function recurrenceTime($recurrenceInterval, $time) {
        // $time is in minutes not seconds
        $recurrenceTimeValues = array(
            'hour' => 60,
            'day' => 60 * 24,
            'week' => 60 * 24 * 7,
            'month' => 60 * 24 * 30);
        return $recurrenceTimeValues[$recurrenceInterval] >= $time;
        //T_("Recurring time limit must be less than interval");
    }

    public static function MACAddress($MACAddress) {
        // Check string is in format XX-XX-XX-XX-XX-XX (and upper case);
        return preg_match('/([0-9A-F]{2}-){5}[0-9A-F]{2}/', $MACAddress);
        // TODO: Check that each XX pair is a valid hex number
    }

}
