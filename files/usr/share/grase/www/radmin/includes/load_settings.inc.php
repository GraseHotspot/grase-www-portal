<?php

/* Copyright 2008 Timothy White */

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
