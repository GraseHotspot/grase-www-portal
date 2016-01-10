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
$PAGE = 'users';
require_once 'includes/pageaccess.inc.php';

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';

DatabaseFunctions::getInstance()->loadAllUserDetails();

$users = DatabaseFunctions::getInstance()->getMultipleUsersDetails(DatabaseFunctions::getInstance()->getAllUserNames());
$users_groups = sort_users_into_groups($users); // TODO: Reports and then no longer sort user list by downloads??
$users_groups['All'] = $users; // TODO: Group names can't have space in name TODO: Translate all?

$templateEngine->assign("groupdata", DatabaseFunctions::getInstance()->getGroupAttributes());
$templateEngine->assign("users", $users);
$templateEngine->assign("users_groups", $users_groups);

$templateEngine->displayPage('display.tpl');
