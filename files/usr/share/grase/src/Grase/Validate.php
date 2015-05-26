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

class Validate
{

    public static function numericLimit($limit)
    {
        // Should we be checking for null and false here, or force the callers to check?
        return $limit === null || $limit === false || is_numeric($limit);
    }

    // Validation functions
    public static function validateNumber($number)
    {
        if ($number && is_numeric($number) && trim($number) != "") {
            return true;
        }
        if ($number + 0 === 0) {
            return true;
        }
        return false;
    }

    public static function validateInt($number, $optional = false)
    {
        if ($number && is_numeric($number) && is_int($number) && trim($number) != "") {
            return true;
        }
        if ($optional && is_int($number) && trim($number) == "") {
            return true;
        }
        return false;
    }

    public static function validateUUCPTimerange($timeRanges)
    {
        // We can have multiple time ranges, so split on comma (and |)
        if (trim($timeRanges)) {
            $timeRange = str_replace('|', ',', $timeRanges);

            $timeRange = explode(',', $timeRange);

            // For each range, check we start with valid start, followed by range
            foreach ($timeRange as $range) {
                $result = preg_match('/^(Su|Mo|Tu|We|Th|Fr|Sa|Sun|Mon|Tue|Wed|Thur|Fri|Sat|Wk|Any|Al|Never)(\d{4}-\d{4})?$/', $range);
                if ($result == 0) {
                    return false;
                }
            }
        }
        return true;
    }


    //sprintf(T_("Invalid value '%s' for Data Limit"),$limit)

    public static function recurrenceInterval($interval, $recurrenceIntervals)
    {
        return isset($recurrenceIntervals[$interval]);
        //sprintf(T_("Invalid recurrence interval '%s'"), $recurrence);
    }

    public static function bandwidthOptions($kBits, $options)
    {
        return isset($options[$kBits]);
        //sprintf(T_("Invalid Bandwidth Limit '%s'"), $kbits);
    }

    public static function recurrenceTime($recurrenceInterval, $time)
    {
        // $time is in minutes not seconds
        $recurrenceTimeValues = array(
            'hour' => 60,
            'day' => 60 * 24,
            'week' => 60 * 24 * 7,
            'month' => 60 * 24 * 30
        );
        return $recurrenceTimeValues[$recurrenceInterval] >= $time;
        //T_("Recurring time limit must be less than interval");
    }

    public static function MACAddress($MACAddress)
    {
        // Check string is in format XX-XX-XX-XX-XX-XX (and upper case);
        return preg_match('/([0-9A-F]{2}-){5}[0-9A-F]{2}/', $MACAddress);
        // TODO: Check that each XX pair is a valid hex number
    }
}
