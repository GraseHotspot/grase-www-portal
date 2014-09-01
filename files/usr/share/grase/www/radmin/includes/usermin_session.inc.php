<?php

/* Copyright 2008 Timothy White */

/*  This file is part of GRASE Hotspot.

    http://grasehotspot.org/

    GRASE Hotspot is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    GRASE Hotspot is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GRASE Hotspot.  If not, see <http://www.gnu.org/licenses/>.
*/

//error_reporting(E_ALL|E_STRICT); // REMOVE FOR RELEASE (NOT IN USE DUE TO PEAR MODULES BRING UP LOTS OF ERROS
require_once "Auth.php";
require_once "MDB2.php";

function grase_autoload($class_name) {
    if( file_exists(__DIR__. '/../classes/' . $class_name . '.class.php'))
    {
        include_once __DIR__. '/../classes/' . $class_name . '.class.php';
    }
}

spl_autoload_register('grase_autoload');


require_once 'load_settings.inc.php';
require_once 'usermin_page_functions.inc.php';


   $mtime = microtime();
   $mtime = explode(" ",$mtime);
   $mtime = $mtime[1] + $mtime[0];
   $pagestarttime = $mtime;



function loginForm($username = null, $status = null, &$auth = null)
{
    global $templateEngine;

    // TODO make it so the clearAssign lines aren't needed
	$templateEngine->clearAssign('MenuItems');
	$templateEngine->clearAssign("LoggedInUsername");
    $templateEngine->assign('username', $username);
    if(isset($_GET['user'])) $templateEngine->assign('username', $_GET['user']);
    
    switch($status)
    {
        case 0:
            break;
        case -1:
        case -2:
            $error = "Your session has expired. Please login again";
            break; 
        case -3:
            $error = "Incorrect Login.";
            break;
        case -5:
            $errro = "Security Issue. Please login again";
            break;
        default:
            $error = "Authentication Issue. Please report to Admin";
    }
    if(isset($error)) $templateEngine->assign("error", $error);
   	$templateEngine->displayPage('usermin_login.tpl');
   	exit();
}


$Usermin = new DatabaseUsermin($DBs->getRadiusDB());


$options = array(
    'cryptType' => 'none',
    'users' => $Usermin->getUsers()
    );
    
$Auth = new Auth("Array", $options, "loginForm");
$Auth->setSessionName("GRASE Usermin");
$Auth->setAdvancedSecurity(array(
    AUTH_ADV_USERAGENT => true,
    AUTH_ADV_IPCHECK   => true,
    AUTH_ADV_CHALLENGE => false
));
$Auth->setIdle(120);
$Auth->start();
    
if (!$Auth->checkAuth())
{
    echo "Should never get here"; // THIS CODE SHOULD NEVER RUN
    exit();
}elseif(isset($_GET['logoff']))
{
    $Auth->logout();
    $Auth->start();
}else
{
    $templateEngine->assign("LoggedInUsername", $Auth->getUsername());
}


?>
