<?php
header("Content-Type: text/javascript;");

if($_GET['username']){
require_once('../radmin/includes/database_functions.inc.php');

$user=getDBUserDetails(mysql_real_escape_string($_GET['username'])); //TODO sanitize INPUT
?>
chilliJSON.reply({"version":"1.0","clientState":1,"user_details":{"monthlyusagelimit":"<? echo $user['Max-Octets'];?>","monthlytimelimit":"<? echo $user['Max-All-Session'];?>"}})
<?}else{?>
chilliJSON.reply({"version":"1.0","clientState":0})
<?}?>
