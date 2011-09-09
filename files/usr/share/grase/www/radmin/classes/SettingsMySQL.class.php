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

class SettingsMySQL extends Settings
{

    private $databaseSettingsFile;
    private $databaseSettings;
    private $db;
    
    private $dbSchemeVersion = "2.0";
    private $dbSchemeSettings = 
        "CREATE TABLE IF NOT EXISTS `settings` (
          `setting` varchar(20) NOT NULL,
          `value` varchar(1000) NOT NULL,
          PRIMARY KEY (`setting`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Settings for RAFI interface'";
    private $dbSchemeAuth =
        "CREATE TABLE IF NOT EXISTS `auth` (
          `username` varchar(50) NOT NULL DEFAULT '',
          `password` varchar(60) NOT NULL,
          PRIMARY KEY (`username`),
          KEY `password` (`password`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1";
    private $dbSchemaTemplates = 
        "CREATE TABLE IF NOT EXISTS `templates` (
          `id` tinyint(4) NOT NULL,
          `tpl` text NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='HTML/CSS Storage'";

    public function __construct($db)
    {
        if($db)
        {
            $this->db =& $db;
        }
        /* * Old code to handle not depending on DatabaseConnections class
        else
        {
            //$this->databaseSettings['sql_radmindatabase'] = 'radmin';
            $this->databaseSettingsFile = $databaseSettingsFile;
            $this->connectDatabase();
         
        }*/
        if(! $this->checkTablesExist()) $this->createTables();
        //if($this->getSetting('DBVersion') == "" || $this->getSetting('DBVersion') < $this->dbSchemeVersion) $this->upgradeTables();
    }
        
#    private function connectDatabase()
#    {
#    
#        // Connecting, selecting database
#        $settings = file($this->databaseSettingsFile);

#        foreach($settings as $setting) 
#        {
#            list($key, $value) = split(":", $setting);
#            $this->databaseSettings[$key] = trim($value);
#        }
#        
#        $this->db = mysql_pconnect($this->databaseSettings['sql_server'], $this->databaseSettings['sql_username'], $this->databaseSettings['sql_password']) or ErrorHandling::fatal_error('Could not connect: ' . mysql_error());
#        mysql_select_db($this->databaseSettings['sql_radmindatabase'], $this->db) or ErrorHandling::fatal_error('Could not select database');
#    }
    
    private function checkTablesExist()
    {
        if( $this->db->query("SHOW TABLES LIKE 'settings'")->numRows() &&
            $this->db->query("SHOW TABLES LIKE 'auth'")->numRows() &&
            $this->db->query("SHOW TABLES LIKE 'templates'")->numRows())             
        {
            return true;
        }
        return false;

    }
    
    private function createTables()
    {
        // Settings Table
        $this->db->query($this->dbSchemeSettings);   
        // Auth Table
        $this->db->query($this->dbSchemeAuth);
        // Templates table
        $this->db->query($this->dbSchemaTemplates);
        $this->setSetting('DBSchemaVersion', $this->dbSchemeVersion);
    }
    
    public function getSetting($setting)
    {
        $sql = sprintf("SELECT value FROM settings WHERE setting = '%s'",
                        mysql_real_escape_string($setting));
        return $this->db->queryOne($sql);
       
    }
    
    // ^^ Returning the value fails on empty strings
    public function checkExistsSetting($setting)
    {
        $sql = sprintf("SELECT COUNT(setting) FROM settings WHERE setting = '%s'  LIMIT 1",
                        mysql_real_escape_string($setting));
        return $this->db->queryOne($sql);
       
    }    
    
    public function setSetting($setting, $value)
    {
        // Check count not contents ^^
        if($this->checkExistsSetting($setting) == 0)
        {
            // Insert new record
            $sql = sprintf("INSERT INTO settings SET
                            setting='%s',
                            value='%s'",
                            mysql_real_escape_string($setting),
                            mysql_real_escape_string($value));
        }else
        {
            // Update old record
            $sql = sprintf("UPDATE settings SET
                            value='%s'
                            WHERE setting='%s'",
                            mysql_real_escape_string($value),
                            mysql_real_escape_string($setting));
            
        }
        
        $affected =& $this->db->exec($sql);

        // Always check that result is not an error
        if (PEAR::isError($affected)) {
	        AdminLog::getInstance()->log("Setting $setting failed to update (to $value)");        
            ErrorHandling::fatal_error('Updating setting failed: '. $affected->getMessage());
        }
        if($setting != 'lastbatch') // lastbatch clogs admin log, filter it out
            AdminLog::getInstance()->log("Setting $setting updated to $value");
        return true;
        

    }
    
    
/* "Settings" for templates */

    // Template map is to make it easier to lookup templates via int not txt
    private $templatemap = array(
        'maincss'   => 0,
        'loginhelptext' => 1,
        'helptext' => 2,
        'belowloginhtml' => 3,
        'loggedinnojshtml' => 4,
    );

    public function getTemplate($template)
    {
        $sql = sprintf("SELECT tpl FROM templates WHERE id = '%s' LIMIT 1",
                        mysql_real_escape_string($this->templatemap[$template]));
        return $this->db->queryOne($sql);
       
    }
    
    // ^^ Returning the value fails on empty strings
    public function checkExistsTemplate($template)
    {
        $sql = sprintf("SELECT COUNT(id) FROM templates WHERE id = '%s' LIMIT 1",
                        mysql_real_escape_string($this->templatemap[$template]));
        return $this->db->queryOne($sql);
       
    }    
    
    public function setTemplate($template, $value)
    {
        // Check count not contents ^^
        if($this->checkExistsTemplate($template) == 0)
        {
            // Insert new record
            $sql = sprintf("INSERT INTO templates SET
                            id='%s',
                            tpl='%s'",
                            mysql_real_escape_string($this->templatemap[$template]),
                            mysql_real_escape_string($value));
        }else
        {
            // Update old record
            $sql = sprintf("UPDATE templates SET
                            tpl='%s'
                            WHERE id='%s'",
                            mysql_real_escape_string($value),
                            mysql_real_escape_string($this->templatemap[$template]));
            
        }
        
        $affected =& $this->db->exec($sql);

        // Always check that result is not an error
        if (PEAR::isError($affected)) {
	        AdminLog::getInstance()->log("Template $template failed to update");        
            ErrorHandling::fatal_error('Updating template failed: '. $affected->getMessage());
        }
        //if($template != 'lastbatch') // lastbatch clogs admin log, filter it out
            AdminLog::getInstance()->log("Template $template updated");
        return true;
        

    }
/* End templates functions */    
    
    public function upgradeFromFiles()
    {
        AdminLog::getInstance()->log("Performing upgradeFromFiles on Settings");
        /* */
        $location_file = '/var/www/radmin/configs/site_settings/location'; // Hotspot location
        $pricemb_file = '/var/www/radmin/configs/site_settings/pricemb'; // Price per MB
        $pricetime_file = '/var/www/radmin/configs/site_settings/pricetime'; // Price permin
        //$currency_file = '/var/www/radmin/configs/site_settings/currency'; // Currency, e.g. R or $
        $sellable_data_file = '/var/www/radmin/configs/site_settings/sellable_data'; // Sellable Data in Octets's
        $useable_data_file = '/var/www/radmin/configs/site_settings/useable_data'; // Useable Data in Octets's
        $support_contact_file = '/var/www/radmin/configs/site_settings/support_contact'; // Support Contact
        $website_file = '/var/www/radmin/configs/site_settings/website'; // Support Contact

        $old_error_level = error_reporting(1);
        /* */

        $location = trim(file_get_contents($location_file)); if($location == "") $location = "Default";
        $pricemb = trim(file_get_contents($pricemb_file)); if($pricemb == "") $pricemb = 0.6;
        $pricetime = trim(file_get_contents($pricetime_file)); if($pricetime == "") $pricetime = 0.1;
        //$currency = trim(file_get_contents($currency_file)); if($currency == "") $currency = "R";
        $sellable_data = trim(file_get_contents($sellable_data_file)); if($sellable_data == "") $sellable_data = "2147483648"; //2Gb
        $useable_data = trim(file_get_contents($useable_data_file)); if($useable_data == "") $useable_data = "3221225472"; //3Gb
        $support_contact = trim(file_get_contents($support_contact_file)); if($support_contact == "") $support_contact = "http://purewhite.id.au/ Tim White";
        list($support_link, $support_name) = explode(' ', $support_contact, 2);

        $website = trim(file_get_contents($website_file)); if($website == "") $website = "http://ywam.org/ YWAM";
        list($website_link, $website_name) = explode(' ', $website, 2);
        error_reporting($old_error_level);        

        $this->setSetting('locationName', $location);                
        $this->setSetting('priceMb', $pricemb);                        
        $this->setSetting('priceMinute', $pricetime);
        //$this->setSetting('currency', $currency);
        $this->setSetting('sellableData', $sellable_data);
        $this->setSetting('useableData', $useable_data);
        $this->setSetting('supportContactName', $support_name);
        $this->setSetting('supportContactLink', $support_link);

        $this->setSetting('websiteLink', $website_link);
        $this->setSetting('websiteName', $website_name);

    }

}
?>
