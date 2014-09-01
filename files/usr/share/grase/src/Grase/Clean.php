<?php

namespace Grase;

/* Functions for "cleaning" different kinds of inputs */
class Clean
{

    public static function text($text)
    {
        $text = strip_tags($text);
        $text = str_replace("<", "", $text);
        $text = str_replace(">", "", $text);
        return trim($text);
    }

    public static function username($text)
    {
        // Usernames should be stricter than other strings, ' and " just cause problems
        $text = self::text($text);
        $text = str_replace("'", "", $text);
        $text = str_replace('"', "", $text);
        // Maybe should also strip spaces?
        return $text;
    }
}
