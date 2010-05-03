<?

ini_set('include_path', 
  ini_get('include_path') . PATH_SEPARATOR . '../ofc2/php5-ofc-library/lib/');
  
  require_once('../ofc2/php5-ofc-library/lib/OFC/OFC_Chart.php');

 
require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';


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
{
//    $smarty->assign('chart1', $chart->toPrettyString());

	display_page('reports.tpl');

}

?>

