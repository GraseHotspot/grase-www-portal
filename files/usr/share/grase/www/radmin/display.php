<?php
require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';

	$users = database_get_users(database_get_user_names());
	$users_groups = sort_users_into_groups($users); // TODO Reports and then no longer sort user list by downloads??
	$smarty->assign("users", $users);
	$smarty->assign("users_groups", $users_groups);
	$smarty->register_modifier( "sortby", "smarty_modifier_sortby" );   
	display_page('display.tpl');
?>
