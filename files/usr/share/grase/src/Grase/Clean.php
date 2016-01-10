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

    private static function unsafeToUnderscores($text)
    {
        // This function is used to cleanup things like ids, so replace all chars that shouldn't be in id's and such
        return str_replace(array(' ', '$', '(', ')'), '_', $text);
    }

    public static function groupName($text)
    {
        // Get the group name in a suitable format
        return self::unsafeToUnderscores(self::text($text));
    }
}
