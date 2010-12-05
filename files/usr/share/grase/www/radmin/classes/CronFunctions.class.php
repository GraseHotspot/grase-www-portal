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
        
        $result = $this->db->query($sql);
        
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
            $timepattern = strftime("%B __ %Y 00:00:00", strtotime("$months months"));
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
}

?>
