<?php

/* Copyright 2009 Timothy White */

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

    // Current month up and down
    list($data1, $labels, $assoc1) = $Reports->getThisMonthDownUsageReport();
//    $smarty->assign('thismonthdowndata', json_encode($data1));
    list($data2, $labels, $assoc2) = $Reports->getThisMonthUpUsageReport();
//    $smarty->assign('thismonthupdata', json_encode($data2));
    $smarty->assign('thismonthseries', json_encode(array($assoc1, $assoc2)));
//    $smarty->assign('thismonthticks', json_encode($labels));
    
    // Previous months total usage
    list($data, $labels, $assoc) = $Reports->getPreviousMonthsUsageReport();
    $smarty->assign('previousmonthsseries', json_encode(array($assoc)));
    //$smarty->assign('previousmonthsticks', json_encode($labels));    
    
    // Current month by users
    list($data1, $data2, $labels) = $Reports->getThisMonthUsersUsageReport();
    //$smarty->assign('thismonthusersdata', json_encode($data1));
    //$smarty->assign('thismonthusersquota', json_encode($data2));
    $smarty->assign('thismonthusersseries', json_encode(array($data1, $data2)));
    //$smarty->assign('thismonthuserslabels', json_encode($labels));
    
    
    // Current month group usage
    $smarty->assign('thismonthgroupdata', json_encode($Reports->getMonthGroupUsage()));
    



	display_page('reports.tpl');

//}

?>

