<?php

/* Functions for "cleaning" different kinds of inputs */

namespace Grase;

class Clean {

    public static function text($text)
    {
        $text = strip_tags($text);
        $text = str_replace("<", "", $text);
        $text = str_replace(">", "", $text);
        return trim($text);
    }


} 