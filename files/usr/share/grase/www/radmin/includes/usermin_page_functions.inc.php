<?php

/* Copyright 2008 Timothy White */

require_once 'includes/page_functions.inc.php';

function usermin_createmenuitems()
{
	//	$menubar['id'] = array("href" => , "label" => );
	$menubar['user'] = array("href" => "?user", "label" => "My Details");
	$menubar['history'] = array("href" => "?history", "label" => "My History");
	$menubar['logout'] = array("href" => "?logoff", "label" => "Logoff" );
	return $menubar;
}

$smarty->assign("Application", USERMIN_APPLICATION_NAME);

$smarty->assign("Title", $location . " - " . USERMIN_APPLICATION_NAME);

// Setup Menus
$smarty->assign("MenuItems", usermin_createmenuitems());
isset($_SESSION['username']) && $smarty->assign("LoggedInUsername", $_SESSION['username']);


?>
