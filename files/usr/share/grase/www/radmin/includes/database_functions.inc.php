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


/* USER DETAILS
*
*
*/

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
