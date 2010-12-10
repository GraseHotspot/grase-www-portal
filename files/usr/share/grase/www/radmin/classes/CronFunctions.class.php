<?php

/* Copyright 2010 Timothy White */

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
    
    private function __construct()
    {
        $this->db =& DatabaseConnections::getInstance()->getRadiusDB();
    }    
         

    public function clearStaleSessions()
    {
        /* Finds all Sessions that appear to have timed out
         * Timed out is when the StartTime + SessionTime is more than 300 seconds older than now
         * AND When starttime is more than 12 hours ago
         * So essentially any sessions that haven't updated the session date in the last 5 minutes and started more than 12 hours ago
         * */
        $sql = "UPDATE radacct
                SET
                AcctTerminateCause='Admin-Reset',
                AcctStopTime = FROM_UNIXTIME(UNIX_TIMESTAMP(AcctStartTime) + AcctSessionTime)
                WHERE
                AcctStopTime = 0 AND
                TIME_TO_SEC(
                            TIMEDIFF(
                                     NOW(),
                                     ADDTIME(
                                             AcctStartTime,
                                             SEC_TO_TIME(AcctSessionTime)
                                            )
                                     )
                            ) > 300 AND
                TIME_TO_SEC(
                            TIMEDIFF(
                                     NOW(),
                                     AcctStartTime
                                     )
                            ) > 43200";
        
        $result = $this->db->exec($sql);
        
        if (PEAR::isError($result))
        {
            return _('Clearing stale sessions failed: ') . $result->toString();
        }
        
        return _('Stale sessions cleared');
    }
    
    public function deleteExpiredUsers()
    {
        /* Do select to get list of usernames
         * Run deleteUser over each username (this clears all junk easily
         * can be condensed into less queries but this removes complexity
         * */
         
        //  SELECT UserName FROM radcheck WHERE Attribute = 'Expiration' AND Value LIKE 'January __ 2011 00:00:00'
         
        // Loop through previous months incase they have been missed. Bit of overkill but works. Time is cheap
        $months = array(-2, -3, -4, -5, -6, -7, -8, -9, -10, -11, -12);
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
                return _('Fetching users to delete failed') . $results->toString();
            }
            
            foreach($results as $user)
            {
                AdminLog::getInstance()->log_cron("Cron Deleting ${user['UserName']}");
                $this->deleteUser($user['UserName']);
            }
        }
        
        return _('Expired users deleted');
         
    }
    
    public function condensePreviousMonthsAccounting()
    {
        $months = array(-2, -3, -4, -5, -6, -7, -8, -9, -10, -11, -12);
        foreach($months as $month)
        {
            // Generate start and end dates for each month in question        
            $startdate = strftime("%Y-%m-%d", strtotime("first day $month months"));
            $nextmonth = $month + 1;
            $enddate = strftime("%Y-%m-%d", strtotime("first day $nextmonth months"));
            
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
                return _('Unable to insert data into mtotaccttmp: ') . $result->toString();
            }
                             

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
                return _('Unable to delete old radacct data: ') . $result->toString();
            }                            
        
            // Update users details in radcheck for Max-octets and Max-All-Session
            
            $sql = sprintf("UPDATE radcheck, mtotaccttmp
                            SET
                            radcheck.value = radcheck.value - (mtotaccttmp.InputOctets + mtotaccttmp.OutputOctets)
                            WHERE radcheck.Attribute=%s
                            AND radcheck.UserName=mtotaccttmp.UserName
                            AND mtotaccttmp.AcctDate=%s",
                            $this->db->quote('Max-Octets'),
                            $this->db->quote($startdate)
                            );
                            
            $result = $this->db->exec($sql);
            
            if (PEAR::isError($result))
            {
                return _('Unable to update users Max-Octets: ') . $result->toString();
            }                            
                            
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
                return _('Unable to update users Max-All-Session: ') . $result->toString();
            }                            
        
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
                return _('Unable to move mtotaccttmp data to mtotacct: ') . $result->toString();
            }                    
        
            // Clear mtotaccttmp
            
            $sql = "TRUNCATE mtotaccttmp";
            
            $result = $this->db->exec($sql);
            
            if (PEAR::isError($result))
            {
                return _('Unable to truncate mtotaccttmp: ') . $result->toString();
            }            
        
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
                return _('Unable to ensure positive values in radcheck: ') . $result->toString();
            }                            
        
            // Clear all data in radacct older than X months that has been missed?
            
            if($month == "-12")
            {
                $sql = sprintf("DELETE FROM radacct
                                WHERE AcctStopTime < %s",
                                $this->db->quote($enddate)
                                );
                                
                $result = $this->db->exec($sql);
                
                if (PEAR::isError($result))
                {
                    return _('Unable to delete anchient radacct data: ') . $result->toString();
                }                                
                
            }
        }
        
        return _('Old Radius Accounting Data Archived');
    }
}

?>
