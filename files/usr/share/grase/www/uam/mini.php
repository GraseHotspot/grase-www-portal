<?php

/* Copyright 2008 Timothy White */

require_once('includes/site.inc.php');


//
$loginurl = parse_url($_GET['loginurl']);
$query = $loginurl['query'];
parse_str($query, $uamopts);

if(isset($_GET['loginurl'])){
	$loginlink = "http://$lanIP/uam/mini?$query";
	$loginlink2 = $_GET['loginurl'];
}else{
	$loginlink = "http://$lanIP/uam/mini";
	$loginlink2 = "http://$lanIP:3990/prelogin";
}

/*
 * We need uamip and uamport to pass to jqchilli.js
 */
$uamIP = (empty($_GET['uamip'])) ? $lanIP : $_GET['uamip'];
$uamPort = (empty($_GET['uamport'])) ? 3990 : $_GET['uamport'];
$smarty->assign('uamquery', [
		'uamip' => $uamIP,
		'uamport' => $uamPort,
]);

$smarty->assign("user_url", $uamopts['userurl']);

$smarty->assign("loginlink", $loginlink);
$smarty->assign("loginlink2", $loginlink2);

$smarty->display('mini.tpl');

