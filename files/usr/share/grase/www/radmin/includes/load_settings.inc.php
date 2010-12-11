<?php

/* Copyright 2008 Timothy White */

/*  This file is part of GRASE Hotspot.

    http://hotspot.purewhite.id.au/

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

/* Application Description/Settings */
require ('constants.inc.php');

$config_file = 'configs/site.conf';

// Test if file exists

if (is_file($config_file)) 
{

    // Parse Config File
    $CONFIG = parse_ini_file($config_file);

    /* Don't care if config file doesn't exist, it just overrides defaults now */
}

     // If no *_database_config_file is set, set defaults
    if (!isset($CONFIG['radius_database_config_file'])) $CONFIG['radius_database_config_file'] = '/etc/grase/radius.conf';
    if (!isset($CONFIG['radmin_database_config_file'])) $CONFIG['radmin_database_config_file'] = '/etc/grase/radmin.conf';


    // Check for if DB config file is valid is left upto DatabaseConnections class now    
//    if (!is_file($CONFIG['database_config_file']))
//        ErrorHandling::fatal_nodb_error('database_config_file(' . $CONFIG['database_config_file'] . ') isn\'t a valid file.');


$DBs =& DatabaseConnections::getInstance($CONFIG['radius_database_config_file'], $CONFIG['radmin_database_config_file']);

require_once ('site_settings.inc.php');
?>
