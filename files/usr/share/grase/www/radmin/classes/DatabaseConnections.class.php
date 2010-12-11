<?php

/* Copyright 2009 Timothy White */

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

require_once "MDB2.php";

class DatabaseConnections
{
    private $radiusDatabaseSettingsFile;
    private $radminDatabaseSettingsFile;
    private $radminDatabaseSettings;
    private $radiusDatabaseSettings;            
    private $radminDB;
    private $radiusDB;
    private $radminDSN;
    private $radiusDSN;
    private $radminOptions;
    private $radiusOptions;
    
    /* To prevent multiple instances of the DB, but also allowing us to use the DB
     * from multiple locations without global vars, we get the instance with
     * $DBs =& DatabaseConnections::getInstance();
     * Initial call is
     * $DBs =& DatabaseConnections::getInstance($CONFIG['database_config_file']);
     */
     
    public function &getInstance($radiusDatabaseSettingsFile = '/etc/grase/radius.conf', $radminDatabaseSettingsFile = '/etc/grase/radmin.conf')
    {
        // Static reference of this class's instance.
        static $instance;
        if(!isset($instance)) {
            $instance = new DatabaseConnections($radiusDatabaseSettingsFile, $radminDatabaseSettingsFile);
        }
        return $instance;
    }    
    
    private function __construct($radiusDatabaseSettingsFile = '/etc/grase/radius.conf', $radminDatabaseSettingsFile = '/etc/grase/radmin.conf')
    {
        //$this->databaseSettings['sql_radmindatabase'] = 'radmin';
        $this->radiusDatabaseSettingsFile = $radiusDatabaseSettingsFile;
        $this->radminDatabaseSettingsFile = $radminDatabaseSettingsFile;
        $this->connectDatabase();
    }

    private function loadSettingsFromFile($dbSettingsFile)
    {

        // Check that databaseSettingsFile is valid
        if (!is_file($dbSettingsFile))
        {
            ErrorHandling::fatal_nodb_error(
		_("DB Config File isn't a valid file.") . "($dbSettingsFile)"
	    );
        }
    
        $settings = file($dbSettingsFile);

        foreach($settings as $setting) 
        {
            list($key, $value) = explode(":", $setting);
            $db_settings[$key] = trim($value);
//            $this->databaseSettings[$key] = trim($value);
        }
//        $db_settings = $this->databaseSettings;        
        return $db_settings;
    }
        
    private function connectDatabase()
    {
    
#        // Check that databaseSettingsFile is valid
#        if (!is_file($this->databaseSettingsFile))
#        {
#            ErrorHandling::fatal_nodb_error('DB Config File(' . $this->databaseSettingsFile . ') isn\'t a valid file.');
#        }
#    
#        // Connecting, selecting database
#        $settings = file($this->databaseSettingsFile);

#        foreach($settings as $setting) 
#        {
#            list($key, $value) = split(":", $setting);
#            $this->databaseSettings[$key] = trim($value);
#        }
#        $db_settings = $this->databaseSettings;

        $this->radminDatabaseSettings = $this->loadSettingsFromFile($this->radminDatabaseSettingsFile);
        $this->radiusDatabaseSettings = $this->loadSettingsFromFile($this->radiusDatabaseSettingsFile);
        
        // Set options and DSN
        $db_settings = $this->radiusDatabaseSettings;
        $this->radiusDSN = array(
            "phptype" => "mysql",
            "username" => $db_settings['sql_username'],
            "password" => $db_settings['sql_password'],
            "hostspec" => $db_settings['sql_server'],
            "database" => $db_settings['sql_database'],
            "new_link" => true
            );
            
        $this->radiusOptions = array(
            'portability' => MDB2_PORTABILITY_ALL ^ MDB2_PORTABILITY_FIX_CASE,
            );            

        $db_settings = $this->radminDatabaseSettings;
        $this->radminDSN = array(
            "phptype" => "mysql",
            "username" => $db_settings['sql_username'],
            "password" => $db_settings['sql_password'],
            "hostspec" => $db_settings['sql_server'],
            "database" => $db_settings['sql_radmindatabase'],
            'portability' => MDB2_PORTABILITY_ALL ^ MDB2_PORTABILITY_FIX_CASE,            
            "new_link" => true
            );
            
        $this->radminOptions = array(
            'portability' => MDB2_PORTABILITY_ALL ^ MDB2_PORTABILITY_FIX_CASE,
            );            
            

        // Connect

        $this->radiusDB =& MDB2::connect($this->radiusDSN, $this->radiusOptions);
        if (PEAR::isError($this->radiusDB))
        {
            ErrorHandling::fatal_nodb_error($this->radiusDB->getMessage() . " RADIUS<br/>The RADIUS database does not exist");
        }
        
        // Set mode for Radius DB
        $this->radiusDB->setFetchMode(MDB2_FETCHMODE_ASSOC);

        
        $this->radminDB =& MDB2::connect($this->radminDSN);
        if (PEAR::isError($this->radminDB))
        {
            // Attempt to create the radminDB? TODO: Make nicer?
            $this->radiusDB->loadModule('Manager');
            $this->radiusDB->createDatabase($db_settings['sql_radmindatabase']);
            
            $this->radminDB =& MDB2::connect($this->radminDSN);
            if (PEAR::isError($this->radminDB))
            {
                ErrorHandling::fatal_nodb_error($this->radminDB->getMessage()." RADMIN");
            }
        }
        
        // Set mode for Radmin DB
        $this->radminDB->setFetchMode(MDB2_FETCHMODE_ASSOC);
        
        
        //print $this->radiusDB;
    }
    
    public function getRadminDB()
    {
        return $this->radminDB;
    }
    
    public function getRadiusDB()
    {
        return $this->radiusDB;
    }
    
    public function getRadminDSN()
    {
        return $this->radminDSN;
    }
    
    public function getRadiusDSN()
    {
        return $this->radiusDSN;
    }    
    
}
?>
