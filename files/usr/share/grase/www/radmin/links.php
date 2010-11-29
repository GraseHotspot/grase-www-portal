<?php

/* Copyright 2008 Timothy White */

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';

	$smarty->assign("links", createusefullinks());
	display_page('links.tpl');

?>


