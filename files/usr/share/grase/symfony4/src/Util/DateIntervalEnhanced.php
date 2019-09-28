<?php

namespace App\Util;

use DateInterval;
use DateTime;

/**
 * Class DateIntervalEnhanced
 * Taken from https://www.php.net/manual/es/dateinterval.format.php
 * This enhanced DateInterval allows us to shove a pure seconds value in, hit recalculate and get the seconds,
 * minutes, hours, days, months...
 *
 * @author  glavic@gmail.com
 */
class DateIntervalEnhanced extends DateInterval
{
    /**
     * Recalculate carry over points
     *
     * @return $this
     */
    public function recalculate()
    {
        $from = new DateTime();
        $to = clone $from;
        $to = $to->add($this);
        $diff = $from->diff($to);
        foreach ($diff as $k => $v) {
            $this->$k = $v;
        }

        return $this;
    }
}
