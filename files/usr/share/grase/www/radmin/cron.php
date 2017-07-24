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
$NONINTERACTIVE_SCRIPT = true;

require_once __DIR__ . '/../../vendor/autoload.php';

require_once('includes/misc_functions.inc.php');

// Special case for stale sessions, don't log it
/*if(isset($_GET['clearstalesessions']))
{
    CronFunctions::getInstance()->clearStaleSessions();
    exit;
}*/

AdminLog::getInstance()->log_cron("CRON");

$DBs = new DatabaseConnections();
$radiusDB = new \Grase\Database\Database();
$radminDB = new \Grase\Database\Database('/etc/grase/radmin.conf');
$radmin = new \Grase\Database\Radmin($radminDB);
$upgradeDB = new \Grase\Database\Upgrade($radiusDB, $radminDB, $radmin, CronFunctions::getInstance());
$upgradeDatabaseResults = $upgradeDB->upgradeDatabase();
if ($upgradeDatabaseResults) {
    echo "$upgradeDatabaseResults\n";
}

$staleSessionsResult = CronFunctions::getInstance()->clearStaleSessions();
if ($staleSessionsResult) {
    echo "$staleSessionsResult\n";
}

$expiredUsersResults = CronFunctions::getInstance()->deleteExpiredUsers();
if ($expiredUsersResults) {
    echo "$expiredUsersResults\n";
}

$condensePreviousMonthsResults = CronFunctions::getInstance()->condensePreviousMonthsAccounting();
if ($condensePreviousMonthsResults) {
    echo "$condensePreviousMonthsResults\n";
}
$clearOldPostDataResults = CronFunctions::getInstance()->clearOldPostAuth();
if ($clearOldPostDataResults) {
    echo "$clearOldPostDataResults\n";
}

$clearPostAuthMACRejectResults = CronFunctions::getInstance()->clearPostAuthMacRejects();
if ($clearPostAuthMACRejectResults) {
    echo "$clearPostAuthMACRejectResults\n";
}


if (isset($_GET['deleteoutoftimeusers']) && $_GET['deleteoutoftimeusers']) {
    $deleteOutOfTimeUsersResults = CronFunctions::getInstance()->deleteOutOfTimeUsers();
    if ($deleteOutOfTimeUsersResults) {
        echo "$deleteOutOfTimeUsersResults\n";
    }
}

if (isset($_GET['deleteoutofdatausers']) && $_GET['deleteoutofdatausers']) {
    $deleteOutOfDataUsersResults = CronFunctions::getInstance()->deleteOutOfDataUsers();
    if ($deleteOutOfDataUsersResults) {
        echo "$deleteOutOfDataUsersResults\n";
    }
}

if (isset($_GET['deleteoldfreeusers']) && $_GET['deleteoldfreeusers']) {
    $deleteOldFreeUsersResults = CronFunctions::getInstance()->deleteOldFreeUsers($radmin->getSetting('autocreategroup'));
    if ($deleteOldFreeUsersResults) {
        echo "$deleteOldFreeUsersResults\n";
    }
}

$clearOldBatchesResults = CronFunctions::getInstance()->clearOldBatches();
if ($clearOldBatchesResults) {
    echo "$clearOldBatchesResults\n";
}

echo CronFunctions::getInstance()->activateExpireAfterLogin();
