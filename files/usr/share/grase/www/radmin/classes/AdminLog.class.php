<?php

/* Copyright 2008 Timothy White */

class AnonAuth
{
    public function getUsername()
    {
        return "Anon";
    }
}

class AdminLog
{
    /* This class logs all admin/usermin actions to a database table with timestamp
     * admin/usermin username, ip address, action performed */
     
     /*
             CREATE TABLE adminlog (
	        id INT NOT NULL AUTO_INCREMENT,
	        timestamp DATETIME NOT NULL,
	        username VARCHAR(100) NULL,
	        ipaddress VARCHAR(16) NULL,
	        action TEXT(1000) NOT NULL,
	        PRIMARY KEY id (id)
        ) EMGOME=innoDB COMMENT ='Log of Admin/Usermin Actions';
     */

    private $dbSchemeAdminLog = 
        "CREATE TABLE IF NOT EXISTS  `adminlog` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `timestamp` DATETIME NOT NULL,
            `username` VARCHAR(100) NULL,
            `ipaddress` VARCHAR(16) NULL,
            `action` TEXT(1000) NOT NULL,
            PRIMARY KEY `id` (`id`)
        ) ENGINE=innoDB COMMENT ='Log of Admin/Usermin Actions'";
     
     private $db;

     private $log_sql;
     
     private function __construct($db, $Auth)
     {
        $this->db =& $db;        
        $this->Auth =& $Auth;
        
        $this->ip = $this->ipCheck();
        
        if(! $this->checkTablesExist()) $this->createTables();

	// Share SQL Query between functions
        $this->log_sql = $this->db->prepare('INSERT INTO adminlog
        	(timestamp, username, ipaddress, action)
        	VALUES (?, ?, ?, ?)',
        	array('timestamp', 'text', 'text', 'text'), MDB2_PREPARE_MANIP);
     }
    
    
    /* To prevent multiple instances of the log, but also allowing us to use the log
     * from multiple locations without global vars, we get the AdminLog instance with
     * $AdminLog =& AdminLog::getInstance();
     */
     
    public function &getInstance($db = false, $Auth = false)
    {
        // Static reference of this class's instance.
        static $instance;
        if(!isset($instance))
        {
            if($db == false)
            {
                $db = DatabaseConnections::getInstance()->getRadminDB();
            }
            if($Auth == false)
            {
                $Auth = new AnonAuth();
            }
            $instance = new AdminLog($db, $Auth);
        }
        if(isset($instance) && $Auth != false)
        {
            $instance->Auth =& $Auth;
        }
        return $instance;
    }
    
    public function getLog()
    {
        $sql = "SELECT timestamp, username, ipaddress, action FROM adminlog ORDER BY id DESC";
        
        $res =& $this->db->query($sql);
        
        //print_r($res);
        // Always check that result is not an error
        if (PEAR::isError($res)) {
            ErrorHandling::fatal_error("Getting Admin Log Failed: ". $res->getMessage());
        }
        
        $results = $res->fetchAll(MDB2_FETCHMODE_ASSOC, false, false);
        return $results;
        
    }

     
    public function log($action)
    {
        $affected =& $this->log_sql->execute(array(date('c' /*Y-m-d H:i:s'*/),
                $this->Auth->getUsername(),
                $this->ip,
                $action));

        // Always check that result is not an error
        if (PEAR::isError($affected)) {
            ErrorHandling::fatal_error('Creating Log Entry failed: '. $affected->getMessage());
        }
        
        return $affected;
     
    }
    
    public function log_cron($action)
    {
        $affected =& $this->log_sql->execute(array(date('c' /*Y-m-d H:i:s'*/),
                'CRON',
                $this->ip,
                $action));

        // Always check that result is not an error
        if (PEAR::isError($affected)) {
            ErrorHandling::fatal_error('Creating CRON Log Entry failed: '. $affected->getMessage());
        }
        
        return $affected;
     
    }    
    
    public function log_error($action)
    {
        $affected =& $this->log_sql->execute(array(date('c' /*Y-m-d H:i:s'*/),
                $this->Auth->getUsername(),
                $this->ip,
                "FATAL: ".$action));

        // Always check that result is not an error
        /*if (PEAR::isError($affected)) {
            die('Creating Log Entry failed: '. $affected->getMessage());
        }*/ // Don't check for error here as this is for error handling
        
        return $affected;
     
    }
    
     /* TODO: Check where this code came from */
     private function ipCheck()
     {
        if (getenv('HTTP_CLIENT_IP'))
        {
           $ip = getenv('HTTP_CLIENT_IP');
        }
        elseif (getenv('HTTP_X_FORWARDED_FOR'))
        {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        }
        elseif (getenv('HTTP_X_FORWARDED'))
        {
            $ip = getenv('HTTP_X_FORWARDED');
        }
        elseif (getenv('HTTP_FORWARDED_FOR'))
        {
            $ip = getenv('HTTP_FORWARDED_FOR');
        }
        elseif (getenv('HTTP_FORWARDED'))
        {
            $ip = getenv('HTTP_FORWARDED');
        }
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
	}
	
	private function checkTablesExist()
    {
        if( $this->db->query("SHOW TABLES LIKE 'adminlog'")->numRows())
        {
            return true;
        }
        return false;

    }
    
    private function createTables()
    {
        // Adminlog Table
        $this->db->query($this->dbSchemeAdminLog);
    }
}

?>
