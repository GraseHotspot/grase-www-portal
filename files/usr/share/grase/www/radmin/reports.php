<?php

/* Copyright 2009 Timothy White */

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

$PAGE = 'reports';
require_once 'includes/pageaccess.inc.php';
require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';

$Reports = new Reports(new DatabaseConnections());

$templateEngine->assign(
    'monthsavailableaccounting',
    DatabaseFunctions::getInstance()->getMonthsAccountingDataAvailableFor()
);

// Current month up and down
list($data1, $labels, $assoc1) = $Reports->getThisMonthDownUsageReport();
list($data2, $labels, $assoc2) = $Reports->getThisMonthUpUsageReport();
$templateEngine->assign('thismonthseries', json_encode(array($assoc1, $assoc2)));
$thisMonthUpDown[] = array('Day', 'Downloads', 'Uploads');
foreach ($labels as $id => $label) {
    $thisMonthUpDown[] = array($label, $data1[$id], $data2[$id]);
}
$templateEngine->assign('thismonthupdownarray', json_encode($thisMonthUpDown));


// All months users usage
$templateEngine->assign('userusagebymontharray', json_encode($Reports->getUsersUsageByMonth()));

// Previous months total usage
list($data, $labels, $assoc) = $Reports->getPreviousMonthsUsageReport();
$templateEngine->assign('previousmonthsseries', json_encode(array($assoc)));

// Users usage - Current Month
list($data1, $data2, $labels, $month) = $Reports->getUsersUsageMonthReport(
    $_GET['UsersUsageMonth']
); //TODO: Sanatise input?
$templateEngine->assign('usersusagemonth', $month[0]);
$templateEngine->assign('usersusageprettymonth', $month[1]);
$templateEngine->assign('userdatausagemonthseries', json_encode(array($data1)));
$templateEngine->assign('usertimeusagemonthseries', json_encode(array($data2)));

// Current month group usage
$templateEngine->assign('thismonthgroupdata', json_encode($Reports->getMonthGroupUsage()));
$templateEngine->displayPage('reports.tpl');
