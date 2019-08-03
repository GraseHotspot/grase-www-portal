<?php

/* Copyright 2008 Timothy White */

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

	// TODO: Make timestamp auto update in sql
	// Share SQL Query between functions
        $this->log_sql = $this->db->prepare('INSERT INTO adminlog
        	(`timestamp`, username, ipaddress, action)
        	VALUES (?, ?, ?, ?)',
        	array('timestamp', 'text', 'text', 'text'), MDB2_PREPARE_MANIP);
        
        //var_dump($this->db);
        //var_dump($this->log_sql);	
    	if(MDB2::isError($this->log_sql))
    	    \Grase\ErrorHandling::fatalNoDatabaseError("Preparing logging statement failed: ". $this->log_sql->getMessage());
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
                $Auth = new \Grase\AnonAuth();
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
        $sql = "SELECT `timestamp`, username, ipaddress, action FROM adminlog WHERE NOT username = 'CRON' ORDER BY id DESC";
        
        $res =& $this->db->query($sql);
        
        //print_r($res);
        // Always check that result is not an error
        if (MDB2::isError($res)) {
            \Grase\ErrorHandling::fatalError("Getting Admin Log Failed: ". $res->getMessage());
        }
        
        $results = $res->fetchAll(MDB2_FETCHMODE_ASSOC, false, false);
        return $results;
        
    }
    
    public function lastCron()
    {
        $sql = "SELECT `timestamp` FROM adminlog WHERE username = 'CRON' ORDER BY id DESC LIMIT 1";
        
        $res =& $this->db->query($sql);
        
        //print_r($res);
        // Always check that result is not an error
        if (MDB2::isError($res)) {
            \Grase\ErrorHandling::fatalError("Getting Admin Log Failed: ". $res->getMessage());
        }
        
        $result = $res->fetchOne();
        return $result;    
    }

     
    public function log($action)
    {
	// TODO: Make timestamp auto update in sql
        $affected =& $this->log_sql->execute(array(date('Y-m-d H:i:s'),
                $this->Auth->getUsername(),
                $this->ip,
                $action));

        // Always check that result is not an error
        if (MDB2::isError($affected)) {
            \Grase\ErrorHandling::fatalError('Creating Log Entry failed: '. $affected->getMessage());
        }
        
        return $affected;
     
    }
    
    public function log_cron($action)
    {
        $affected =& $this->log_sql->execute(array(date('Y-m-d H:i:s'),
                'CRON',
                $this->ip,
                $action));

        // Always check that result is not an error
        if (MDB2::isError($affected)) {
            \Grase\ErrorHandling::fatalError('Creating CRON Log Entry failed: '. $affected->getMessage());
        }
        
        return $affected;
     
    }    
    
    public function log_error($action)
    {
        $affected =& $this->log_sql->execute(array(date('Y-m-d H:i:s'),
                $this->Auth->getUsername(),
                $this->ip,
                "FATAL: ".$action));

        // Always check that result is not an error
        /*if (MDB2::isError($affected)) {
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
