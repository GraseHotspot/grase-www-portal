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

class DatabaseFunctions
{
    public $db; // Radius DB
    
    public function &getInstance()
    {
        // Static reference of this class's instance.
        static $instance;
        if(!isset($instance)) {
            $instance = new DatabaseFunctions();
        }
        return $instance;
    }    
    
    public function __construct()
    {
        $this->db =& DatabaseConnections::getInstance()->getRadiusDB();
    }
    
    
    
    
    /* Database Functions
     * getSoldData          -   Gets total amount of data sold for valid accounts
     * getMonthUsedData     -   Gets how much data was used in that month (historic)
     * getUsedData          -   Gets current month used data
     *
     *
     *
     */
    
    // Accounting Functions
    
    // NAME:     getSoldData
    // PURPOSE:  Retrieves from the Database the total Data Sold to users
    // IMPORTS:
    // EXPORTS:  The total value of all sold data in octets

    // DatabaseFunctions::getInstance()->getSoldData();
    public function getSoldData() 
    {
        // Only counts Max-Octets currently (Future proof with up and down) TODO
        $sql = "select SUM(Value)
			      FROM radcheck
			      WHERE Attribute='Max-Octets'
			      AND Username IN (
			      	select UserName
			      	FROM radcheck
			      	WHERE Attribute='Expiration'
			      	AND STR_TO_DATE(Value,'%M %d %Y %T') > now()
			      );"; // DONE Fix to only include non-expired users

        $result = $this->db->queryOne($sql);
        
        if (PEAR::isError($result)) {
            ErrorHandling::fatal_db_error(_('Retrieving Sold Usage failed: '), $result);
        }
        $soldoctets = $result;
        return $soldoctets + 0;
    }
    
    /* NAME:     getMonthUsedData
    * PURPOSE:  Retrieves from the Database the total data used for the selected
    *			 month
    * IMPORTS:  Month (as a numeric value of 1-12), defaults to last month
    * EXPORTS:  The total value of all sold data in octets
    * Asserts:
    *			 Must be a month that is earlier than current month, otherwise the
    *			 data will not be in the mtotacct table
    */

    public function getMonthUsedData($month = "") 
    {

        // TODO: Will only work for months in mtotacct        
        if ($month == "") $month = date("m") - 1; //last month
        $date = date("Y-m-d", mktime(0, 0, 0, $month, 1, date("Y"))); // last month
        
        $sql = sprintf("SELECT 
            SUM(InputOctets) + SUM(OutputOctets) AS TotalOctets
            FROM mtotacct
            WHERE AcctDate='%s'",
            mysql_real_escape_string($date));
        
        $usedoctets = $this->db->queryOne($sql);
        
        if (PEAR::isError($usedoctets)) {
            ErrorHandling::fatal_db_error(_('Retrieving Month Usage failed: '), $usedoctets);
        }
        
        return $usedoctets;
    }
    
    public function getUsedData() 
    {
        $sql = "SELECT
            SUM(radacct.AcctInputOctets) + SUM(radacct.AcctOutputOctets)
            FROM radacct
            WHERE DATE_SUB(CURDATE(),INTERVAL 30 DAY) <= AcctStartTime";
        
        $usedoctets = $this->db->queryOne($sql);
        
        if (PEAR::isError($usedoctets)) {
            ErrorHandling::fatal_db_error(_('Retrieving Current Month Usage failed: '), $usedoctets);
        }
        
        return $usedoctets + 0;
    }
    
    
    
    public function getRadiusSessionDetails($radacctid) 
    {
        $sql = sprintf("SELECT RadAcctId,
            AcctStartTime,
            AcctStopTime,
            AcctSessionTime,
            FramedIPAddress,
            Username,
            AcctInputOctets,
            AcctOutputOctets,
            SUM(AcctInputOctets + AcctOutputOctets) AS AcctTotalOctets,
            CallingStationId
            FROM radacct
            WHERE RadAcctID = '%s'
            ORDER BY RadAcctId DESC",
            mysql_real_escape_string($radacctid));
        
        $session = $this->db->queryRow($sql);
        
         if (PEAR::isError($session)) {
            ErrorHandling::fatal_db_error(_('Retrieving Session by RadAcctID failed: '), $session);
        }
        
        return $session;
    }
    
    public function getRadiusUserSessionsDetails($username = '') 
    {
        $where_clause = sprintf("WHERE Username = '%s'", mysql_real_escape_string($username));
        if($username == '') $where_clause = '';
        $sql = sprintf("SELECT RadAcctId,
            AcctStartTime,
            AcctStopTime,
            AcctSessionTime,
            FramedIPAddress,
            Username,
            AcctInputOctets,
            AcctOutputOctets,
            AcctInputOctets + AcctOutputOctets AS AcctTotalOctets,
            CallingStationId
            FROM radacct
            %s
            ORDER BY RadAcctId DESC",
            $where_clause);
            
        $sessions = $this->db->queryAll($sql);
        
        if (PEAR::isError($sessions))
        {
            ErrorHandling::fatal_db_error(_('Retrieving Sessions by Username failed: '), $sessions);
        }        
        return $sessions;            
        
    }
    
    public function getRadiusUserByCurrentSession($ipaddress)
    {
        // Gets the username for an active session based on ip address
        $sql = sprintf("SELECT UserName
	            FROM radacct
	            WHERE FramedIPAddress='%s'
	            AND AcctStopTime IS NULL
	            ORDER BY AcctStartTime DESC LIMIT 1",
	            $ipaddress);
        
        $username = $this->db->queryOne($sql);
        
        if (PEAR::isError($username))
        {
            ErrorHandling::fatal_db_error(_('Retrieving Username by Active Session (ipadddress): '), $username);
        }
        
        return $username;
    }        

    public function getUserDetails($username)
    {
        // Will be from function getDBUserDetails($username) // was database_get_user_details
        $Userdata['Username'] = $username;

        // Get radcheck attributes
        $sql = sprintf("SELECT Attribute, Value
                        FROM radcheck
                        WHERE Username = %s",
                        $this->db->quote($username)
                        );
                        
        $results = $this->db->queryAll($sql);
        
        if (PEAR::isError($results))
        {
            ErrorHandling::fatal_db_error(_('Get User details Query failed: '), $results);
        }
        
        foreach ($results as $attribute) 
        {
            $Userdata[$attribute['Attribute']] = $attribute['Value'];
        }

        // User Data Limit
        
        if (isset($Userdata['Max-Octets'])) 
        {
            $Userdata['MaxOctets'] = $Userdata['Max-Octets'];
            $Userdata['MaxMb'] = sprintf('%0.2f',$Userdata['Max-Octets'] / 1024 / 1024); //Needed for forms
        }

        // User Expiry
        
        if (isset($Userdata['Expiration'])) 
        {
            $Userdata['FormatExpiration'] = substr($Userdata['Expiration'], 0, -8);
            $Userdata['ExpirationTimestamp'] = strtotime(substr($Userdata['Expiration'], 0, -8));
        }
        else
        {
            $Userdata['Expiration'] = "--";
            $Userdata['FormatExpiration'] = "--";
        }
        
        // User "time" limit
        
        if (isset($Userdata['Max-All-Session'])) 
        {
            $Userdata['MaxAllSession'] = $Userdata['Max-All-Session'];
            $Userdata['MaxTime'] = $Userdata['Max-All-Session'] / 60;
        }

        // Get User Group
        $Userdata['Group'] = $this->getUserGroup($username);

        // Get Data usage
        $Userdata['AcctTotalOctets'] = $this->getUserDataUsage($username);
        $Userdata['TotalOctets'] = $this->getUserDataUsageTotal($username);

        // Get Total Session Time
        $Userdata['TotalTimeMonth'] = $this->getUserTotalSessionTime($username);


        // Get Last Logout
        $Userdata['LastLogout'] = $this->getUserLastLogoutTime($username);

        // Get Account Status
        $Userdata['account_status'] = $this->_userAccountStatus($Userdata);
        
        // Get User Comment
        $Userdata['Comment'] = $this->getUserComment($username);
        
        return $Userdata;        
    }
    
    public function getUserGroup($username)
    {
        // Get users group
        $sql = sprintf("SELECT GroupName
                        FROM radusergroup
                        WHERE UserName = %s
                        ORDER BY priority
                        LIMIT 1",
                        $this->db->quote($username, 'text', true, true));
        $results = $this->db->queryOne($sql);
        
        if (PEAR::isError($results))
        {
            ErrorHandling::fatal_db_error(_('Get User Group Query failed: '), $results);
        }
        
        return $results;
    }
    
    public function getAllUserNames()
    {
        // Gets an array of all usernames in radcheck table
        $sql = "SELECT UserName
	            FROM radcheck
	            WHERE Attribute='Password'
	            ORDER BY id";
        
        $results = $this->db->queryAll($sql);
        
        if (PEAR::isError($results))
        {
            ErrorHandling::fatal_db_error(_('Get All Usernames Query Failed: '), $results);
        }
        
        foreach ($results as $user)
        {
            $users[] = $user['UserName'];
        }
        
        return $users;
    }
    
    public function getUserLastLogoutTime($username)
    {
        // Get Last Logout
        $sql = sprintf("SELECT AcctStopTime
		              FROM radacct
		              WHERE AcctTerminateCause != ''
		              AND UserName = %s
		              ORDER BY RadAcctId DESC LIMIT 1",
		              $this->db->quote($username, 'text', true, true));
		              
	    $results = $this->db->queryOne($sql);
	
	    if (PEAR::isError($results))
	    {
	        ErrorHandling::fatal_db_error(_('Get User Last Logout Query Failed: '), $results);
	    }
        
        if (is_null($results)) 
        {
            $LastLogout = "...";
        }
        else
        {
            $LastLogout = $results;
        }
        
        return $LastLogout;    
    }
    
    public function getUserTotalSessionTime($username)
    {
        // Get Total Session Time
        $sql = sprintf("SELECT
		              SUM(AcctSessionTime)
		              FROM radacct
		              WHERE UserName= %s
		              LIMIT 1",
		              $this->db->quote($username, 'text', true, true));
		              
		$results = $this->db->queryOne($sql);
		if (PEAR::isError($results))
		{
            ErrorHandling::fatal_db_error(_('Get User Total Session Time Query failed: '), $results);
        }

        return $results;
    }
    
    public function getUserDataUsage($username)
    {
        // Get Data usage
        $sql = sprintf("SELECT (
		                SUM(radacct.AcctInputOctets)+SUM(radacct.AcctOutputOctets)
		              )
		              AS AcctTotalOctets
		              FROM radacct
		              WHERE UserName= %s",
		              $this->db->quote($username, 'text', true, true));
		              
		$results = $this->db->queryOne($sql);
		if (PEAR::isError($results))
		{
            ErrorHandling::fatal_db_error(_('Get User Data Usage Query failed: '), $results);
        }
        
        return $results + 0; // Need to zero it if null
    }
    
    public function getUserDataUsageTotal($username)
    {
        // Get Data usage
        $sql = sprintf("SELECT (
		                SUM(mtotacct.InputOctets) + SUM(mtotacct.OutputOctets)
		              )
		              AS TotalOctets
		              FROM mtotacct
		              WHERE UserName= %s",
		              $this->db->quote($username, 'text', true, true));
		              
		$results = $this->db->queryOne($sql);
		if (PEAR::isError($results))
		{
            ErrorHandling::fatal_db_error(_('Get User Data Usage (Total) Query failed: '), $results);
        }
        
        return $results + 0; // Need to zero it if null
    }    
    
    public function getMonthlyAccounts()
    {
        $sql = "SELECT Username,
                       AcctDate,
                       InputOctets,
                       OutputOctets,
                       SUM(InputOctets + OutputOctets) as TotalOctets
                 FROM mtotacct";
        
        $results = $this->db->queryAll($sql);
        
        if (PEAR::isError($results))
        {
            ErrorHandling::fatal_db_error(_('Get Monthly Data Usage Query failed: '), $results);
        }
        
        foreach($results as $result)
        {
            $monthly_accounts[$result['AcctDate']][] = $row;
        }
        
        krsort($monthly_accounts);
        
        return $monthly_accounts;
    }
    
    public function getMonthlyAccountsTotals()
    {
        $sql = "SELECT AcctDate,
                       SUM(InputOctets) as TotalInputOctets,
                       SUM(OutputOctets)as TotalOutputOctets,
                       SUM(InputOctets + OutputOctets) as TotalOctets
                FROM mtotacct
                WHERE AcctDate=AcctDate
                GROUP BY AcctDate";
        
        $results = $this->db->queryAll($sql);
        
        if (PEAR::isError($results))
        {
            ErrorHandling::fatal_db_error(_('Get Monthly Data Usage Totals Query failed: '), $results);
        }
        
        foreach($results as $result)
        {
            $monthly_accounts_totals[$result['AcctDate']] = $row;
        }
        
        krsort($monthly_accounts_totals);
        
        return $monthly_accounts_totals;
    }
    


    public function getUserComment($username)
    {
        $sql = sprintf("SELECT Comment
                         FROM radusercomment
                         WHERE UserName = %s",
                         $this->db->quote($username));
        
        $results = $this->db->queryOne($sql);
        
        if (PEAR::isError($results))
        {
            ErrorHandling::fatal_db_error(_('Get User Comment failed: '), $results);
        }
        
        return trim($results);
    }
    
    public function createUser($username, $password, $datalimitmb, $timelimitmins, $expirydate, $group, $comment)
    {
        $this->setUserPassword($username, $password);
        
        if ($group)
            $this->setUserGroup($username, $group);
        
        if ($datalimitmb)
            $this->setUserDataLimit($username, $datalimitmb);
        
        if ($timelimitmins)
            $this->setUserTimeLimit($username, $timelimitmins);
        
        if (trim($expirydate) && trim($expirydate) != '--') 
            $this->setUserExpiry($username, $expirydate);
        
        if (trim($comment))
            $this->setUserComment($username, $comment);

        
        return true;    
    }

    public function setUserComment($username, $comment)
    {
        $fields = array (
            'Username'  => array ( 'value' => $username,    'key' => true),
            'Comment'     => array ( 'value' => $comment . ' ' )
            );
        
        $result = $this->db->replace('radusercomment', $fields);
        
        if (PEAR::isError($result))
        {
            ErrorHandling::fatal_db_error(_('Setting User Comment Query Failed: '), $result);
        }
        
        return $result;
    }
    
    public function setUserGroup($username, $group)
    {
        $fields = array (
            'UserName'  => array ( 'value' => $username,    'key' => true),
            'Priority'  => array ( 'value' => 1,  'key' => true),
            'GroupName' => array ( 'value' => $group )
            );
        
        $result = $this->db->replace('radusergroup', $fields);
        
        if (PEAR::isError($result))
        {
            ErrorHandling::fatal_db_error(_('Setting User Group Query Failed: '), $result);
        }
        
        return $result;    
    }
    
    public function setUserDatalimit($username, $limitmb)
    {
        $datalimitoctets = $limitmb * 1024 * 1024;
        $fields = array (
            'Username'  => array ( 'value' => $username,    'key' => true),
            'Attribute' => array ( 'value' => 'Max-Octets',  'key' => true),
            'op'        => array ( 'value' => ':=' ),
            'Value'     => array ( 'value' => intval($datalimitoctets))
            );   
        
        $result = $this->db->replace('radcheck', $fields);
        
        if (PEAR::isError($result))
        {
            ErrorHandling::fatal_db_error(_('Setting User Datalimit Query Failed: '), $result);
        }
        
        return $result;     
    }
    
    public function increaseUserDatalimit($username, $addmb)
    {
        $addoctets = $addmb * 1024 * 1024;
        // NOTE: This is a select and an replace (using setUserDataLimit) so that it can be called without worry for a missing field.
 
        $sql = sprintf("SELECT Value
                        FROM radcheck
                        WHERE Username = %s
                        AND Attribute = %s",
                        $this->db->quote($username),
                        $this->db->quote('Max-Octets')
                        );
        
        $result = $this->db->queryOne($sql);
        
        if (PEAR::isError($result))
        {
            ErrorHandling::fatal_db_error(_('Getting User Current Data Limit to increase failed: '), $result);
        }
        
        return $this->setUserDatalimit($username, ($result + $addoctets)/1024/1024);
    
    }
    
    public function setUserTimelimit($username, $limitmins)
    {
        $limitsecs = $limitmins * 60;
        $fields = array (
            'Username'  => array ( 'value' => $username,    'key' => true),
            'Attribute' => array ( 'value' => 'Max-All-Session',  'key' => true),
            'op'        => array ( 'value' => ':=' ),
            'Value'     => array ( 'value' => $limitsecs)
            );   
        
        $result = $this->db->replace('radcheck', $fields);
        
        if (PEAR::isError($result))
        {
            ErrorHandling::fatal_db_error(_('Setting User Timelimit Query Failed: '), $result);
        }
        
        return $result;     
    }    

    public function increaseUserTimelimit($username, $addmins)
    {
        $addsecs = $addmins * 60;
        // NOTE: This is a select and an replace (using setUserTimelimit) so that it can be called without worry for a missing field.
 
        $sql = sprintf("SELECT Value
                        FROM radcheck
                        WHERE Username = %s
                        AND Attribute = %s",
                        $this->db->quote($username),
                        $this->db->quote('Max-All-Session')
                        );
        
        $result = $this->db->queryOne($sql);
        
        if (PEAR::isError($result))
        {
            ErrorHandling::fatal_db_error(_('Getting User Current Time Limit to increase failed: '), $result);
        }
        
        return $this->setUserTimelimit($username, ($result + $addsecs)/60);
    
    }    
    
   public function setUserPassword($username, $password)
    {
        $fields = array (
            'Username'  => array ( 'value' => $username,    'key' => true),
            'Attribute' => array ( 'value' => 'Password',  'key' => true),
            'op'        => array ( 'value' => ':=' ),
            'Value'     => array ( 'value' => $password)
            );   
        
        $result = $this->db->replace('radcheck', $fields);
        
        if (PEAR::isError($result))
        {
            ErrorHandling::fatal_db_error(_('Setting User Password Query Failed: '), $result);
        }
        
        return $result;     
    }
    
    public function setUserExpiry($username, $expirydate)
    {
        if (trim($expirydate) && trim($expirydate) != '--')
        {
            // We have a valid expiry date, update value in database
            $fields = array (
                'Username'  => array ( 'value' => $username,    'key' => true),
                'Attribute' => array ( 'value' => 'Expiration',  'key' => true),
                'op'        => array ( 'value' => ':=' ),
                'Value'     => array ( 'value' => $this->_expiryFormat($expirydate))
                );

            $result = $this->db->replace('radcheck', $fields);
            
            if (PEAR::isError($result))
            {
                ErrorHandling::fatal_db_error(_('Setting User Expiry Date Query Failed: '), $result);
            }
            
            return $result;     
        }
        else
        {
            // No expiry, delete key from database
            $sql = sprintf("DELETE FROM radcheck
                            WHERE Username=%s
                            AND Attribute=%s",
                            $this->db->quote($username),
                            $this->db->quote('Expiration')
                            );
            
            $result = $this->db->queryOne($sql);
            
            if (PEAR::isError($result))
            {
                ErrorHandling::fatal_db_error(_('Deleting User Expiry Date Query Failed: '), $result);
            }
            
            return $result;
        }
    }    
    
    public function deleteUser($username)
    {
    
        /* Remove data from radcheck */
        // TODO: Remove from radreply?
        $sql = sprintf("DELETE from radcheck
                        WHERE UserName=%s",
                        $this->db->quote($username));
        
        $result = $this->db->query($sql);
        
        if (PEAR::isError($result))
        {
            ErrorHandling::fatal_db_error(_('Deleting user failed: '). "($username) ", $result);
        }

        /* Remove user from group */
        $sql = sprintf("DELETE from radusergroup
                        WHERE UserName=%s",
                        $this->db->quote($username));
        
        $result = $this->db->query($sql);
        
        if (PEAR::isError($result))
        {
            ErrorHandling::fatal_db_error(_('Deleting users group failed: '). "($username) ", $result);
        }
        
        /* Remove user comment */
        $sql = sprintf("DELETE from radusercomment
                        WHERE UserName=%s",
                        $this->db->quote($username));
        
        $result = $this->db->query($sql);
        
        if (PEAR::isError($result))
        {
            ErrorHandling::fatal_db_error(_('Deleting users comment failed: '). "($username) ", $result);
        }
                      
                        
        return true;
    }
    
    public function checkUniqueUsername($username)
    {
        // TODO: Make this a COUNT() and then a queryOne
        $sql = sprintf("SELECT Username
            FROM radcheck
            WHERE Username='%s'",
            mysql_real_escape_string($username));
            
        $results = $this->db->query($sql);
        
        if (PEAR::isError($results))
        {
            ErrorHandling::fatal_db_error(_('Checking Uniq Username failed: '), $results);
        }
        
        $unique = true;
        if($results->numRows() != 0)
        {
            $unique = false;
        }
        
        return $unique;
    }
    
    private function _expiryFormat($date)
    {
    	list($year, $month, $day) = explode("-", $date, 3);
    	
	    if($year && $month && $day)
	        return date("F d Y H:i:s", mktime(0,0,0, $month, $day, $year));
	        // ^^ Was previously. But as year, month and day should all exist, it has been shortened
	        //return date("F d Y H:i:s", makeTimeStamp($year, $month, $day));	        
	        
	    if(!$year && !$month && !$day)
	        return "";
	        
	    // Should never get here as Expiry date is now handled automatically and isn't user supplied
	    ErrorHandling::fatal_error(_("Problem With expiration Date Format"));
    }
    
    private function _userAccountStatus($Userdata)
    {
        // NOTE: It would be nice if all this could be changed at some time
	    if(isset($Userdata['ExpirationTimestamp']) && $Userdata['ExpirationTimestamp'] < time())
	    {
	        $status = EXPIRED_ACCOUNT;
	    }
	    elseif(isset($Userdata['Max-Octets']) && ($Userdata['Max-Octets'] - $Userdata['AcctTotalOctets']) <= 0 )
	    {
	        $status = LOCKED_ACCOUNT;
	    }
	    elseif(isset($Userdata['Max-Octets']) && ($Userdata['Max-Octets'] - $Userdata['AcctTotalOctets']) <= 1024*1024*2 )
	    {
	        $status = LOWDATA_ACCOUNT;
	    }
	    elseif($Userdata['Group'] == MACHINE_GROUP_NAME)
	    {
	        $status = MACHINE_ACCOUNT; 
	    }
	    elseif($Userdata['Group'] != "")
	    {
	        $status = NORMAL_ACCOUNT;
	    }
	    else
	    {
	        $status = NOGROUP_ACCOUNT;
	    }
	    return $status;    
    }
           
}



?>
