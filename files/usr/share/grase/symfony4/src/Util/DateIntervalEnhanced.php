<?php

namespace App\Util;

use DateInterval;
use DateTime;

class DateIntervalEnhanced extends DateInterval
{
    public function recalculate()
    {
        $from = new DateTime;
        $to = clone $from;
        $to = $to->add($this);
        $diff = $from->diff($to);
        foreach ($diff as $k => $v) {
            $this->$k = $v;
        }

        return $this;
    }
}
