<?php

/* Copyright 2009 Timothy White */

require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';

	$monthly_accounts = getDBMonthlyAccounts();
	$monthly_accounts_totals = getDBMonthlyAccountsTotals();
	$smarty->assign("monthly_accounts", $monthly_accounts);
	$smarty->assign("monthly_accounts_totals", $monthly_accounts_totals);
	$smarty->display('monthly_accounts.tpl');

// TODO Data usage over "forever"
	

?>
