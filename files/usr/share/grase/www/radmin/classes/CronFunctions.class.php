<?php

/* Copyright 2010 Timothy White */

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
        if (!isset($instance)) {
            $instance = new CronFunctions();
        }
        return $instance;
    }


    public function activateExpireAfterLogin()
    {
        $rowsaffected = 0;

        $query = "
          SELECT
            radcheck.UserName as username,
            radcheck.value as expireafter,
            UNIX_TIMESTAMP(radpostauth.authdate) as firstlogin
          FROM radius.radcheck, radius.radpostauth
          WHERE
            radcheck.UserName = radpostauth.username
            AND Attribute = 'GRASE-ExpireAfter'
            AND reply = 'Access-Accept'
          GROUP BY radcheck.Username
          ORDER BY authdate";

        $results = $this->db->queryAll($query);
        if (PEAR::isError($results)) {
            return T_('Unable to select users needing First Login Activiation') . $results->toString();
        }

        foreach ($results as $user) {
            $this->setUserExpiry(
                $user['username'],
                date('Y-m-d H:i:s', strtotime($user['expireafter'], $user['firstlogin']))
            );
            $this->setUserExpireAfter($user['username'], '');
            $rowsaffected++;
        }

        if ($rowsaffected) {
            return "($rowsaffected) " . T_('First login users activiated') . "\n";
        }

        return false;
    }

    public function clearOldBatches()
    {
        $rowsaffected = 0;
        // Delete user names from batch that are no longer in radcheck table (gone)

        /*
         * Not the fastest way to do this, but due to it being in 2 different databases that we wish to keep user perms
         * separate for, we need to execute extra queries and do some php processing
         */
        $sql = "SELECT UserName FROM radcheck";
        
        $result = $this->db->queryAll($sql);
        
        if (PEAR::isError($result)) {
            return T_('Unable to select user from radcheck') . $result->toString();
        }
        
        foreach ($result as $user) {
            $users[] = $this->db->quote($user['UserName']);
        }
        $users = implode(', ', $users);
        
        // $users has already been escaped above
        $sql = "DELETE FROM batch WHERE UserName NOT IN ($users)";
        
        $sql2 = "DELETE FROM batches WHERE batchID NOT IN (SELECT batchID FROM batch)";
        
        $result = $this->radminDB->exec($sql);
        
        if (PEAR::isError($result)) {
            return T_('Unable to cleanup old users from batch ') . $result->toString();
        }
                         
        $rowsaffected += $result;
        
        $result = $this->radminDB->exec($sql2);
        
        if (PEAR::isError($result)) {
            return T_('Unable to cleanup old batches ') . $result->toString();
        }
                         
        $rowsaffected += $result;

        if ($rowsaffected) {
            return "($rowsaffected) " . T_('Old Batches Cleaned');
        }
        
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
        
        if (PEAR::isError($result)) {
            return T_('Clearing stale sessions failed: ') . $result->toString();
        }

        if ($result > 0) {
            return T_('Stale sessions cleared') . $result;
        }
        
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
        foreach ($months as $month) {
            $timepattern = strftime("%B %% %Y __:__:__", strtotime("$month months"));
            $sql = sprintf(
                "SELECT UserName
                            FROM radcheck
                            WHERE Attribute = %s AND
                            Value LIKE %s",
                $this->db->quote('Expiration'),
                $this->db->quote($timepattern)
            );
            
            $results = $this->db->queryAll($sql);
            
            if (PEAR::isError($results)) {
                return T_('Fetching users to delete failed') . $results->toString();
            }
            
            foreach ($results as $user) {
                AdminLog::getInstance()->log_cron("Cron Deleting Expired ${user['UserName']}");
                $this->deleteUser($user['UserName']);
            }
            $deleted_results += sizeof($results);
        }

        if ($deleted_results) {
            return "($deleted_results) " . T_('Expired users deleted');
        }
            
        return false;
         
    }
    
    public function deleteOutOfTimeUsers()
    {
        /* Do select to get list of usernames
         * Run deleteUser over each username (this clears all junk easily
         * can be condensed into less queries but this removes complexity
         * */
         
        $deleted_results = 0;
        $sql = sprintf(
            "SELECT UserName
                        FROM radcheck
                        WHERE Attribute = %s AND
                        Value = 0",
            $this->db->quote('Max-All-Session')
        );
        
        $results = $this->db->queryAll($sql);
        
        if (PEAR::isError($results)) {
            return T_('Fetching users to delete failed') . $results->toString();
        }
        
        foreach ($results as $user) {
            AdminLog::getInstance()->log_cron("Cron Deleting OutOfTime ${user['UserName']}");
            $this->deleteUser($user['UserName']);
        }
        $deleted_results += sizeof($results);


        if ($deleted_results) {
            return "($deleted_results) " . T_('OutOfTime users deleted');
        }
            
        return false;
         
    }
    
    public function deleteOutOfDataUsers()
    {
        /* Do select to get list of usernames
         * Run deleteUser over each username (this clears all junk easily
         * can be condensed into less queries but this removes complexity
         * */

        $deleted_results = 0;
        $sql = sprintf(
            "SELECT UserName
                        FROM radcheck
                        WHERE Attribute = %s AND
                        Value = 0",
            $this->db->quote('Max-Octets')
        );
        
        $results = $this->db->queryAll($sql);
        
        if (PEAR::isError($results)) {
            return T_('Fetching users to delete failed') . $results->toString();
        }
        
        foreach ($results as $user) {
            AdminLog::getInstance()->log_cron("Cron Deleting OutOfData ${user['UserName']}");
            $this->deleteUser($user['UserName']);
        }
        $deleted_results += sizeof($results);


        if ($deleted_results) {
            return "($deleted_results) " . T_('OutOfData users deleted');
        }
            
        return false;
         
    }
    
    public function condensePreviousMonthsAccounting()
    {
        $rowsaffected = 0;
        $months = array(-2, -3, -4, -5, -6, -7, -8, -9, -10, -11, -12);
        /*
         * If we remove -1 and leave last months data, the recurring data limits will work better, however lots of other
         * code will need to change to search both mtotacct and radacct for information.
         * Probably better to implement the extra code as this will preserve more accounting data for longer TODO:
         */
        foreach ($months as $month) {
        // Generate start and end dates for each month in question
            $startdate = strftime("%Y-%m-%d", strtotime("first day of $month months"));
            $nextmonth = $month + 1;
            $enddate = strftime("%Y-%m-%d", strtotime("first day of $nextmonth months"));

            // Select all radacct data for month into mtotaccttmp
            // (which totals it)
            $sql = sprintf(
                "INSERT INTO mtotaccttmp
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
            
            if (PEAR::isError($result)) {
                return T_('Unable to insert data into mtotaccttmp: ') . $result->toString();
            }
                             
            $rowsaffected += $result;

            // Remove user details from radacct that we just put into mtotaccttmp
        
            $sql = sprintf(
                "DELETE FROM radacct
                            WHERE AcctStopTime >= %s
                            AND AcctStopTime < %s",
                $this->db->quote($startdate),
                $this->db->quote($enddate)
            );
                            
            $result = $this->db->exec($sql);
            
            if (PEAR::isError($result)) {
                return T_('Unable to delete old radacct data: ') . $result->toString();
            }

            $rowsaffected += $result;
        
            // Update users details in radcheck for Max-octets and Max-All-Session
            
            $sql = sprintf(
                "UPDATE radcheck, mtotaccttmp
                            SET
                            radcheck.value = CAST(radcheck.value AS SIGNED INTEGER) -
                                (mtotaccttmp.InputOctets + mtotaccttmp.OutputOctets)
                            WHERE radcheck.Attribute=%s
                            AND radcheck.UserName=mtotaccttmp.UserName
                            AND mtotaccttmp.AcctDate=%s",
                $this->db->quote('Max-Octets'),
                $this->db->quote($startdate)
            );
                            
            $result = $this->db->exec($sql);
            
            if (PEAR::isError($result)) {
                return T_('Unable to update users Max-Octets: ') . $result->toString();
            }
            
            $rowsaffected += $result;
                            
            $sql = sprintf(
                "UPDATE radcheck, mtotaccttmp
                            SET
                            radcheck.value = CAST(radcheck.value AS SIGNED INTEGER) - mtotaccttmp.ConnTotDuration 
                            WHERE radcheck.Attribute = %s
                            AND radcheck.UserName = mtotaccttmp.UserName
                            AND mtotaccttmp.AcctDate = %s",
                $this->db->quote('Max-All-Session'),
                $this->db->quote($startdate)
            );
                            
            $result = $this->db->exec($sql);
            
            if (PEAR::isError($result)) {
                return T_('Unable to update users Max-All-Session: ') . $result->toString();
            }
            
            $rowsaffected += $result;
        
            // Insert mtotaccttmp details into mtotacct (update what is already in there?)
            /* TODO: Do we need to do a select & delete from mtotacct into mtotaccttmp to ensure only a single line for
             * each user per month in mtotacct?
             * */
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
            
            if (PEAR::isError($result)) {
                return T_('Unable to move mtotaccttmp data to mtotacct: ') . $result->toString();
            }
            
            $rowsaffected += $result;
        
            // Clear mtotaccttmp
            
            $sql = "TRUNCATE mtotaccttmp";
            
            $result = $this->db->exec($sql);
            
            if (PEAR::isError($result)) {
                return T_('Unable to truncate mtotaccttmp: ') . $result->toString();
            }
            
            $rowsaffected += $result;
        
            // Ensure all radcheck values are > 0 where appropriate
        // TODO: Check if any other attributes may need "resetting"

        // Max-Octets reset
            $sql = sprintf(
                "UPDATE radcheck 
                            SET
                            radcheck.value = 0 
                            WHERE
                            radcheck.Attribute = %s
                            AND CAST(radcheck.value AS SIGNED INTEGER) < 0",
                $this->db->quote('Max-Octets')
            );
                            
            $result = $this->db->exec($sql);
            
            if (PEAR::isError($result)) {
                return T_('Unable to ensure positive values in radcheck: ') . $result->toString();
            }
            
            $rowsaffected += $result;

        // Max-All-Session reset
            $sql = sprintf(
                "UPDATE radcheck 
                            SET
                            radcheck.value = 0 
                            WHERE
                            radcheck.Attribute = %s
                            AND CAST(radcheck.value AS SIGNED INTEGER) < 0",
                $this->db->quote('Max-All-Session')
            );
                            
            $result = $this->db->exec($sql);
            
            if (PEAR::isError($result)) {
                return T_('Unable to ensure positive values in radcheck: ') . $result->toString();
            }
            
            $rowsaffected += $result;
        
            
            // Disabled, as we can have old accounting data due to clocks not being set. Need another way to handle this
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
        
        if ($rowsaffected > 0) {
            return T_('Old Radius Accounting Data Archived') . $rowsaffected;
        }
        
        return false;
    }
    public function clearOldPostAuth()
    {
        $twomonthsago = strftime("%Y-%m-%d", strtotime("first day of -1 months"));
        $sql = sprintf(
            "DELETE FROM radpostauth WHERE AuthDate < %s",
            $this->db->quote($twomonthsago)
        );

        $result = $this->db->exec($sql);

        if (PEAR::isError($result)) {
            return T_('Unable to clear old Postauth rows: ') . $result->toString();
        }

        if ($result) {
            return "($result) " . T_('Old Postauth rows cleared');
        }

        return false;
    }

    public function clearPostAuthMacRejects()
    {
        $sql = "
                SELECT id
                FROM radpostauth R
                  JOIN (
                         SELECT
                           username,
                           max(AuthDate) AS maxauthdate
                         FROM radpostauth
                         WHERE username LIKE '__-__-__-__-__-__'
                               AND reply = 'Access-Reject'
                         GROUP BY username
                       ) A ON (R.username = A.username)
                WHERE reply = 'Access-Reject'
                      AND authdate <> maxauthdate";

        $result =& $this->db->query($sql);

        if (PEAR::isError($result)) {
            return T_('Unable to get PostAuth MAC Reject IDs: ') . $result->toString();
        }


        $rows = 0;
        $time_start = microtime(true);
        while (($row = $result->fetchRow())) {
            set_time_limit(30);
            $sql = sprintf(
                "DELETE from radpostauth WHERE id = %s",
                $this->db->quote($row['id'])
            );

            $rowresult = $this->db->exec($sql);


            if (PEAR::isError($rowresult)) {
                return T_('Unable to delete PostAuth MAC Reject entry: ' . $rowresult->toString());
            }

            $rows += $rowresult;

        }
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        if ($rows) {
            return "Deleted $rows in $time seconds: " . T_('PostAuth MAC Reject rows cleared');
        }

        return false;
    }
}

/* Post auth needs some cleaning up.

DELETE all access-rejects for mac addresses except last 1
DELETE R from radpostauth R JOIN (select username, max(AuthDate) AS maxauthdate from radpostauth
WHERE username LIKE '__-__-__-__-__-__' AND reply = 'Access-Reject' GROUP BY username) A ON (R.username = A.username)
WHERE reply= 'Access-Reject' AND authdate <> maxauthdate;

^^^ SQL is SOOOO slow

INSTEAD we do following select which might take a minute

SELECT id from radpostauth R JOIN (select username, max(AuthDate) AS maxauthdate from radpostauth
WHERE username LIKE '__-__-__-__-__-__' AND reply = 'Access-Reject' GROUP BY username) A ON (R.username = A.username)
WHERE reply= 'Access-Reject' AND authdate <> maxauthdate;

THEN WE DELETE 1 by 1
This will cleanup all mac address auths and coovachilli auths, leaving only the last attempt
DELETE t1 from radpostauth t1, radpostauth t2 WHERE t1.username=t2.username AND t1.reply = t2.reply AND t1.id < t2.id
AND (t1.username  REGEXP '^([[:xdigit:]]{2}-){5}[[:xdigit:]]{2}' OR t1.username = "CoovaChilli")

Probably only need to clear rejects for mac address, and clear accepts for coovachilli admin user
*/
