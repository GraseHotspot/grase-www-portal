<?php
/* Copyright 2012 Timothy White */

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

// This Locale stuff doesn't need any DB, so we can call it from anywhere and just apply the locale we want without DB calls!

namespace Grase;

// Probably not needed, but just to be sure for now we'll require the gettext.inc
require_once('php-gettext/gettext.inc');

class Locale
{
    public static $locale = '';

    public static function applyLocale($newlocale)
    {
        self::$locale = $newlocale;
        //TODO Allow locale to be overriden by GET request?
        //if($_GET['lang']) $locale = $_GET['lang'];

        locale_set_default(self::$locale);
        $language = locale_get_display_language(self::$locale, 'en');
        $lang = locale_get_primary_language(self::$locale);
        $region = locale_get_display_region(self::$locale);

        T_setlocale(LC_MESSAGES, $lang);

        T_bindtextdomain("grase", "/usr/share/grase/locale");
        T_bind_textdomain_codeset("grase", "UTF-8");
        T_textdomain("grase");
    }
} 