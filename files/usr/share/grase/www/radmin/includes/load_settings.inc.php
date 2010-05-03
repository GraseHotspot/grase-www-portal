<?php

/* Application Description/Settings */
require ('constants.inc.php');

$config_file = 'configs/site.conf';

// Test if file exists

if (is_file($config_file)) 
{

    // Parse Config File
    $CONFIG = parse_ini_file($config_file);

}
    // $CONFIG['database_config_file'];
    
    if (!isset($CONFIG['database_config_file'])) $CONFIG['database_config_file'] = '/etc/radmin.conf'; // If no database_config_file is set, set default
    
//    if (!is_file($CONFIG['database_config_file']))
//        ErrorHandling::fatal_nodb_error('database_config_file(' . $CONFIG['database_config_file'] . ') isn\'t a valid file.');

/* Don't care if config file doesn't exist, it just overrides defaults now
}
else
{

    // File not found. Die
    die("Config File Missing. Please copy site.conf.example to $config_file and modify to refelect the site settings.");
}
*/

$DBs =& DatabaseConnections::getInstance($CONFIG['database_config_file']);

require_once ('site_settings.inc.php');
?>
