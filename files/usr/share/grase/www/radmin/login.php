<?
$from_page='login';
require_once 'includes/session.inc.php';
AdminLog::getInstance()->log("Log in");
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
if(isset($_GET['page'])) $uri  = $_GET['page']; // Sanity check
header("Location: http://$host$uri");
exit;
?>
