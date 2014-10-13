<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 13/10/14
 * Time: 6:50 AM
 */

namespace Grase;


class Validate {

    public static function dataLimit($limit) {
        return $limit && is_numeric($limit);
    }
    //sprintf(T_("Invalid value '%s' for Data Limit"),$limit)

    public static function recurrenceInterval($interval, $recurrenceIntervals)
    {
        return isset($recurrenceIntervals[$interval]));
        //sprintf(T_("Invalid recurrence interval '%s'"), $recurrence);
    }

    public static function bandwidthOptions($kbits, $options) {
        return isset($options[$kbits]);
        //sprintf(T_("Invalid Bandwidth Limit '%s'"), $kbits);
    }

} 