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

class DatabaseFunctions
{

    /*
     *  DatabaseFunctions is a Class that deals with with RADIUS database
     *
     */
    public $db; // Radius DB

    // radminDB only here because CronFunctions inherits from us. Maybe move this to CronFunctions? TODO:
    public $radminDB; // Radmin DB

    private $groupdetails = array(); //cache group details 

    private $usercache = array(); // Cache users details
    private $usercacheloaded = false;

    public function &getInstance()
    {
        // Static reference of this class's instance.
        static $instance;
        if (!isset($instance)) {
            $instance = new DatabaseFunctions();
        }
        return $instance;
    }

    public function __construct()
    {
        $this->db =& DatabaseConnections::getInstance()->getRadiusDB();
        $this->radminDB =& DatabaseConnections::getInstance()->getRadminDB();

        // Share SQL Query between functions
        $this->insert_radius_values_sql = $this->db->prepare(
            'INSERT INTO radreply
        	(Username, Attribute, op, Value)
        	VALUES (?, ?, ?, ?)',
            array('text', 'text', 'text', 'text'),
            MDB2_PREPARE_MANIP
        );
    }

    public function replace_radcheck_query(
        $username,
        $attribute,
        $op = ':=',
        $value
    ) {
        // Take out some of the duplicate code in below functions
        $fields = array(
            'Username' => array('value' => $username, 'key' => true),
            'Attribute' => array('value' => $attribute, 'key' => true),
            'op' => array('value' => $op),
            'Value' => array('value' => $value)
        );

        // Error checking is left to calling function
        return $this->db->replace('radcheck', $fields);
    }


    public function getMonthsAccountingDataAvailableFor()
    {
        $sql = "SELECT DATE_FORMAT(AcctDate, '%Y-%m') AS Month FROM mtotacct
                UNION
                SELECT DATE_FORMAT(AcctStartTime, '%Y-%m') AS Month from radacct";

        $res =& $this->db->query($sql);

        if (PEAR::isError($res)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Retrieving Months Accounting Data Available For failed: '),
                $res
            );
        }

        $results = $res->fetchAll(MDB2_FETCHMODE_ASSOC, false, false);

        $monthsavailable = array();

        foreach ($results as $result) {
            //$label = date('Y-m-d', strtotime($result['Month']));
            $label = date('F Y', strtotime($result['Month']));
            $monthsavailable[$result['Month']] = $label;
        }

        ksort($monthsavailable);
        return $monthsavailable;;
    }


    public function getRadiusSessionDetails($radacctid)
    {
        $sql = sprintf(
            "SELECT RadAcctId,
                        AcctStartTime,
                        AcctStopTime,
                        AcctSessionTime,
                        AcctTerminateCause,
                        ServiceType,
                        FramedIPAddress,
                        Username,
                        AcctInputOctets,
                        AcctOutputOctets,
                        (AcctInputOctets + AcctOutputOctets) AS AcctTotalOctets,
                        CallingStationId
                        FROM radacct
                        WHERE RadAcctID = %s
                        ORDER BY RadAcctId DESC",
            $this->db->quote($radacctid)
        );

        $session = $this->db->queryRow($sql);

        if (PEAR::isError($session)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Retrieving Session by RadAcctID failed: '),
                $session
            );
        }

        return $session;
    }

    public function getRadiusUserSessionsDetails($username = '')
    {
        $where_clause = sprintf(
            "WHERE Username = %s",
            $this->db->quote($username)
        );
        if ($username == '') {
            $where_clause = '';
        }
        $sql = sprintf(
            "SELECT RadAcctId,
                        AcctStartTime,
                        AcctStopTime,
                        AcctSessionTime,
                        AcctTerminateCause,
                        ServiceType,
                        FramedIPAddress,
                        Username,
                        AcctInputOctets,
                        AcctOutputOctets,
                        AcctInputOctets + AcctOutputOctets AS AcctTotalOctets,
                        CallingStationId
                        FROM radacct
                        %s
                        ORDER BY RadAcctId DESC",
            $where_clause
        );

        $sessions = $this->db->queryAll($sql);

        if (PEAR::isError($sessions)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Retrieving Sessions by Username failed: '),
                $sessions
            );
        }
        return $sessions;

    }

    public function getActiveRadiusSessionsDetails($username = '')
    {
        $where_clause = sprintf(
            "AND Username = %s",
            $this->db->quote($username)
        );
        if ($username == '') {
            $where_clause = '';
        }
        $sql = sprintf(
            "SELECT RadAcctId,
                        AcctStartTime,
                        AcctStopTime,
                        AcctSessionTime,
                        AcctTerminateCause,
                        ServiceType,
                        FramedIPAddress,
                        Username,
                        AcctInputOctets,
                        AcctOutputOctets,
                        AcctInputOctets + AcctOutputOctets AS AcctTotalOctets,
                        CallingStationId
                        FROM radacct
                        WHERE (AcctStopTime = '' OR AcctStopTime IS NULL)
                        %s
                        ORDER BY RadAcctId DESC",
            $where_clause
        );

        $sessions = $this->db->queryAll($sql);

        if (PEAR::isError($sessions)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Retrieving Sessions by Username failed: '),
                $sessions
            );
        }
        return $sessions;

    }

    public function getRadiusIDCurrentSessionByUser($username)
    {
        // Gets the username for an active session based on ip address
        $sql = sprintf(
            "SELECT RadAcctId
                            FROM radacct
                            WHERE UserName= %s
                            AND AcctStopTime IS NULL
                            ORDER BY AcctStartTime DESC LIMIT 1",
            $this->db->quote($username)
        );

        $radacctid = $this->db->queryOne($sql);

        if (PEAR::isError($radacctid)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Retrieving Current Session by Username failed: '),
                $radacctid
            );
        }

        return $radacctid;
    }

    public function getRadiusUserByCurrentSession($ipaddress)
    {
        // Gets the username for an active session based on ip address
        $sql = sprintf(
            "SELECT UserName
                            FROM radacct
                            WHERE FramedIPAddress= %s
                            AND AcctStopTime IS NULL
                            ORDER BY AcctStartTime DESC LIMIT 1",
            $this->db->quote($ipaddress)
        ); // TODO: Ensure all sprintf's are using quote

        $username = $this->db->queryOne($sql);

        if (PEAR::isError($username)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Retrieving Username by Active Session (ipadddress): '),
                $username
            );
        }

        return $username;
    }

    public function loadAllUserDetails($force = false)
    {
        // If there is stuff in the cache and force isn't set, don't do anything
        if ($this->usercacheloaded && $force == false) {
            return true;
        }

        // Load all the user details we are going to lookup and cache them!

        /* Lowercase the initial key (username) to make lookups case insensitive!! */

        // Radcheck
        $sql = "SELECT Attribute, Value, UserName FROM radcheck";

        $results = $this->db->queryAll($sql);

        if (PEAR::isError($results)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Get All User radcheck details Query failed: '),
                $results
            );
        }

        foreach ($results as $row) {
            $this->usercache[mb_strtolower(
                $row['UserName']
            )]['radcheck'][$row['Attribute']] = $row['Value'];
        }

        // Radreply
        $sql = "SELECT Attribute, Value, UserName FROM radreply";

        $results = $this->db->queryAll($sql);

        if (PEAR::isError($results)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Get All User radreply details Query failed: '),
                $results
            );
        }

        foreach ($results as $row) {
            $this->usercache[mb_strtolower(
                $row['UserName']
            )]['radreply'][$row['Attribute']] = $row['Value'];
        }


        // Usergroup
        $sql = "SELECT GroupName, UserName FROM radusergroup";

        $results = $this->db->queryAll($sql);

        if (PEAR::isError($results)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Get All User Group details Query failed: '),
                $results
            );
        }

        foreach ($results as $user) {
            $this->usercache[mb_strtolower(
                $user['UserName']
            )]['GroupName'] = $user['GroupName'];
        }

        // Comment
        $sql = "SELECT Comment, UserName FROM radusercomment";

        $results = $this->db->queryAll($sql);

        if (PEAR::isError($results)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Get All User Comment details Query failed: '),
                $results
            );
        }

        foreach ($results as $user) {
            $this->usercache[mb_strtolower(
                $user['UserName']
            )]['Comment'] = $user['Comment'];
        }
        // AcctTotalOctets from radacct
        // AcctSessionTime from radacct
        // Last logout from radacct            
        $sql = "SELECT UserName,
            SUM(radacct.AcctInputOctets)+SUM(radacct.AcctOutputOctets)
            AS AcctTotalOctets,
            SUM(AcctSessionTime) AS AcctSessionTime,
            MAX(AcctStopTime) AS LastLogout
            FROM radacct
            GROUP BY UserName";

        $results = $this->db->queryAll($sql);

        if (PEAR::isError($results)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Get All User radacct details Query failed: '),
                $results
            );
        }

        foreach ($results as $user) {
            $this->usercache[mb_strtolower(
                $user['UserName']
            )]['AcctTotalOctets'] = $user['AcctTotalOctets'];
            $this->usercache[mb_strtolower(
                $user['UserName']
            )]['AcctSessionTime'] = $user['AcctSessionTime'];
            $this->usercache[mb_strtolower(
                $user['UserName']
            )]['LastLogout'] = $user['LastLogout'];
        }

        // TotalTime from mtotacct        
        // TotalOctets from mtotacct        
        $sql = "SELECT UserName,
            SUM(mtotacct.ConnTotDuration) AS TotalTime,
            SUM(mtotacct.InputOctets) + SUM(mtotacct.OutputOctets) AS TotalOctets
            FROM mtotacct GROUP BY UserName";

        $results = $this->db->queryAll($sql);

        if (PEAR::isError($results)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Get All User mtotacct details Query failed: '),
                $results
            );
        }

        foreach ($results as $user) {
            $this->usercache[mb_strtolower(
                $user['UserName']
            )]['TotalTime'] = $user['TotalTime'];
            $this->usercache[mb_strtolower(
                $user['UserName']
            )]['TotalOctets'] = $user['TotalOctets'];
        }

        $this->usercacheloaded = true;
    }

    public function getMultipleUsersDetails($usernames)
    {
        $users = array();
        // For each user, get their information
        foreach ($usernames as $username) {
            $users[] = $this->getUserDetails($username);
        }
        return $users;
    }

    public function getUserDetails($username)
    {
        if ($this->usercacheloaded) {
            $Userdata = $this->usercache[mb_strtolower($username)]['radcheck'];
            $Userreplydata = $this->usercache[mb_strtolower(
                $username
            )]['radreply'];
            $Userdata['Username'] = $username;
        } else {

            $Userdata['Username'] = $username;

            // Get radcheck attributes
            $sql = sprintf(
                "SELECT Attribute, Value
                                            FROM radcheck
                                            WHERE Username = %s",
                $this->db->quote($username)
            );

            $results = $this->db->queryAll($sql);

            if (PEAR::isError($results)) {
                \Grase\ErrorHandling::fatalDatabaseError(
                    T_('Get User details Query failed: '),
                    $results
                );
            }

            foreach ($results as $attribute) {
                $Userdata[$attribute['Attribute']] = $attribute['Value'];
            }

            // Get radreply attributes
            $sql = sprintf(
                "SELECT Attribute, Value
                                            FROM radreply
                                            WHERE Username = %s",
                $this->db->quote($username)
            );

            $results = $this->db->queryAll($sql);

            if (PEAR::isError($results)) {
                \Grase\ErrorHandling::fatalDatabaseError(
                    T_('Get User radreply details Query failed: '),
                    $results
                );
            }

            foreach ($results as $attribute) {
                $Userreplydata[$attribute['Attribute']] = $attribute['Value'];
            }

        }

        // User Password (Upgraded to Cleartext-Password, but smarty doesn't like '-' in names)
        if (isset($Userdata['Cleartext-Password']) && !isset($Userdata['Password'])) {
            $Userdata['Password'] = $Userdata['Cleartext-Password'];
        }

        // User Data Limit

        if (isset($Userdata['Max-Octets'])) {
            $Userdata['MaxOctets'] = $Userdata['Max-Octets'];
            $Userdata['MaxMb'] = sprintf(
                '%0.2f',
                $Userdata['Max-Octets'] / 1024 / 1024
            ); //Needed for forms
        }

        // User Expiry

        if (isset($Userdata['Expiration'])) {
            $Userdata['FormatExpiration'] = date(
                "M j Y H:i:s",
                strtotime($Userdata['Expiration'])
            );
            if (substr($Userdata['Expiration'], -8) == "00:00:00") {
                $Userdata['FormatExpiration'] = substr(
                    $Userdata['FormatExpiration'],
                    0,
                    -8
                );
            }
            $Userdata['ExpirationTimestamp'] = strtotime(
                $Userdata['Expiration']
            );
        } else {
            $Userdata['Expiration'] = "--";
            $Userdata['FormatExpiration'] = "--";
        }

        if (isset($Userdata['GRASE-ExpireAfter'])) {
            $Userdata['ExpireAfter'] = $Userdata['GRASE-ExpireAfter'];
        }

        // User Account Lockout
        if (isset($Userdata['Auth-Type'])) {
            // Check we are actually locked (Reject)
            if ($Userdata['Auth-Type'] == "Reject") {
                $Userdata['AccountLock'] = true;
                $Userdata['LockReason'] = $Userreplydata['Reply-Message'];
            }
            // Get message
        }

        // User "time" limit

        if (isset($Userdata['Max-All-Session'])) {
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
        $Userdata['TotalTimeAll'] = $this->getUserSessionTimeTotal($username);


        // User remaining time

        if (isset($Userdata['Max-All-Session'])) {
            $Userdata['RemainingSeconds'] = $Userdata['Max-All-Session'] - $Userdata['TotalTimeMonth'];
            if ($Userdata['RemainingSeconds'] < 0) {
                $Userdata['RemainingSeconds'] = 0;
            }
        }

        // Get Last Logout
        $Userdata['LastLogout'] = $this->getUserLastLogoutTime($username);

        // Get Account Status
        $Userdata['account_status'] = $this->_userAccountStatus($Userdata);

        // Get User Comment
        $Userdata['Comment'] = $this->getUserComment($username);

        // Get Information about groups (it's cached, so might as well fetch it all)
        $groupdata = $this->getGroupAttributes();

        if (isset($groupdata[$Userdata['Group']])) {
            $Userdata['GroupSettings'] = $groupdata[$Userdata['Group']];
        }

        return $Userdata;
    }

    public function getUserGroup($username)
    {
        if ($this->usercacheloaded) {
            return $this->usercache[mb_strtolower(
                $username
            )]['GroupName'];
        }
        // Get users group
        $sql = sprintf(
            "SELECT GroupName
                                    FROM radusergroup
                                    WHERE UserName = %s
                                    ORDER BY priority
                                    LIMIT 1",
            $this->db->quote($username, 'text', true, true)
        );
        $results = $this->db->queryOne($sql);

        if (PEAR::isError($results)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Get User Group Query failed: '),
                $results
            );
        }

        return $results;
    }

    public function getAllUserNames()
    {
        // Gets an array of all usernames in radcheck table
        $sql = "SELECT UserName
	            FROM radcheck
	            WHERE Attribute='Cleartext-Password'
	            AND UserName NOT IN (
	                SELECT UserName 
	                FROM radcheck 
	                WHERE Attribute='Service-Type' 
	                AND Value='Administrative-User'
	            )	                
	            ORDER BY id";
        // Filters out Coova Chilli Config user.
        // Maybe just filter out groupless users?

        $results = $this->db->queryAll($sql);

        if (PEAR::isError($results)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Get All Usernames Query Failed: '),
                $results
            );
        }

        foreach ($results as $user) {
            $users[] = $user['UserName'];
        }

        return $users;
    }

    public function getUsersByGroup($groupname)
    {
        // Gets an array of all usernames in radcheck table
        $sql = sprintf(
            "SELECT UserName
                            FROM radusergroup
                            WHERE GroupName = %s",
            $this->db->quote($groupname)
        );

        $results = $this->db->queryAll($sql);

        if (PEAR::isError($results)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Get Users By Group Query Failed: '),
                $results
            );
        }

        foreach ($results as $user) {
            $users[] = $user['UserName'];
        }

        return $users;
    }

    public function getUserLastLogoutTime($username)
    {
        if ($this->usercacheloaded) {
            $results = $this->usercache[mb_strtolower($username)]['LastLogout'];
        } else {
            // Get Last Logout
            $sql = sprintf(
                "SELECT AcctStopTime
                                          FROM radacct
                                          WHERE AcctTerminateCause != ''
                                          AND UserName = %s
                                          ORDER BY RadAcctId DESC LIMIT 1",
                $this->db->quote($username, 'text', true, true)
            );

            $results = $this->db->queryOne($sql);

            if (PEAR::isError($results)) {
                \Grase\ErrorHandling::fatalDatabaseError(
                    T_('Get User Last Logout Query Failed: '),
                    $results
                );
            }
        }

        if (is_null($results)) {
            $LastLogout = "...";
        } else {
            $LastLogout = $results;
        }

        return $LastLogout;
    }

    // Session time for current month TODO: rename?
    public function getUserTotalSessionTime($username)
    {
        if ($this->usercacheloaded) {
            return $this->usercache[mb_strtolower(
                $username
            )]['AcctSessionTime'];
        }
        // Get Total Session Time
        $sql = sprintf(
            "SELECT
                                  SUM(AcctSessionTime)
                                  FROM radacct
                                  WHERE UserName= %s
                                  LIMIT 1",
            $this->db->quote($username, 'text', true, true)
        );

        $results = $this->db->queryOne($sql);
        if (PEAR::isError($results)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Get User Total Session Time Query failed: '),
                $results
            );
        }

        return $results;
    }

    // Session time for all time
    public function getUserSessionTimeTotal($username)
    {
        if ($this->usercacheloaded) {
            $results = $this->usercache[mb_strtolower($username)]['TotalTime'];
        } else {
            // Get Time usage
            $sql = sprintf(
                "SELECT (
                                            SUM(mtotacct.ConnTotDuration)
                                          )
                                          AS TotalTime
                                          FROM mtotacct
                                          WHERE UserName= %s",
                $this->db->quote($username, 'text', true, true)
            );

            $results = $this->db->queryOne($sql);
            if (PEAR::isError($results)) {
                \Grase\ErrorHandling::fatalDatabaseError(
                    T_('Get User Time Usage (Total) Query failed: '),
                    $results
                );
            }
        }
        // We want a real total, not an archived total
        return $this->getUserTotalSessionTime(
            $username
        ) + $results + 0; // Need to zero it if null
    }


    public function getUserDataUsage($username)
    {
        if ($this->usercacheloaded) {
            return $this->usercache[mb_strtolower(
                $username
            )]['AcctTotalOctets'] + 0;
        }

        // Get Data usage
        $sql = sprintf(
            "SELECT (
                                    SUM(radacct.AcctInputOctets)+SUM(radacct.AcctOutputOctets)
                                  )
                                  AS AcctTotalOctets
                                  FROM radacct
                                  WHERE UserName= %s",
            $this->db->quote($username, 'text', true, true)
        );

        $results = $this->db->queryOne($sql);
        if (PEAR::isError($results)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Get User Data Usage Query failed: '),
                $results
            );
        }

        return $results + 0; // Need to zero it if null
    }

    public function getUserDataUsageTotal($username)
    {
        if ($this->usercacheloaded) {
            $results = $this->usercache[mb_strtolower(
                $username
            )]['TotalOctets'];
        } else {
            // Get Data usage
            $sql = sprintf(
                "SELECT (
                                            SUM(mtotacct.InputOctets) + SUM(mtotacct.OutputOctets)
                                          )
                                          AS TotalOctets
                                          FROM mtotacct
                                          WHERE UserName= %s",
                $this->db->quote($username, 'text', true, true)
            );

            $results = $this->db->queryOne($sql);
            if (PEAR::isError($results)) {
                \Grase\ErrorHandling::fatalDatabaseError(
                    T_('Get User Data Usage (Total) Query failed: '),
                    $results
                );
            }
        }
        // We want a real total, not an archived total
        return $this->getUserDataUsage(
            $username
        ) + $results + 0; // Need to zero it if null
    }

    public function getUserComment($username)
    {
        if ($this->usercacheloaded) {
            return trim(
                $this->usercache[mb_strtolower($username)]['Comment']
            );
        }

        $sql = sprintf(
            "SELECT Comment
                                     FROM radusercomment
                                     WHERE UserName = %s",
            $this->db->quote($username)
        );

        $results = $this->db->queryOne($sql);

        if (PEAR::isError($results)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Get User Comment failed: '),
                $results
            );
        }

        return trim($results);
    }

    public function createUser(
        $username,
        $password,
        $datalimitmb,
        $timelimitmins,
        $expirydate,
        $expireAfter,
        $group,
        $comment
    ) {
        // Check unique user
        if (!$this->checkUniqueUsername(
            $username
        )
        ) {
            return false;
        } // This may get done multiple times for the same user in batch creation

        $this->setUserPassword($username, $password);

        if ($group) {
            $this->setUserGroup($username, $group);
        }

        if (is_numeric($datalimitmb)) {
            $this->setUserDataLimit($username, $datalimitmb);
        }

        if (is_numeric($timelimitmins)) {
            $this->setUserTimeLimit($username, $timelimitmins);
        }

        if (trim($expirydate) && trim($expirydate) != '--') {
            $this->setUserExpiry($username, $expirydate);
        }

        if (trim($expireAfter)) {
            $this->setUserExpireAfter($username, $expireAfter);
        }

        if (trim($comment)) {
            $this->setUserComment($username, $comment);
        }


        return true;
    }

    // This function is used via Cron to assist with upgrades, but is otherwise obsolete
    public function setGroupSimultaneousUse($name, $value)
    {
        $sql = sprintf(
            "DELETE FROM radgroupcheck WHERE GroupName = %s AND Attribute = 'Simultaneous-Use'",
            $this->db->quote($name)
        );

        $result = $this->db->exec($sql);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Deleting radgroupcheck query failed: '),
                $result
            );
        }

        if ($value > 0) // We leave deleted if < 0
        {
            $fields = array(
                'GroupName' => array('value' => $name, 'key' => true),
                'Attribute' => array(
                    'value' => 'Simultaneous-Use',
                    'key' => true
                ),
                'op' => array('value' => ":="),
                'Value' => array('value' => $value)
            );

            $result = $this->db->replace('radgroupcheck', $fields);
            if (PEAR::isError($result)) {
                \Grase\ErrorHandling::fatalDatabaseError(
                    T_('Adding Group Check Attributes query failed:  '),
                    $result
                );
            }
        }

        return $result;
    }


    public function setGroupAttributes($name, $attributes)
    {
        $checkitems = array(
            'SimultaneousUse',
            'MaxOctets',
            'MaxSeconds',
            'hourTime',
            'dayTime',
            'weekTime',
            'monthTime',
            'hourData',
            'dayData',
            'weekData',
            'monthData',
            'LoginTime',

        );
        // DELETE all attributes from groupreply
        $sql = sprintf(
            "DELETE FROM radgroupreply WHERE GroupName = %s",
            $this->db->quote($name)
        );

        $result = $this->db->exec($sql);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Deleting radgroupreply query failed: '),
                $result
            );
        }

        // DELETE all attributes from groupcheck
        $sql = sprintf(
            "DELETE FROM radgroupcheck WHERE GroupName = %s",
            $this->db->quote($name)
        );

        $result = $this->db->exec($sql);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Deleting radgroupreply query failed: '),
                $result
            );
        }

        if (isset($attributes['DataRecurLimit'])) {
            $attributes[$attributes['DataRecurTime'] . 'Data'] = \Grase\Util::bigIntVal(
                $attributes['DataRecurLimit'] * 1024 * 1024
            );
            unset($attributes['DataRecurLimit']);
            unset($attributes['DataRecurTime']);
        }

        if (isset($attributes['TimeRecurLimit'])) {
            $attributes[$attributes['TimeRecurTime'] . 'Time'] = $attributes['TimeRecurLimit'] * 60;
            unset($attributes['TimeRecurLimit']);
            unset($attributes['TimeRecurTime']);
        }

        if (isset($attributes['MaxMb'])) {
            $attributes['MaxOctets'] = \Grase\Util::bigIntVal(
                $attributes['MaxMb'] * 1024 * 1024
            );
            unset($attributes['MaxMb']);
        }

        if (isset($attributes['MaxTime'])) {
            $attributes['MaxSeconds'] = $attributes['MaxTime'] * 60;
            unset($attributes['MaxTime']);
        }

        if (isset($attributes['SimultaneousUse'])) {
            //$this->setGroupSimultaneousUse($name, $attributes['SimultaneousUse']);
            if ($attributes['SimultaneousUse'] == "") {
                unset($attributes['SimultaneousUse']);
            }

        }

        $attributelookup = array(
            'MaxOctets' => 'Max-Octets',
            'MaxSeconds' => 'Max-All-Session',
            'hourTime' => 'Max-Hourly-Session',
            'dayTime' => 'Max-Daily-Session',
            'weekTime' => 'Max-Weekly-Session',
            'monthTime' => 'Max-Monthly-Session',
            'hourData' => 'Max-Hourly-Octets',
            'dayData' => 'Max-Daily-Octets',
            'weekData' => 'Max-Weekly-Octets',
            'monthData' => 'Max-Monthly-Octets',
            'BandwidthDownLimit' => 'ChilliSpot-Bandwidth-Max-Down',
            'BandwidthUpLimit' => 'ChilliSpot-Bandwidth-Max-Up',
            'SimultaneousUse' => 'Simultaneous-Use',
            'LoginTime' => 'Login-Time',

        );
        // Insert each attribute
        foreach ($attributes as $key => $value) {
            if (isset($attributelookup[$key])) {
                $table = 'radgroupreply';
                $op = '=';
                if (in_array($key, $checkitems)) {
                    $table = 'radgroupcheck';
                    $op = ':=';
                }

                $fields = array(
                    'GroupName' => array('value' => $name, 'key' => true),
                    'Attribute' => array(
                        'value' => $attributelookup[$key],
                        'key' => true
                    ),
                    'op' => array('value' => $op),
                    'Value' => array('value' => $value)
                );

                $result = $this->db->replace($table, $fields);
                if (PEAR::isError($result)) {
                    \Grase\ErrorHandling::fatalDatabaseError(
                        T_('Adding Group Attributes query failed:  '),
                        $result
                    );
                }
            }
        }

        AdminLog::getInstance()->log("Group $name updated db settings");
    }

    public function getGroupAttributes($groupname = '', $clearcache = false)
    {

        if (!$clearcache && $groupname != '' && isset($this->groupdetails[$groupname])) {
            return $this->groupdetails;
        }
        if (!$clearcache && $groupname == '' && sizeof(
                $this->groupdetails
            ) > 0
        ) {
            return $this->groupdetails;
        }

        $groups = array();
        if (!$clearcache) {
            $groups = $this->groupdetails;
        }


        // setup sql for getting group and check attributes     
        if ($groupname != '') {
            $sql = sprintf(
                "SELECT GroupName, Attribute, Value
                            FROM radgroupreply WHERE GroupName = %s",
                $this->db->quote($groupname)
            );
            $sql2 = sprintf(
                "SELECT GroupName, Attribute, Value
                            FROM radgroupcheck WHERE GroupName = %s",
                $this->db->quote($groupname)
            );
        } else {

            $sql = "SELECT GroupName, Attribute, Value
                FROM radgroupreply";
            $sql2 = "SELECT GroupName, Attribute, Value
            FROM radgroupcheck";
        }


        // Get radgroupreply items    
        $results = $this->db->queryAll($sql);

        if (PEAR::isError($results)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Get Groups details Query failed: '),
                $results
            );
        }

        // Get radgroupcheck items
        $results2 = $this->db->queryAll($sql2);

        if (PEAR::isError($results)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Get Groups details Query failed: '),
                $results
            );
        }

        // Merge results of check and reply
        $results = array_merge($results, $results2);

        $attributelookup = array(
            'Max-Octets' => 'MaxOctets',
            'Max-All-Session' => 'MaxSeconds',
            'Max-Hourly-Session' => 'hour_Time',
            'Max-Daily-Session' => 'day_Time',
            'Max-Weekly-Session' => 'week_Time',
            'Max-Monthly-Session' => 'month_Time',
            'Max-Hourly-Octets' => 'hour_Data',
            'Max-Daily-Octets' => 'day_Data',
            'Max-Weekly-Octets' => 'week_Data',
            'Max-Monthly-Octets' => 'month_Data',
            'ChilliSpot-Bandwidth-Max-Up' => 'BandwidthUpLimit',
            'ChilliSpot-Bandwidth-Max-Down' => 'BandwidthDownLimit',
            'Simultaneous-Use' => 'Simultaneous-Use',
            'Login-Time' => 'LoginTime',

        );

        $recurance_string = array(
            'hour' => T_('Hourly'),
            'day' => T_('Daily'),
            'week' => T_('Weekly'),
            'month' => T_('Monthly')
        );

        foreach ($results as $attribute) {
            @list($recurance, $type) = explode(
                "_",
                $attributelookup[$attribute['Attribute']],
                2
            );
            if ($type == 'Time') {
                $value = $attribute['Value'] / 60;
                $attr = 'TimeRecurLimit';
                $groups[$attribute['GroupName']]['TimeRecurLimitS'] = $attribute['Value'];
                $groups[$attribute['GroupName']]['TimeRecurTime'] = $recurance;
                $groups[$attribute['GroupName']]['TimeRecurTimeFormatted'] = $recurance_string[$recurance];

            } elseif ($type == 'Data') {
                $value = $attribute['Value'] / 1024 / 1024;
                $attr = 'DataRecurLimit';
                $groups[$attribute['GroupName']]['DataRecurLimitB'] = $attribute['Value'];
                $groups[$attribute['GroupName']]['DataRecurTime'] = $recurance;
                $groups[$attribute['GroupName']]['DataRecurTimeFormatted'] = $recurance_string[$recurance];

            } else {
                if ($recurance == "MaxOctets") {
                    $groups[$attribute['GroupName']]['MaxMb'] = $attribute['Value'] / 1024 / 1024;
                    /*                    $value = $attribute['Value'] /1024 /1024;
                                        $attr = "MaxMb";*/
                } elseif ($recurance == "MaxSeconds") {
                    $groups[$attribute['GroupName']]['MaxTime'] = $attribute['Value'] / 60;
                    /*                    $value = $attribute['Value'] / 60;
                                        $attr = "MaxTime";*/
                } elseif ($recurance == "Simultaneous-Use") {
                    $groups[$attribute['GroupName']]['SimultaneousUse'] = $attribute['Value'];
                }
                //else
                //{
                $value = $attribute['Value'];
                $attr = $recurance;
                //}
            }
            $groups[$attribute['GroupName']][$attr] = $value;
        }

        $this->groupdetails = $groups;

        return $groups;

    }

    public function setUserComment($username, $comment)
    {
        $fields = array(
            'Username' => array('value' => $username, 'key' => true),
            'Comment' => array('value' => $comment . ' ')
        );

        $result = $this->db->replace('radusercomment', $fields);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Setting User Comment Query Failed: '),
                $result
            );
        }

        return $result;
    }

    public function setUserGroup($username, $group)
    {
        $fields = array(
            'UserName' => array('value' => $username, 'key' => true),
            'Priority' => array('value' => 1, 'key' => true),
            'GroupName' => array('value' => $group)
        );

        $result = $this->db->replace('radusergroup', $fields);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Setting User Group Query Failed: '),
                $result
            );
        }

        return $result;
    }

    public function setUserDatalimit($username, $limitmb)
    {
        $datalimitoctets = $limitmb * 1024 * 1024;
        $fields = array(
            'Username' => array('value' => $username, 'key' => true),
            'Attribute' => array('value' => 'Max-Octets', 'key' => true),
            'op' => array('value' => ':='),
            'Value' => array(
                'value' => \Grase\Util::bigIntVal(
                        $datalimitoctets
                    )
            )
        );

        $result = $this->db->replace('radcheck', $fields);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Setting User Datalimit Query Failed: '),
                $result
            );
        }

        return $result;
    }

    public function increaseUserDatalimit($username, $addmb)
    {
        $addoctets = $addmb * 1024 * 1024;
        /* NOTE: This is a select and an replace (using setUserDataLimit) so
         * that it can be called without worry for a missing field. */

        $sql = sprintf(
            "SELECT Value
                                    FROM radcheck
                                    WHERE Username = %s
                                    AND Attribute = %s",
            $this->db->quote($username),
            $this->db->quote('Max-Octets')
        );

        $result = $this->db->queryOne($sql);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Getting User Current Data Limit to increase failed: '),
                $result
            );
        }

        return $this->setUserDatalimit(
            $username,
            ($result + $addoctets) / 1024 / 1024
        );

    }

    public function setUserTimelimit($username, $limitmins)
    {
        $limitsecs = $limitmins * 60;
        $fields = array(
            'Username' => array('value' => $username, 'key' => true),
            'Attribute' => array('value' => 'Max-All-Session', 'key' => true),
            'op' => array('value' => ':='),
            'Value' => array('value' => $limitsecs)
        );

        $result = $this->db->replace('radcheck', $fields);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Setting User Timelimit Query Failed: '),
                $result
            );
        }

        return $result;
    }

    public function increaseUserTimelimit($username, $addmins)
    {
        $addsecs = $addmins * 60;
        // NOTE: This is a select and an replace (using setUserTimelimit) so that it can be called without worry for a missing field.

        $sql = sprintf(
            "SELECT Value
                                    FROM radcheck
                                    WHERE Username = %s
                                    AND Attribute = %s",
            $this->db->quote($username),
            $this->db->quote('Max-All-Session')
        );

        $result = $this->db->queryOne($sql);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Getting User Current Time Limit to increase failed: '),
                $result
            );
        }

        return $this->setUserTimelimit($username, ($result + $addsecs) / 60);

    }

    public function setUserPassword($username, $password)
    {
        $fields = array(
            'Username' => array('value' => $username, 'key' => true),
            'Attribute' => array(
                'value' => 'Cleartext-Password',
                'key' => true
            ),
            'op' => array('value' => ':='),
            'Value' => array('value' => $password)
        );

        $result = $this->db->replace('radcheck', $fields);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Setting User Password Query Failed: '),
                $result
            );
        }

        return $result;
    }

    public function setUserExpireAfter($username, $expireAfter)
    {
        $fields = array(
            'Username' => array('value' => $username, 'key' => true),
            'Attribute' => array(
                'value' => 'GRASE-ExpireAfter',
                'key' => true
            ),
            'op' => array('value' => ':='),
            'Value' => array('value' => $expireAfter)
        );

        $result = $this->db->replace('radcheck', $fields);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Setting User ExpireAfter Query Failed: '),
                $result
            );
        }

        return $result;
    }

    public function setUserExpiry($username, $expirydate)
    {
        if (trim($expirydate) && trim($expirydate) != '--') {
            // We have a valid expiry date, update value in database
            $fields = array(
                'Username' => array('value' => $username, 'key' => true),
                'Attribute' => array('value' => 'Expiration', 'key' => true),
                'op' => array('value' => ':='),
                'Value' => array('value' => $this->_expiryFormat($expirydate))
            );

            $result = $this->db->replace('radcheck', $fields);

            if (PEAR::isError($result)) {
                \Grase\ErrorHandling::fatalDatabaseError(
                    T_('Setting User Expiry Date Query Failed: '),
                    $result
                );
            }

            return $result;
        } else {
            // No expiry, delete key from database
            $sql = sprintf(
                "DELETE FROM radcheck
                                            WHERE Username=%s
                                            AND Attribute=%s",
                $this->db->quote($username),
                $this->db->quote('Expiration')
            );

            $result = $this->db->queryOne($sql);

            if (PEAR::isError($result)) {
                \Grase\ErrorHandling::fatalDatabaseError(
                    T_('Deleting User Expiry Date Query Failed: '),
                    $result
                );
            }

            return $result;
        }
    }

    public function lockUser($username, $reason)
    {
        /* Lock a user account with a message */
        $fields = array(
            'Username' => array('value' => $username, 'key' => true),
            'Attribute' => array('value' => 'Auth-Type', 'key' => true),
            'op' => array('value' => ':='),
            'Value' => array('value' => 'Reject')
        );

        $result = $this->db->replace('radcheck', $fields);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Locking User Account Query Failed: '),
                $result
            );
        }

        // Apply message
        $fields = array(
            'Username' => array('value' => $username, 'key' => true),
            'Attribute' => array('value' => 'Reply-Message', 'key' => true),
            'op' => array('value' => ':='),
            'Value' => array('value' => $reason)
        );

        $result = $this->db->replace('radreply', $fields);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('User Account Lock Reason Query Failed: '),
                $result
            );
        }

        AdminLog::getInstance()->log("Locking user $username because: $reason");

    }

    public function unlockUser($username)
    {
        /* Remove a lock on a user account */

        $sql = sprintf(
            "DELETE FROM radcheck
                                WHERE Username=%s
                                AND Attribute=%s",
            $this->db->quote($username),
            $this->db->quote('Auth-Type')
        );

        $result = $this->db->queryOne($sql);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Removing User Lock Query Failed: '),
                $result
            );
        }

        $sql = sprintf(
            "DELETE FROM radreply
                                WHERE Username=%s
                                AND Attribute=%s",
            $this->db->quote($username),
            $this->db->quote('Reply-Message')
        );

        $result = $this->db->queryOne($sql);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Removing User Lock Message Query Failed: '),
                $result
            );
        }

        AdminLog::getInstance()->log("Unlocked user $username");

    }

    public function deleteUser($username)
    {

        /* Remove data from radcheck */
        // TODO: Remove from radreply?
        $sql = sprintf(
            "DELETE from radcheck
                                    WHERE UserName=%s",
            $this->db->quote($username)
        );

        $result = $this->db->query($sql);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Deleting user failed: ') . "($username) ",
                $result
            );
        }

        /* Remove data from radreply */
        $sql = sprintf(
            "DELETE from radreply
                                    WHERE UserName=%s",
            $this->db->quote($username)
        );

        $result = $this->db->query($sql);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Deleting user radreply data failed: ') . "($username) ",
                $result
            );
        }

        /* Remove user from group */
        $sql = sprintf(
            "DELETE from radusergroup
                                    WHERE UserName=%s",
            $this->db->quote($username)
        );

        $result = $this->db->query($sql);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Deleting users group failed: ') . "($username) ",
                $result
            );
        }

        /* Remove user comment */
        $sql = sprintf(
            "DELETE from radusercomment
                                    WHERE UserName=%s",
            $this->db->quote($username)
        );

        $result = $this->db->query($sql);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Deleting users comment failed: ') . "($username) ",
                $result
            );
        }


        return true;
    }

    public function checkUniqueUsername($username)
    {
        // TODO: Make this a COUNT() and then a queryOne
        $sql = sprintf(
            "SELECT Username
                        FROM radcheck
                        WHERE Username= %s",
            $this->db->quote($username)
        );

        $results = $this->db->query($sql);

        if (PEAR::isError($results)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Checking Uniq Username failed: '),
                $results
            );
        }

        $unique = true;
        if ($results->numRows() != 0) {
            $unique = false;
        }

        return $unique;
    }


    /* Functions related to ChilliSpot Config attributes */

    public function getChilliConfigSingle($option)
    {
        $sql = sprintf(
            "SELECT Value
                        FROM radreply
                        WHERE Username=%s
                        AND Attribute='ChilliSpot-Config'
                        AND Value LIKE %s",
            $this->db->quote(RADIUS_CONFIG_USER),
            $this->db->quote($option . "%")
        );

        $value = $this->db->queryOne($sql);

        if (PEAR::isError($value)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Retrieving Chilli Config Single Value failed: '),
                $value
            );
        }

        @ list($option, $value) = explode('=', $value, 2);

        return $value;

    }

    public function delChilliConfig($option, $value = '')
    {
        if ($value != '') {
            $test = "$option=$value";
        } else {
            $test = "$option";
        }

        $sql = sprintf(
            "DELETE
                        FROM radreply
                        WHERE Username= %s
                        AND Attribute='ChilliSpot-Config'
                        AND Value LIKE %s",
            $this->db->quote(RADIUS_CONFIG_USER),
            $this->db->quote($test . "%")
        );

        $result = $this->db->exec($sql);

        if (PEAR::isError($result)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Deleting Chilli Config Query Failed: '),
                $result
            );
        }

        return $result;
    }

    public function setChilliConfigSingle($option, $value)
    {

        /* Because of DB structure, we can't uniquely identify these items, so
         * must delete then insert as they are single values */
        $this->delChilliConfig($option, '');
        // $value is '' so we delete any options as this is a single not multi

        if ($value != '') {
            $test = "$option=$value";
        } else {
            $test = "$option";
        }

        $affected =& $this->insert_radius_values_sql->execute(
            array(
                RADIUS_CONFIG_USER,
                RADIUS_CONFIG_ATTRIBUTE,
                '+=',
                "$test"
            )
        );

        if (PEAR::isError($affected)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Setting Chilli Config Single Query Failed: '),
                $affected
            );
        }

        return $affected;
    }

    public function setChilliConfigMulti($option, $value)
    {

        /* Because it's multiple, we rely on other parts of the software to
         * ensure no dupplicates */

        $affected =& $this->insert_radius_values_sql->execute(
            array(
                RADIUS_CONFIG_USER,
                RADIUS_CONFIG_ATTRIBUTE,
                '+=',
                "$option=$value"
            )
        );

        if (PEAR::isError($affected)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Setting Chilli Config Multi Query Failed: '),
                $affected
            );
        }

        return $affected;
    }

    public function getChilliConfigMulti($option)
    {
        $sql = sprintf(
            "SELECT Value
                        FROM radreply
                        WHERE Username= %s
                        AND Attribute='ChilliSpot-Config'
                        AND Value LIKE %s",
            $this->db->quote(RADIUS_CONFIG_USER),
            $this->db->quote($option . "%")
        );

        $values = $this->db->queryAll($sql);

        if (PEAR::isError($values)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Retrieving Chilli Config Multi Value failed: '),
                $values
            );
        }
        $results = array();
        foreach ($values as $val) {
            list($option, $value) = explode("=", $val['Value']);
            $results[] = $value;
        }

        return $results;
    }


    /* Private functions */

    private function _expiryFormat($date)
    {
        // This function is only called with a valid $date, don't error check.
        return date("F d Y H:i:s", strtotime($date));
    }

    private function _userAccountStatus($Userdata)
    {
        // NOTE: It would be nice if all this could be changed at some time
        if (isset($Userdata['ExpirationTimestamp']) && $Userdata['ExpirationTimestamp'] < time(
            )
        ) {
            $status = EXPIRED_ACCOUNT;
        } elseif (isset($Userdata['Max-Octets']) && ($Userdata['Max-Octets'] - $Userdata['AcctTotalOctets']) <= 0) {
            $status = LOCKED_ACCOUNT;
        } elseif (isset($Userdata['Max-All-Session']) && ($Userdata['Max-All-Session'] - $Userdata['TotalTimeMonth']) <= 0) {
            $status = LOCKED_ACCOUNT;
        } elseif (isset($Userdata['Max-All-Session']) && ($Userdata['TotalTimeMonth'] / $Userdata['Max-All-Session']) > 0.90) {
            $status = LOWTIME_ACCOUNT;
        } // TODO: Change this to a percentage?
        elseif (isset($Userdata['Max-Octets']) && ($Userdata['AcctTotalOctets'] / $Userdata['Max-Octets']) > 0.90) {
            $status = LOWDATA_ACCOUNT;
        } elseif ($Userdata['Group'] == MACHINE_GROUP_NAME) {
            $status = MACHINE_ACCOUNT;
        } elseif ($Userdata['Group'] != "") {
            $status = NORMAL_ACCOUNT;
        } else {
            $status = NOGROUP_ACCOUNT;
        }
        return $status;
    }

    /* Postauth Related functions here */

    public function latestMacFromIP($ipaddress)
    {
        // We limit the selection to a machine that has connected in the last 
        // hour, (this may need to be updated in the future with 
        // CallingStationId for multiple APs)
        $sql = sprintf(
            "SELECT username from radpostauth
                        WHERE FramedIPAddress=%s
                        AND username LIKE '__-__-__-__-__-__'
                        AND authdate > TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 HOUR))

                        ORDER BY ID DESC
                        LIMIT 1",
            $this->db->quote($ipaddress)
        );

        $mac = $this->db->queryOne($sql);

        if (PEAR::isError($mac)) {
            \Grase\ErrorHandling::fatalDatabaseError(
                T_('Retrieving MAC from IP failed: '),
                $mac
            );
        }

        // Check its a MAC address??

        return $mac;
    }

    /* Squid Related Functions HERE */
    public function activeSessionUsername($ipaddress)
    {
        /* select * from radacct WHERE FramedIPAddress != '' AND AcctStopTime = '' OR AcctStopTime IS NULL ORDER BY RadAcctId DESC LIMIT 4; */
        $sql = sprintf(
            "SELECT UserName from radacct
                        WHERE FramedIPAddress=%s
                        AND (AcctStopTime = '' OR AcctStopTime IS NULL)
                        ORDER BY RadAcctId DESC",
            $this->db->quote($ipaddress)
        );

        $user = $this->db->queryOne($sql);

        if (PEAR::isError($user)) {
            // TODO: This needs to be logged in admin log? Won't see error as it's squid calling it
            return "ERR";
            //\Grase\ErrorHandling::fatalDatabaseError(
            //    T_('Retrieving active session from ipaddress failed: '), $user);
        }

        return $user;

    }
}
