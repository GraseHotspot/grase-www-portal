<?php

/* Copyright 2008 Timothy White */

/*  This file is part of GRASE Hotspot.

    http://hotspot.purewhite.id.au/

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
$PAGE = 'sessions';
require_once 'includes/pageaccess.inc.php';


require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';

    if(isset($_GET['username']))
    {
	    $smarty->assign("sessions", getDBSessionsAccounting($_GET['username']));
	    $smarty->assign("username", $_GET['username']);
	}
	elseif(isset($_GET['allsessions']))
	{
	    $sessions = getDBSessionsAccounting();
	    $totalrows = sizeof($sessions);
	    $numPerPage = $_GET['items'] ? abs($_GET['items']) : 25; // TODO check this is safe
	    $page = $_GET['page'] ? abs($_GET['page']) : 0; //TODO check this is safe
	    
	    $pages = floor($totalrows/$numPerPage);
	    if($page > $pages) $page = $pages;
	    $currentstartitem = $page * $numPerPage;
	    
	    $displaysessions = array_slice($sessions, $currentstartitem, $numPerPage, TRUE );
    	$smarty->assign("sessions", $displaysessions);        
    	
    	$smarty->assign("pages", $pages);
    	$smarty->assign("perpage", $numPerPage);
    	$smarty->assign("currentpage", $page);
    }else{
        $smarty->assign("activesessions", DatabaseFunctions::getInstance()->getActiveRadiusSessionsDetails());
    }

	display_page('sessions.tpl');

// TODO: Data usage over "forever"
	

?>
