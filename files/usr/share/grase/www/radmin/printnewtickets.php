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
$PAGE = 'users';
require_once 'includes/pageaccess.inc.php';
require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
if (isset($_GET['user'])) {
    $users = DatabaseFunctions::getInstance()->getMultipleUsersDetails(array(\Grase\Clean::text($_GET['user'])));
    if (!is_array($users)) {
        $users = array();
    }
    $title = \Grase\Clean::text($_GET['user']) . ' Voucher';
} elseif (isset($_GET['batch'])) {
    $batches = explode(',', $_GET['batch']);

    $users = array();
    foreach ($batches as $batch) {
        $batch = clean_number($batch);
        $fetchUsers = DatabaseFunctions::getInstance()->getMultipleUsersDetails($Settings->getBatch($batch));
        if (!is_array($fetchUsers)) {
            $fetchUsers = array();
        }
        $users = array_merge($users, $fetchUsers);
    }

    // TODO: replace , with _ in below
    $title = sprintf(T_('Batch %s Vouchers'), implode('-', $batches));
} else {
    $batch = $Settings->getSetting('lastbatch');
    $users = DatabaseFunctions::getInstance()->getMultipleUsersDetails($Settings->getBatch($batch));
    if (!is_array($users)) {
        $users = array();
    }
    $title = sprintf(T_('Batch %s Vouchers'), $batch);
}

$users_groups = sort_users_into_groups($users);
$templateEngine->assign("batchTitle", $title);
$templateEngine->assign("users", $users);
$templateEngine->assign("users_groups", $users_groups);
$templateEngine->assign("groupsettings", grouplist());
$templateEngine->assign("networksettings", $networksettings = unserialize($Settings->getSetting('networkoptions')));
$templateEngine->displayPage('printnewtickets.tpl');
