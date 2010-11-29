<?php

/* Copyright 2008 Timothy White */

require_once 'misc_functions.inc.php';
require_once 'load_settings.inc.php';

// MAJOR TODO Migrate all this to new DB stuff

// Connecting, selecting database
$settings = file($CONFIG['radius_database_config_file']);

foreach($settings as $setting) 
{
    list($key, $value) = split(":", $setting);
    $db_settings[$key] = trim($value);
}
$dblink = mysql_pconnect($db_settings['sql_server'], $db_settings['sql_username'], $db_settings['sql_password']) or die('Could not connect: ' . mysql_error());
mysql_select_db($db_settings['sql_database']) or die('Could not select database');



/* DATA Accounting Functions
* getSoldData()
* getUsedData()
*
*
*/

function getSoldData() 
{
    return DatabaseFunctions::getInstance()->getSoldData();
}

function getMonthUsedData($month = "") 
{
    return DatabaseFunctions::getInstance()->getMonthUsedData($month);
}

function getUsedData() 
{
    return DatabaseFunctions::getInstance()->getUsedData();
}

/* RADIUS ACCOUNTING FUNCTIONS
* getDBSessionAccounting($radacctit)
* getDBSessionsAccounting($username = '')
* convertRadacctIPtoUsername($IP)
*
*/

function getDBSessionAccounting($radacctid) 
{
    return DatabaseFunctions::getInstance()->getRadiusSessionDetails($radacctid);
}

function getDBSessionsAccounting($username = '') 
{
    return DatabaseFunctions::getInstance()->getRadiusUserSessionsDetails($username);
}

// Used by scripts/squid_user_group.php
function convertRadacctIPtoUsername($IP) // was database_radacct_ip_to_username
{
    $query = sprintf("SELECT UserName from radacct
			WHERE AcctTerminateCause = ''
			AND FramedIPAddress = '%s'
			ORDER BY RadAcctId DESC LIMIT 1", mysql_real_escape_string($IP));
    $result2 = mysql_db_query('radius', $query) or die('Get IP to User Query failed: ' . mysql_error());
    
    if (mysql_num_rows($result2) == 0) 
    {
        $username = "";
    }
    else
    {
        $username = mysql_result($result2, 0);
    }
    mysql_free_result($result2);
    
    return $username;
}

/* USER DETAILS
*
*
*/

function getDBUserDetails($username) // was database_get_user_details
{
    $Userdata['Username'] = $username;

    // Get radcheck attributes
    $query = "SELECT Attribute,Value
		          FROM radcheck
		          WHERE Username='${Userdata['Username']}'";
    $result2 = mysql_db_query('radius', $query) or die('Get User details Query failed: ' . mysql_error());
    
    while ($attribute = mysql_fetch_array($result2, MYSQL_ASSOC)) 
    {
        $Userdata[$attribute['Attribute']] = $attribute['Value'];
    }
    mysql_free_result($result2);

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
        $Userdata['ExpirationTimestamp'] = expiration_to_timestamp(substr($Userdata['Expiration'], 0, -8));
    }
    else
    {
        $Userdata['Expiration'] = "--";
    }
    
    if (!isset($Userdata['FormatExpiration'])) 
    {
        $Userdata['FormatExpiration'] = "--";
    }

    // User "time" limit
    
    if (isset($Userdata['Max-All-Session'])) 
    {
        $Userdata['MaxAllSession'] = $Userdata['Max-All-Session'];
        $Userdata['MaxTime'] = $Userdata['Max-All-Session'] / 60;
    }

    // Get User Group
    $Userdata['Group'] = getDBUserGroup($username);

    // Get Data usage
    $Userdata['AcctTotalOctets'] = getDBUserDataUsage($username);
    $Userdata['TotalOctets'] = getDBUserDataUsageTotal($username);

    // Get Total Session Time
    $Userdata['TotalTimeMonth'] = getDBUserTotalSessionTime($username);


    // Get Last Logout
    $Userdata['LastLogout'] = getDBUserLastLogoutTime($username);

    // Get Account Status
    $Userdata['account_status'] = user_account_status($Userdata);
    
    // Get User Comment
    $Userdata['Comment'] = getDBComment($username);
    
    return $Userdata;
}

/* Not in use yet. TODO move some of above into these functions and make them active */

function getDBUserLastLogoutTime($username) 
{

    // Get Last Logout
    $query = "SELECT AcctStopTime
		          FROM radacct
		          WHERE AcctTerminateCause != ''
		          AND UserName = '$username'
		          ORDER BY RadAcctId DESC LIMIT 1";
    $result2 = mysql_db_query('radius', $query) or die('Get User Last Logout Query failed: ' . mysql_error());
    
    if (mysql_num_rows($result2) == 0) 
    {
        $Userdata['LastLogout'] = "...";
    }
    else
    {
        $Userdata['LastLogout'] = mysql_result($result2, 0);
    }
    mysql_free_result($result2);
    
    return $Userdata['LastLogout'];
}

function getDBUserTotalSessionTime($username) 
{

    // Get Total Session Time
    $query = "SELECT
		          SUM(AcctSessionTime)
		          FROM radacct
		          WHERE UserName='$username'
		          LIMIT 1";
    $result2 = mysql_db_query('radius', $query) or die('Get User Total Session Time Query failed: ' . mysql_error()); //TODO

    return mysql_result($result2, 0); // Just need a getOne()
    if (mysql_num_rows($result2) == 0) 
    {
        $Userdata['TotalTimeMonth'] = "0" . formatSec('0');
    }
    else
    {
        $Userdata['TotalTimeMonth'] = formatSec(mysql_result($result2, 0));
    }
    mysql_free_result($result2);
    
    return $Userdata['TotalTimeMonth'];
}

function getDBUserDataUsage($username) 
{

    // Get Data usage
    $query = "SELECT (
		            SUM(radacct.AcctInputOctets)+SUM(radacct.AcctOutputOctets)
		          )
		          AS AcctTotalOctets
		          FROM radacct
		          WHERE UserName='$username'";
    $result2 = mysql_db_query('radius', $query) or die('Get User Data Usage Query failed: ' . mysql_error());
    
    if (mysql_num_rows($result2) < 1) 
    {
        $Userdata['AcctTotalOctets'] = '0';
    }
    else
    {
        $Userdata['AcctTotalOctets'] = mysql_result($result2, 0);
    }
    //$Userdata['AcctTotalFormatOctets'] = formatBytes($Userdata['AcctTotalOctets']);
    mysql_free_result($result2);
    
    return $Userdata['AcctTotalOctets'];
}

function getDBUserDataUsageTotal($username) // was database_get_user_datausage_total
{

    // Get Data usage (From all previous months as well) // TODO Add this current months usage too?
    $query = "SELECT
	          (
    	          SUM(mtotacct.InputOctets) + SUM(mtotacct.OutputOctets)
    	      )
    	      AS TotalOctets
    	      FROM mtotacct
    	      WHERE UserName = '$username'";
    $result2 = mysql_db_query('radius', $query) or die('Get User Data Usage (Total) Query failed: ' . mysql_error());
    
    if (mysql_num_rows($result2) < 1) 
    {
        $Userdata['TotalOctets'] = '0';
    }
    else
    {
        $Userdata['TotalOctets'] = mysql_result($result2, 0);
    }
    //$Userdata['TotalFormatOctets'] = formatBytes($Userdata['TotalOctets']);
    mysql_free_result($result2);
    
    return $Userdata['TotalOctets'];
}

function getDBMonthlyAccounts() // database_get_monthly_accounts
{
    $query = "SELECT UserName,
	                 AcctDate,
	                 InputOctets,
	                 OutputOctets
	          FROM mtotacct";
    $result2 = mysql_db_query('radius', $query) or die('Get Monthly Data Usage Query failed: ' . mysql_error());
    
    if (mysql_num_rows($result2) < 1) 
    {
        die('No data in mtotacct'); // TODO change this

        
    }
    else
    {
        
        while ($row = mysql_fetch_assoc($result2)) 
        {
            $acct_data['UserName'] = $row['UserName'];
            $acct_data['InputOctets'] = $row['InputOctets'];
            //$acct_data['InputFormatOctets'] = formatBytes($row['InputOctets']);
            $acct_data['OutputOctets'] = $row['OutputOctets'];
            //$acct_data['OutputFormatOctets'] = formatBytes($row['OutputOctets']);
            $acct_data['TotalOctets'] = $row['InputOctets'] + $row['OutputOctets']; // TODO move back to SQL as below

            //$acct_data['TotalFormatOctets'] = formatBytes($row['InputOctets'] + $row['OutputOctets']);
            $monthly_accounts[$row['AcctDate']][] = $acct_data;
        }
    }
    mysql_free_result($result2);
    krsort($monthly_accounts);
    
    return $monthly_accounts;

    //	$month[''] = $username, $download, $upload, $total; // Format and raw
    
}

function getDBMonthlyAccountsTotals() // was database_get_monthly_accounts_totals
{
    $query = "SELECT AcctDate,
	                 SUM(InputOctets) as TotalInputOctets,
	                 SUM(OutputOctets)as TotalOutputOctets,
	                 SUM(InputOctets + OutputOctets) as TotalOctets
	          FROM mtotacct
	          WHERE AcctDate=AcctDate
	          GROUP BY AcctDate";
    $result2 = mysql_db_query('radius', $query) or die('Get Monthly Data Usage Totals Query failed: ' . mysql_error());
    
    if (mysql_num_rows($result2) < 1) 
    {
        die('No data in mtotacct'); // TODO change this

        
    }
    else
    {
        
        while ($row = mysql_fetch_assoc($result2)) 
        {
            $acct_data['TotalInputOctets'] = $row['TotalInputOctets'];
            //$acct_data['TotalInputFormatOctets'] = formatBytes($row['TotalInputOctets']);
            $acct_data['TotalOutputOctets'] = $row['TotalOutputOctets'];
            //$acct_data['TotalOutputFormatOctets'] = formatBytes($row['TotalOutputOctets']);
            $acct_data['TotalOctets'] = $row['TotalOctets'];
            //$acct_data['TotalFormatOctets'] = formatBytes($row['TotalOctets']);
            $monthly_accounts_totals[$row['AcctDate']] = $acct_data;
        }
    }
    mysql_free_result($result2);
    krsort($monthly_accounts_totals);
    
    return $monthly_accounts_totals;
}

function checkDBUniqueUsername($username) // was database_check_uniq_username
{
    return DatabaseFunctions::getInstance()->checkUniqueUsername($username);
    $query = sprintf("SELECT Username
	                  FROM radcheck
	                  WHERE Username='%s'", mysql_real_escape_string($username));
    $result = mysql_db_query('radius', $query) or die('Checking Uniq Username Query failed: ' . mysql_error());
    $result_num = mysql_num_rows($result);
    mysql_free_result($result);
    
    if ($result_num != 0) 
    return false;
    
    return true;
}

function getDBUserGroup($Username) 
{

    // Get Users Group
    $query = "SELECT GroupName
	          FROM radusergroup
	          WHERE UserName = '${Username}'
	          ORDER BY priority
	          LIMIT 1";
    $result2 = mysql_db_query('radius', $query) or die('Get User Group Query failed: ' . mysql_error());
    
    if (mysql_num_rows($result2) == 0) 
    {
        $group = "";
    }
    else
    {
        $group = mysql_result($result2, 0);
    }
    mysql_free_result($result2);
    
    return $group;
}

function database_get_users($selectusers) 
{

    // For each User, get information

    foreach($selectusers as $user) 
    {
        //$users[] = getDBUserDetails($user['UserName']);
        $users[] = getDBUserDetails($user);
    }
    
    return $users;
}

function database_get_user_names() 
{

    // Get Users
    $query = "SELECT UserName
	          FROM radcheck
	          WHERE Attribute='Password'
	          ORDER BY id";
    $result = mysql_db_query('radius', $query) or die('Get Users Query failed: ' . mysql_error());

    // For each User, get information
    
    while ($user = mysql_fetch_array($result, MYSQL_ASSOC)) 
    {
        //unset($Userdata);
        //$Userdata['UserName'] = $user['UserName'];
        //$users[] = $Userdata;
        $users [] = $user['UserName'];
    }
    mysql_free_result($result);
    
    return $users;
}

/* Modify User Functions
* database_delete_user($username)
* database_increase_datalimit($username, $addmb)
* database_increase_timelimit($username, $addmins)
* database_change_password($username, $password)
* database_change_datalimit($username, $limitmb)
* database_change_timelimit($username, $limitmins)
* database_update_expirydate($username, $expirydate)
* database_change_group($username, $group)
* database_user_add_group($username, $group)
*
*/

function database_delete_user($username) 
{

    // Delete User
    $query = "DELETE from radcheck WHERE UserName='$username'";
    $result = mysql_db_query('radius', $query) or die("Deleting user $username failed: " . mysql_error());
    $query = "DELETE from radusergroup WHERE UserName='$username'";
    $result = mysql_db_query('radius', $query) or die("Deleting user(group) $username failed: " . mysql_error());
    
    return true;
}

function database_increase_datalimit($username, $addmb) 
{
    $datalimitoctets = $addmb * 1024 * 1024;
    $query = sprintf("UPDATE radcheck
	                  SET Value = Value + %s
	                  WHERE Username = '%s'
	                  AND Attribute='%s'", mysql_real_escape_string($datalimitoctets) , mysql_real_escape_string($username) , mysql_real_escape_string("Max-Octets"));
    mysql_db_query('radius', $query) or die('Increasing User Datalimit Query failed: ' . mysql_error());
}

function database_increase_timelimit($username, $addmins) 
{
    $timelimitsecs = $addmins * 60;
    $query = sprintf("UPDATE radcheck
	                  SET Value = Value + %s
	                  WHERE Username='%s'
	                  AND Attribute='%s'", mysql_real_escape_string($timelimitsecs) , mysql_real_escape_string($username) , mysql_real_escape_string("Max-All-Session"));
    mysql_db_query('radius', $query) or die('Increasing User Timelimit Query failed: ' . mysql_error());
}

function database_change_password($username, $password) 
{
    $query = sprintf("UPDATE radcheck
	                  SET Value='%s'
	                  WHERE Username='%s'
	                  AND Attribute='%s' ", mysql_real_escape_string($password) , $username, mysql_real_escape_string("Password"));
    return mysql_db_query('radius', $query) or die('Adding User Password Query failed: ' . mysql_error());
}

function database_change_datalimit($username, $limitmb) 
{
    $datalimitoctets = $limitmb * 1024 * 1024;
    $query = "SELECT UserName
	          FROM radcheck
	          WHERE Attribute = 'Max-Octets'
	          AND UserName = '$username'";
    $result2 = mysql_db_query('radius', $query) or die('Get User Expiration failed: ' . mysql_error());
    $num_rows = mysql_num_rows($result2);
    mysql_free_result($result2);
    
    if ($num_rows == 0) 
    { // Insert New

        $query = sprintf("INSERT into radcheck
		                  SET
		                  Username='%s',
		                  Attribute='%s',
		                  op='%s',
		                  Value='%s'", mysql_real_escape_string($username) , mysql_real_escape_string("Max-Octets") , mysql_real_escape_string(":=") , mysql_real_escape_string($datalimitoctets));
        mysql_db_query('radius', $query) or die('Adding User Datalimit Query failed: ' . mysql_error());
    }
    else
    { // Update Old

        $query = sprintf("UPDATE radcheck
		                  SET Value=%s
		                  WHERE Username='%s'
		                  AND Attribute='%s'", mysql_real_escape_string($datalimitoctets) , mysql_real_escape_string($username) , mysql_real_escape_string("Max-Octets"));
        mysql_db_query('radius', $query) or die('Setting User Datalimit Query failed: ' . mysql_error());
    }
}

function database_change_timelimit($username, $limitmins) 
{
    $limitsecs = $limitmins * 60;
    $query = "SELECT UserName FROM radcheck WHERE Attribute = 'Max-All-Session' AND UserName = '$username'";
    $result2 = mysql_db_query('radius', $query) or die('Get User Time Limit failed: ' . mysql_error());
    $num_rows = mysql_num_rows($result2);
    mysql_free_result($result2);
    
    if ($num_rows == 0) 
    { // Insert New

        $query = sprintf("INSERT into radcheck SET Username='%s', Attribute='%s', op='%s', Value='%s'", mysql_real_escape_string($username) , mysql_real_escape_string("Max-All-Session") , mysql_real_escape_string(":=") , mysql_real_escape_string($limitsecs));
        mysql_db_query('radius', $query) or die('Adding User Timelimit Query failed: ' . mysql_error());
    }
    else
    { // Update Old

        $query = sprintf("UPDATE radcheck SET Value=%s WHERE Username='%s' AND Attribute='%s'", mysql_real_escape_string($limitsecs) , mysql_real_escape_string($username) , mysql_real_escape_string("Max-All-Session"));
        mysql_db_query('radius', $query) or die('Setting User Timelimit Query failed: ' . mysql_error());
    }
}

function database_update_expirydate($username, $expirydate) 
{
    $query = "SELECT UserName FROM radcheck WHERE Attribute = 'Expiration' AND UserName = '$username'";
    $result2 = mysql_db_query('radius', $query) or die('Get User Expiration failed: ' . mysql_error());
    $num_rows = mysql_num_rows($result2);
    mysql_free_result($result2);
    
    if ($num_rows == 0) 
    { // Insert New

        
        if (trim($expirydate) && trim($expirydate) != '--') 
        {
            $query = sprintf("INSERT into radcheck SET Username='%s', Attribute='%s', op='%s', Value='%s'", mysql_real_escape_string($username) , mysql_real_escape_string("Expiration") , mysql_real_escape_string(":=") , mysql_real_escape_string(expiration_date_format($expirydate)));
            mysql_db_query('radius', $query) or die('Adding User Expiration Query failed: ' . mysql_error());
        }
    }
    else
    { // Update existing

        
        if (trim($expirydate) && trim($expirydate) != '--') 
        {
            $query = sprintf("UPDATE radcheck SET Value='%s' WHERE Username='%s' AND Attribute='%s'", mysql_real_escape_string(expiration_date_format($expirydate)) , mysql_real_escape_string($username) , mysql_real_escape_string("Expiration"));
            mysql_db_query('radius', $query) or die('Updating User Expiration Query failed: ' . mysql_error());
        }
        else
        {
            $query = sprintf("DELETE FROM radcheck WHERE Username='%s' AND Attribute='%s'", mysql_real_escape_string($username) , mysql_real_escape_string("Expiration"));
            mysql_db_query('radius', $query) or die('Deleting User Expiration Query failed: ' . mysql_error());
        }
    }
    
    return true;
}

function database_change_group($username, $group) 
{
    
    if (getDBUserGroup($username) == "") 
    {
        database_user_add_group($username, $group);
    }
    else
    {
        $query = sprintf("UPDATE radusergroup SET GroupName='%s' WHERE Username='%s' AND priority = '1'", mysql_real_escape_string($group) , $username);
        mysql_db_query('radius', $query) or die('Changing User Group Query failed: ' . mysql_error());
    }
    database_update_expirydate($username, expiry_for_group(getDBUserGroup($username)));
}

function database_user_add_group($username, $group) 
{
    $query = sprintf("INSERT into radusergroup SET UserName='%s', GroupName='%s', Priority='%s'", mysql_real_escape_string($username) , mysql_real_escape_string($group) , mysql_real_escape_string('1'));
    mysql_db_query('radius', $query) or die('Adding User Group Query failed: ' . mysql_error());
}

/* New User Function
* database_create_new_user($username, $password, $datalimitmb, $timelimitmins, $expirydate, $group, $comment)
*
*/

function database_create_new_user($username, $password, $datalimitmb, $timelimitmins, $expirydate, $group, $comment) // TODO Comment field


{
    $query = sprintf("INSERT into radcheck SET Username='%s', Attribute='%s', op='%s', Value='%s'", $username, mysql_real_escape_string("Password") , mysql_real_escape_string(":=") , mysql_real_escape_string($password));
    mysql_db_query('radius', $query) or die('Adding User Password Query failed: ' . mysql_error());
    
    if ($group) database_change_group($username, $group);
    
    if ($datalimitmb) database_change_datalimit($username, $datalimitmb);
    
    if ($timelimitmins) database_change_timelimit($username, $timelimitmins);
    
    if (trim($expirydate) && trim($expirydate) != '--') database_update_expirydate($username, $expirydate);
    
    if (trim($comment)) database_change_comment($username, $comment); // TODO Comment field

    
    return true;
}

function database_change_comment($username, $comment)
{

    if (getDBComment($username) == "") 
    {
        database_user_add_comment($username, $comment);
    }
    else
    {
        database_user_change_comment($username, $comment);
    }
}

function getDBComment($username)
{
    $query = "SELECT Value FROM radreply WHERE Attribute = 'Comment' AND UserName = '$username'";
    $result2 = mysql_db_query('radius', $query) or die('Get User Comment failed: ' . mysql_error());
    $num_rows = mysql_num_rows($result2);
    if($num_rows == 0)
    {
        // No Results
        $comment = "";
    }
    else
    {
        $comment = mysql_result($result2, 0);
    }
    mysql_free_result($result2);
    return $comment;
}

function database_user_add_comment($username, $comment)
{
    $query = sprintf("INSERT into radreply SET Username='%s', Attribute='%s', op='%s', Value='%s'", $username, mysql_real_escape_string("Comment") , mysql_real_escape_string(":=") , mysql_real_escape_string($comment));
    mysql_db_query('radius', $query) or die('Adding User CommentQuery failed: ' . mysql_error());    
}

function database_user_change_comment($username, $comment)
{
    $query = sprintf("UPDATE radreply SET Value='%s' WHERE Username='%s' AND Attribute='%s'", mysql_real_escape_string($comment) , mysql_real_escape_string($username) , mysql_real_escape_string("Comment"));
    mysql_db_query('radius', $query) or die('Updating User Comment Query failed: ' . mysql_error());
}



// Closing connection
//mysql_close($link);


?>
