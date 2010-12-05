<?php

/* Copyright 2008 Timothy White */

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';

    if(isset($_GET['username']))
    {
	    $smarty->assign("sessions", getDBSessionsAccounting($_GET['username']));
	    $smarty->assign("username", $_GET['username']);
	}
	else
	{
        $smarty->assign("sessions", getDBSessionsAccounting());
    }

	display_page('sessions.tpl');

// TODO: Data usage over "forever"
	

?>
