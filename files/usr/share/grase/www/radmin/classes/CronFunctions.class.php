<?php

/* Copyright 2010 Timothy White */

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

/* CronFunctions is mostly an upgrade and maintenace class, makes it easy to 
 * upgrade database features and do regular cleaning. Cron is called after a 
 * package update as well allowing for upgrades at each install
 */

class CronFunctions extends DatabaseFunctions
{
    /* Inherited from DatabaseFunctions
     * 
     * $db is Radius DB handle
     */
     
    public function &getInstance()
    {
        // Static reference of this class's instance.
        static $instance;
        if(!isset($instance)) {
            $instance = new CronFunctions();
        }
        return $instance;
    }    
/*    
    private function __construct()
    {
        //parent::__construct();
        $this->db =& DatabaseConnections::getInstance()->getRadiusDB();
    }   */ 
    
    public function upgradeDB()
    {
        /* Upgrade different aspects of the Database as the scheme changes
         * */
        $Settings = new SettingsMySQL(DatabaseConnections::getInstance()->getRadminDB());
        $olddbversion = $Settings->getSetting("DBVersion");
        
        $results = 0;
         
        if($olddbversion < 1.1)
        {
            $sql = "UPDATE radcheck
                    SET Attribute='Cleartext-Password'
                    WHERE Attribute='Password'";
            
            $result = $this->db->exec($sql);
            
            if (PEAR::isError($result))
            {
                return T_('Upgrading DB failed: ') . $result->toString();
            }
            
            $results += $result;
            $Settings->setSetting("DBVersion", 1.1);
            
        }
        
        if($olddbversion < 1.2)
        {
            // remove unique key from radreply
            $sql = "DROP INDEX userattribute ON radreply";
            
            $result = $this->db->exec($sql);

            // Don't die on error for this as there is no "IF EXISTS"            
            /*if (PEAR::isError($result))
            {
                return T_('Upgrading DB failed: ') . $result->toString();
            }*/
            if (!PEAR::isError($result))
                $results += $result;            
        
            // Add Radius Config user for Coova Chilli Radconfig
            $results += $this->setUserPassword(RADIUS_CONFIG_USER, RADIUS_CONFIG_PASSWORD);
            
            // Set Radius Config user Service-Type to filter it out of normal users
            $result = $this->replace_radcheck_query(
                RADIUS_CONFIG_USER, 
                'Service-Type', 
                ':=',
                'Administrative-User');
            
            if (PEAR::isError($result))
            {
                return T_('Upgrading DB failed: ') . $result->toString();
            }
            
            $results += $result;
            
            // Add default macpasswd string
            $results += $this->setChilliConfigSingle('macpasswd', 'password');
            // Add default defidelsession 
            $results += $this->setChilliConfigSingle('defidletimeout', '600');
            
            // Set last change time
            $Settings->setSetting('lastchangechilliconf', time());
            
            //Install default groups
	            $dgroup["Staff"] = "+6 months";
	            $dgroup["Ministry"] = "+6 months";
	            $dgroup["Students"] = "+3 months";
	            $dgroup["Visitors"] = "+1 months";
            $Settings->setSetting("groups", serialize($dgroup));
            
            // Upgrade DB version to next version
            $Settings->setSetting("DBVersion", 1.2);
            
        }
        
        if($olddbversion < 1.3)
        {
            // Set default groups to not allow simultaneous use
            $results += $this->setGroupSimultaneousUse("Staff", "no");
            $results += $this->setGroupSimultaneousUse("Ministry", "no");
            $results += $this->setGroupSimultaneousUse("Students", "no");
            $results += $this->setGroupSimultaneousUse("Visitors", "no");
            
            // Upgrade DB version to next version
            $Settings->setSetting("DBVersion", 1.3);
            
        }
        
        if($olddbversion < 1.4)
        {

            if(include_once('/usr/share/grase/www/radmin/includes/default_templates.php'))
            {
                $results ++;
        
                // Upgrade DB version to next version
                $Settings->setSetting("DBVersion", 1.4);        
            }else{
                return T_('Upgrading DB failed: ') . T_('Including default_templates file failed');
            }
        
        }
        
        if($olddbversion < 1.5)
        {
            // Load default network settings (match old chilli config)
            $net['lanipaddress'] = '10.1.0.1';
            $net['networkmask'] = '255.255.255.0';
            $net['opendnsbogusnxdomain'] = true;
            $net['dnsservers'] = array('208.67.222.123','208.67.220.123'); // OpenDNS Family Shield
            $net['bogusnx'] = array();
            
            $Settings->setSetting('networkoptions', serialize($net));      
            $Settings->setSetting('lastnetworkconf', time());  
            $results ++;
        
            // Upgrade DB version to next version
            $Settings->setSetting("DBVersion", 1.5);                
        }
        
        if($olddbversion < 1.6)
        {
            // Move groupAttributes to the correct table
            foreach(DatabaseFunctions::getInstance()->getGroupAttributes() as $name => $group)
            {
         
                DatabaseFunctions::getInstance()->setGroupAttributes($name, $group);
                $results ++;
            }
            
            // Upgrade DB version to next version
            $Settings->setSetting("DBVersion", 1.6);              
            
        }
        if($olddbversion < 1.7)
        {
            $sql1 = "ALTER TABLE auth
                    DROP COLUMN accesslevel";
            $sql2 = "ALTER TABLE auth
                    ADD COLUMN accesslevel INT NOT NULL DEFAULT 1";
            
            $result = $this->radminDB->exec($sql1);
            
            // Don't care if it's an error
            /*if (PEAR::isError($result))
            {
                return T_('Upgrading DB failed: ') . $result->toString();
            }*/
            
            $results += $result;
            $result = $this->radminDB->exec($sql2);
            
            if (PEAR::isError($result))
            {
                return T_('Upgrading DB failed: ') . $result->toString();
            }
            
            $results += $result;            
            $Settings->setSetting("DBVersion", 1.7);
        }
        
        if($olddbversion < 1.8)
        {
            // Default network interface settings need to be set
            require_once('/usr/share/grase/www/radmin/includes/network_functions.inc.php');
            $interfaces = default_net_ifs();
            $networkoptions = unserialize($Settings->getSetting('networkoptions'));
            $networkoptions['lanif'] = $interfaces['lanif'];
            $networkoptions['wanif'] = $interfaces['wanif'];

            $Settings->setSetting('networkoptions', serialize($networkoptions));
            
            $Settings->setSetting('lastnetworkconf', time());
            $results += 2;
            
            // New chilli settings for garden
            $results += $this->setChilliConfigSingle('nousergardendata', '');

            
            $Settings->setSetting("DBVersion", 1.8);
        }
        
        if($olddbversion < 1.9)
        {
            // Get last batch and migrate it to new batch system
            $lastbatch = $Settings->getSetting('lastbatch');
            // Check if lastbatch is an array, if so then we migrate
            if(is_array(unserialize($lastbatch)))
            {
                $lastbatchusers = unserialize($lastbatch);
                $nextBatchID = $Settings->nextBatchID();
                $results += $Settings->saveBatch($nextBatchID, $lastbatchusers);
                // Lastbatch becomes an ID
                $Settings->setSetting('lastbatch', $nextBatchID);
            }else{
                $Settings->setSetting('lastbatch', 0);
            }
            
            $Settings->setSetting("DBVersion", 1.9);

        }        
        
        if($olddbversion < 2.0)
        {
            // Migrate groups to new system
            $groups = unserialize($Settings->getSetting('groups'));

            $groupattributes = $this->getGroupAttributes();
            
            foreach($groups as $group => $expiry)
            {
                $attributes = array();
                $attributes['GroupName'] = clean_groupname($group);
                $attributes['GroupLabel'] = $group;
                $attributes['Expiry'] = @ $expiry;
                $attributes['MaxOctets'] = @ $groupattributes[$group]['MaxOctets'];
                $attributes['MaxSeconds'] = @ $groupattributes[$group]['MaxSeconds'];
                // No comment stored, but oh well
                $attributes['Comment'] = @ $groupattributes[$group]['Comment'];
                
                $results += $Settings->setGroup($attributes);
            }
            
            $Settings->setSetting('groups',serialize(''));
            
            $Settings->setSetting("DBVersion", 2.0);
        }
        
        // TODO: cron to clean old batches
        
        if($olddbversion < 2.1)
        {
            // Remove uniq index on radgroupcheck
            $sql = "DROP INDEX GroupName ON radgroupcheck";
            
            $result = $this->db->exec($sql);

            if (!PEAR::isError($result))
                $results += $result;      
                
            $sql = "ALTER TABLE radgroupcheck
                    ADD KEY `GroupName` (`GroupName`(32))";
                    
            $result = $this->db->exec($sql);
            
            if (PEAR::isError($result))
            {
                return T_('Upgrading DB failed: ') . $result->toString();
            }            

            if (!PEAR::isError($result))
                $results += $result;                          

            $Settings->setSetting("DBVersion", 2.1);                
            
        }
        
        if($olddbversion < 2.2)
        {        

            // Need to check if columns exist, or just drop them?
            $sql1 = "ALTER TABLE radpostauth
                    DROP COLUMN ServiceType,
                    DROP COLUMN FramedIPAddress,
                    DROP COLUMN CallingStationId";
            
            $result = $this->radminDB->exec($sql1);
            // Don't care if it's an error            

            $sql = "ALTER TABLE radpostauth
                    ADD COLUMN ServiceType varchar(32) DEFAULT NULL,
                    ADD COLUMN FramedIPAddress varchar(15) DEFAULT NULL,
                    ADD COLUMN CallingStationId varchar(50) DEFAULT NULL";

            $result = $this->db->exec($sql);
            
            if (PEAR::isError($result))
            {
                return T_('Upgrading DB failed: ') . $result->toString();
            }            

            if (!PEAR::isError($result))
                $results += $result;                          

            $Settings->setSetting("DBVersion", 2.2);                
        }


        if($results > 0)
        {            
            return T_('Database upgraded') . $results;
        }
        
        return false;

    }
        
    public function clearOldBatches()
    {
        $rowsaffected = false;
        // Delete user names from batch that are no longer in radcheck table (gone)

        // Not the fastest way to do this, but due to it being in 2 different databases that we wish to keep user perms separate for, we need to execute extra queries and do some php processing
        $sql = "SELECT UserName FROM radcheck";
        
        $result = $this->db->queryAll($sql);
        
        if (PEAR::isError($result))
        {
            return T_('Unable to select user from radcheck') . $result->toString();
        }
        
        foreach($result as $user)
        {
            $users[] = $this->db->quote($user['UserName']);
        }
        $users = implode( ', ' , $users);
        
        // $users has already been escaped above
        $sql = "DELETE FROM batch WHERE UserName NOT IN ($users)";
        
        $sql2 = "DELETE FROM batches WHERE batchID NOT IN (SELECT batchID FROM batch)";
        
        $result = $this->radminDB->exec($sql);
        
        if (PEAR::isError($result))
        {
            return T_('Unable to cleanup old users from batch ') . $result->toString();
        }
                         
        $rowsaffected += $result;        
        
        $result = $this->radminDB->exec($sql2);
        
        if (PEAR::isError($result))
        {
            return T_('Unable to cleanup old batches ') . $result->toString();
        }
                         
        $rowsaffected += $result;

        if($rowsaffected) return "($rowsaffected) " . T_('Old Batches Cleaned');
        
        return false;
    }         

    public function clearStaleSessions()
    {
        /* Finds all Sessions that appear to have timed out
         * Timed out is when the StartTime + SessionTime is more than 300 seconds older than now
         * So essentially any sessions that haven't updated the session date in the last 5 minutes
         * */
        $sql = "UPDATE radacct
                SET
                AcctTerminateCause='Admin-Reset',
                AcctStopTime = FROM_UNIXTIME(UNIX_TIMESTAMP(AcctStartTime) + AcctSessionTime)
                WHERE
                (AcctStopTime IS NULL OR AcctStopTime = 0)
                AND
                TIME_TO_SEC(
                            TIMEDIFF(
                                     NOW(),
                                     ADDTIME(
                                             AcctStartTime,
                                             SEC_TO_TIME(AcctSessionTime)
                                            )
                                     )
                            ) > 300";
        
        $result = $this->db->exec($sql);
        
        if (PEAR::isError($result))
        {
            return T_('Clearing stale sessions failed: ') . $result->toString();
        }

        if($result > 0)        
            return T_('Stale sessions cleared') . $result;
        
        return false;

    }
    
    public function deleteExpiredUsers()
    {
        /* Do select to get list of usernames
         * Run deleteUser over each username (this clears all junk easily
         * can be condensed into less queries but this removes complexity
         * */
         
        //  SELECT UserName FROM radcheck WHERE Attribute = 'Expiration' AND Value LIKE 'January __ 2011 00:00:00'
         
        // Loop through previous months encase they have been missed. Bit of overkill but works. Time is cheap
        $months = array(-2, -3, -4, -5, -6, -7, -8, -9, -10, -11, -12);
        $deleted_results = 0;
        foreach($months as $month)
        {
            $timepattern = strftime("%B __ %Y 00:00:00", strtotime("$month months"));
            $sql = sprintf("SELECT UserName
                            FROM radcheck
                            WHERE Attribute = %s AND
                            Value LIKE %s",
                            $this->db->quote('Expiration'),
                            $this->db->quote($timepattern)
                            );
            
            $results = $this->db->queryAll($sql);
            
            if (PEAR::isError($results))
            {
                return T_('Fetching users to delete failed') . $results->toString();
            }
            
            foreach($results as $user)
            {
                AdminLog::getInstance()->log_cron("Cron Deleting ${user['UserName']}");
                $this->deleteUser($user['UserName']);
            }
            $deleted_results += sizeof($results);
        }

        if($deleted_results)
            return "($deleted_results) " . T_('Expired users deleted');
            
        return false;
         
    }
    
    public function condensePreviousMonthsAccounting()
    {
        $rowsaffected = 0;
        $months = array(-2, -3, -4, -5, -6, -7, -8, -9, -10, -11, -12);
        // If we remove -1 and leave last months data, the recurring data limits will work better, however lots of other code will need to change to search both mtotacct and radacct for information.
        // Probably better to implement the extra code as this will preserve more accounting data for longer TODO:
        foreach($months as $month)
        {
            // Generate start and end dates for each month in question        
            $startdate = strftime("%Y-%m-%d", strtotime("first day of $month months"));
            $nextmonth = $month + 1;
            $enddate = strftime("%Y-%m-%d", strtotime("first day of $nextmonth months"));

            // Select all radacct data for month into mtotaccttmp
            // (which totals it)            
            $sql = sprintf("INSERT INTO mtotaccttmp
                             (UserName,
                             AcctDate,
                             ConnNum,
                             ConnTotDuration,
                             ConnMaxDuration,
                             ConnMinDuration,
                             InputOctets,
                             OutputOctets,
                             NASIPAddress)
                             SELECT UserName,
                             %s,
                             COUNT(*),
                             SUM(AcctSessionTime),
                             MAX(AcctSessionTime),
                             MIN(AcctSessionTime),
                             SUM(AcctInputOctets),
                             SUM(AcctOutputOctets),
                             NASIPAddress
                             FROM radacct
                             WHERE AcctStopTime >= %s AND
                             AcctStopTime < %s
                             GROUP BY UserName,NASIPAddress",
                             $this->db->quote($startdate),
                             $this->db->quote($startdate),
                             $this->db->quote($enddate)
                             );
                             
            $result = $this->db->exec($sql);
            
            if (PEAR::isError($result))
            {
                return T_('Unable to insert data into mtotaccttmp: ') . $result->toString();
            }
                             
            $rowsaffected += $result;

            // Remove user details from radacct that we just put into mtotaccttmp
        
            $sql = sprintf("DELETE FROM radacct
                            WHERE AcctStopTime >= %s
                            AND AcctStopTime < %s",
                            $this->db->quote($startdate),
                            $this->db->quote($enddate)
                            );
                            
            $result = $this->db->exec($sql);
            
            if (PEAR::isError($result))
            {
                return T_('Unable to delete old radacct data: ') . $result->toString();
            }          

            $rowsaffected += $result;                              
        
            // Update users details in radcheck for Max-octets and Max-All-Session
            
            $sql = sprintf("UPDATE radcheck, mtotaccttmp
                            SET
                            radcheck.value = CAST((radcheck.value - (mtotaccttmp.InputOctets + mtotaccttmp.OutputOctets)) AS UNSIGNED)
                            WHERE radcheck.Attribute=%s
                            AND radcheck.UserName=mtotaccttmp.UserName
                            AND mtotaccttmp.AcctDate=%s",
                            $this->db->quote('Max-Octets'),
                            $this->db->quote($startdate)
                            );
                            
            $result = $this->db->exec($sql);
            
            if (PEAR::isError($result))
            {
                return T_('Unable to update users Max-Octets: ') . $result->toString();
            }   
            
            $rowsaffected += $result;                                     
                            
            $sql = sprintf("UPDATE radcheck, mtotaccttmp
                            SET
                            radcheck.value = radcheck.value - mtotaccttmp.ConnTotDuration 
                            WHERE radcheck.Attribute = %s
                            AND radcheck.UserName = mtotaccttmp.UserName
                            AND mtotaccttmp.AcctDate = %s",
                            $this->db->quote('Max-All-Session'),
                            $this->db->quote($startdate)
                            );
                            
            $result = $this->db->exec($sql);
            
            if (PEAR::isError($result))
            {
                return T_('Unable to update users Max-All-Session: ') . $result->toString();
            }   
            
            $rowsaffected += $result;                                     
        
            // Insert mtotaccttmp details into mtotacct (update what is already in there?)
            // TODO: Do we need to do a select & delete from mtotacct into mtotaccttmp to ensure only a single line for each user per month in mtotacct?
            
            $sql = "INSERT INTO mtotacct (
                    UserName,
                    AcctDate,
                    ConnNum,
                    ConnTotDuration, 
                    ConnMaxDuration, 
                    ConnMinDuration, 
                    InputOctets, 
                    OutputOctets, 
                    NASIPAddress
                    )
                    SELECT 
                    UserName, 
                    AcctDate, 
                    ConnNum, 
                    ConnTotDuration, 
                    ConnMaxDuration, 
                    ConnMinDuration, 
                    InputOctets, 
                    OutputOctets, 
                    NASIPAddress
                    FROM 
                    mtotaccttmp";
                    
            $result = $this->db->exec($sql);
            
            if (PEAR::isError($result))
            {
                return T_('Unable to move mtotaccttmp data to mtotacct: ') . $result->toString();
            }   
            
            $rowsaffected += $result;                             
        
            // Clear mtotaccttmp
            
            $sql = "TRUNCATE mtotaccttmp";
            
            $result = $this->db->exec($sql);
            
            if (PEAR::isError($result))
            {
                return T_('Unable to truncate mtotaccttmp: ') . $result->toString();
            }   
            
            $rowsaffected += $result;                     
        
            // Ensure all radcheck values are > 0 where appropriate
            
            $sql = sprintf("UPDATE radcheck 
                            SET
                            radcheck.value = 0 
                            WHERE
                            radcheck.Attribute = %s
                            AND radcheck.value < 0",
                            $this->db->quote('Max-Octets')
                            );
                            
            $result = $this->db->exec($sql);
            
            if (PEAR::isError($result))
            {
                return T_('Unable to ensure positive values in radcheck: ') . $result->toString();
            }
            
            $rowsaffected += $result;                                        
        
            
            // Disabled, as we can have old accounting data due to clocks not being set. Need another way to handle this.
            // Clear all data in radacct older than X months that has been missed?
            // TODO
            /*if($month == "-12")
            {
                $sql = sprintf("DELETE FROM radacct
                                WHERE AcctStopTime < %s",
                                $this->db->quote($enddate)
                                );
                                
                $result = $this->db->exec($sql);
                
                if (PEAR::isError($result))
                {
                    return T_('Unable to delete ancient radacct data: ') . $result->toString();
                }    
                
                $rowsaffected += $result;                                            
                
            }*/
        }
        
        if($rowsaffected > 0)        
            return T_('Old Radius Accounting Data Archived') . $rowsaffected;
        
        return false;
    }
}

/* Post auth needs some cleaning up.
This will cleanup all mac address auths and coovachilli auths, leaving only the last attempt
DELETE t1 from radpostauth t1, radpostauth t2 WHERE t1.username=t2.username AND t1.reply = t2.reply AND t1.id < t2.id AND (t1.username  REGEXP '^([[:xdigit:]]{2}-){5}[[:xdigit:]]{2}' OR t1.username = "CoovaChilli")

Probably only need to clear rejects for mac address, and clear accepts for coovachilli admin user
*/

?>
