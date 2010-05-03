<?php

class DatabaseConnections
{
    private $databaseSettingsFile;
    private $databaseSettings;    
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
     
    public function &getInstance($databaseSettingsFile = '/etc/radmin.conf')
    {
        // Static reference of this class's instance.
        static $instance;
        if(!isset($instance)) {
            $instance = new DatabaseConnections($databaseSettingsFile);
        }
        return $instance;
    }    
    
    private function __construct($databaseSettingsFile = '/etc/radmin.conf')
    {
        $this->databaseSettings['sql_radmindatabase'] = 'radmin';
        $this->databaseSettingsFile = $databaseSettingsFile;
        $this->connectDatabase();
    }
        
    private function connectDatabase()
    {
    
        // Check that databaseSettingsFile is valid
        if (!is_file($this->databaseSettingsFile))
        {
            ErrorHandling::fatal_nodb_error('DB Config File(' . $this->databaseSettingsFile . ') isn\'t a valid file.');
        }
    
        // Connecting, selecting database
        $settings = file($this->databaseSettingsFile);

        foreach($settings as $setting) 
        {
            list($key, $value) = split(":", $setting);
            $this->databaseSettings[$key] = trim($value);
        }
        $db_settings = $this->databaseSettings;
        
        // Set options and DSN
        
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
            // Attempt to create the radminDB? TODO Make nicer?
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
