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

require_once 'misc_functions.inc.php';
require_once 'load_settings.inc.php';

// MAJOR TODO: Migrate all this to new DB stuff

// Connecting, selecting database
/*$settings = file($CONFIG['radius_database_config_file']);

foreach($settings as $setting) 
{
    list($key, $value) = split(":", $setting);
    $db_settings[$key] = trim($value);
}
$dblink = mysql_pconnect($db_settings['sql_server'], $db_settings['sql_username'], $db_settings['sql_password']) or die('Could not connect: ' . mysql_error());
mysql_select_db($db_settings['sql_database']) or die('Could not select database');
*/


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

/* OBSOLETE ?
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
}*/

/* USER DETAILS
*
*
*/

function getDBUserDetails($username) // was database_get_user_details
{
    return DatabaseFunctions::getInstance()->getUserDetails($username);

}

/* Not in use yet. TODO: move some of above into these functions and make them active */

function getDBUserLastLogoutTime($username) 
{

    return DatabaseFunctions::getInstance()->getUserLastLogoutTime($username);

}

function getDBUserTotalSessionTime($username) 
{
    return DatabaseFunctions::getInstance()->getUserTotalSessionTime($username);

}

function getDBUserDataUsage($username) 
{
    return DatabaseFunctions::getInstance()->getUserDataUsage($username);


}

function getDBUserDataUsageTotal($username) // was database_get_user_datausage_total
{
        return DatabaseFunctions::getInstance()->getUserDataUsageTotal($username);

}

function getDBMonthlyAccounts() // database_get_monthly_accounts
{

    return DatabaseFunctions::getInstance()->getMonthlyAccounts();

    
}

function getDBMonthlyAccountsTotals() // was database_get_monthly_accounts_totals
{
    return DatabaseFunctions::getInstance()->getMonthlyAccountsTotals();

}

function checkDBUniqueUsername($username) // was database_check_uniq_username
{
    return DatabaseFunctions::getInstance()->checkUniqueUsername($username);

}

function getDBUserGroup($username) 
{
    return DatabaseFunctions::getInstance()->getUserGroup($username);

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
    return DatabaseFunctions::getInstance()->getAllUserNames();

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
    return DatabaseFunctions::getInstance()->deleteUser($username);

}

function database_increase_datalimit($username, $addmb) 
{
    return DatabaseFunctions::getInstance()->increaseUserDatalimit($username, $addmb);

}

function database_increase_timelimit($username, $addmins) 
{
    return DatabaseFunctions::getInstance()->increaseUserTimelimit($username, $addmins);

}

function database_change_password($username, $password) 
{
    return DatabaseFunctions::getInstance()->setUserPassword($username, $password);

}

function database_change_datalimit($username, $limitmb) 
{
    return DatabaseFunctions::getInstance()->setUserDataLimit($username, $limitmb);

}

function database_change_timelimit($username, $limitmins) 
{
    return DatabaseFunctions::getInstance()->setUserTimeLimit($username, $limitmins);

}

function database_update_expirydate($username, $expirydate) 
{
    return DatabaseFunctions::getInstance()->setUserExpiry($username, $expirydate);

}

function database_change_group($username, $group) 
{
    return DatabaseFunctions::getInstance()->setUserGroup($username, $group);
    

}

function database_create_new_user($username, $password, $datalimitmb, $timelimitmins, $expirydate, $group, $comment) // TODO: Comment field
{       
    return DatabaseFunctions::getInstance()->createUser($username, $password, $datalimitmb, $timelimitmins, $expirydate, $group, $comment);
}

function database_change_comment($username, $comment)
{
    return DatabaseFunctions::getInstance()->setUserComment($username, $comment);
}

function getDBComment($username)
{
    return DatabaseFunctions::getInstance()->getUserComment($username);
}

?>
