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

/*ini_set('include_path', 
  ini_get('include_path') . PATH_SEPARATOR . '../ofc2/php5-ofc-library/lib/');
  
  require_once('../ofc2/php5-ofc-library/lib/OFC/OFC_Chart.php');*/
$PAGE = 'reports';
require_once 'includes/pageaccess.inc.php';

 
require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';


/* No longer using OFC
if(isset($_GET['chart']))
{
    $Reports = new Reports(DatabaseConnections::getInstance());
    switch($_GET['chart'])
    {
        case "months_usage":
            echo $Reports->getMonthsUsageReport();
            break;
        case "current_month_users_usage":
            echo $Reports->getThisMonthUsersUsageReport();
            break;
        case "current_month_usage":
            echo $Reports->getThisMonthUsageReport();
            break;
        case "previous_months_usage":
            echo $Reports->getPreviousMonthsUsageReport();
            break;
        case "daily_sessions":
            echo $Reports->getDailySessionsReport();
            break;              
        case "daily_users":
            echo $Reports->getDailyUsersReport();
            break;            
    }
}
else
{ */
//    $smarty->assign('chart1', $chart->toPrettyString());
$Reports = new Reports(DatabaseConnections::getInstance());

    $smarty->assign('monthsavailableaccounting', DatabaseFunctions::getInstance()->getMonthsAccountingDataAvailableFor());

    // Current month up and down
    list($data1, $labels, $assoc1) = $Reports->getThisMonthDownUsageReport();
    list($data2, $labels, $assoc2) = $Reports->getThisMonthUpUsageReport();
    $smarty->assign('thismonthseries', json_encode(array($assoc1, $assoc2)));
    $thismonthupdown[] = array('Day', 'Downloads', 'Uploads');
    foreach($labels as $id => $label)
    {
        $thismonthupdown[] = array($label, $data1[$id], $data2[$id]);
    }
    $smarty->assign('thismonthupdownarray', json_encode($thismonthupdown));
    
    
    // All months users usage
    $smarty->assign('userusagebymontharray', json_encode($Reports->getUsersUsageByMonth()));
    
    // Previous months total usage
    list($data, $labels, $assoc) = $Reports->getPreviousMonthsUsageReport();
    $smarty->assign('previousmonthsseries', json_encode(array($assoc)));
    
    // Users usage - Current Month
    list($data1, $data2, $labels, $month) = $Reports->getUsersUsageMonthReport($_GET['UsersUsageMonth']); //TODO: Sanatise input?
    $smarty->assign('usersusagemonth', $month[0]);
    $smarty->assign('usersusageprettymonth', $month[1]);    
    $smarty->assign('userdatausagemonthseries', json_encode(array($data1)));
    $smarty->assign('usertimeusagemonthseries', json_encode(array($data2)));    
    
    // Users usage - By Month
    //list($data1, $data2, $labels) = $Reports->getThisMonthUsersUsageReport();
    //$smarty->assign('thismonthusersseries', json_encode(array($data1, $data2)));
    
    
    // Current month group usage
    $smarty->assign('thismonthgroupdata', json_encode($Reports->getMonthGroupUsage()));
    



	display_page('reports.tpl');

//}

?>

