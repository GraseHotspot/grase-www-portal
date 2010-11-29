<?

/* Copyright 2009 Timothy White */

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';


	$smarty->assign("loglines", AdminLog::getInstance()->getLog());
	display_page('adminlog.tpl');

?>

