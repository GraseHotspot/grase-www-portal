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
require_once('php-gettext/gettext.inc');

require_once('includes/accesscheck.inc.php');
require_once 'includes/database_functions.inc.php';
require_once 'includes/load_settings.inc.php';
require_once 'includes/pageaccess.inc.php';


require_once __DIR__.'/../../../vendor/autoload.php';

require_once 'smarty_sortby.php';
require_once("smarty3/SmartyBC.class.php");


function css_file_version()
{
	$resourcefiles = array(
	    '/usr/share/grase/www/hotspot.css',
	    '/usr/share/grase/www/radmin/radmin.css',
	    '/usr/share/grase/www/js/grase.js',
	    '/usr/share/grase/www/radmin/js/radmin.js'	    
	    );
	foreach($resourcefiles as $file)
	{
	    $fileversions[basename($file)] = date("YmdHis",filemtime($file));	
	}
	return array($fileversions, APPLICATION_VERSION);
}

function createmenuitems()
{
    global $PAGESACCESS;
	//	$menubar['id'] = array("href" => , "label" => );
	$menubar['main'] = array("href" => "./", "label" => T_("Status"));
	$menubar['users'] = array("href" => "display", "label" => T_("Users"),
	    "submenu" => array(
	        'createuser' => array("href" => "newuser", "label" => T_("New User")),
	        'createtickets' => array("href" => "newtickets", "label" => T_("Batch Users")),
	        'createmachine' => array("href" => "newmachine", "label" => T_("Computer Account"))	
	        )
    	);
	$menubar['sessions'] = array("href" => "sessions", "label" => T_("Monitor Sessions"),
	    "submenu" => array(
            'reports' => array("href" => "reports", "label" => T_("Reports")),	    
            //'monthly_accounts' => array("href" => "datausage", "label" => "Monthly Reports"); // Not working atm TODO:
            
	    )
	    
	    );
    
	$menubar['settings'] = array("href" => "settings", "label" => T_("Settings"),
	    "submenu" => array(
	        'uploadlogo' => array("href" => "uploadlogo", "label" => T_("Site Logo") ),
	        'netconfig' => array("href" => "netconfig", "label" => T_("Network Settings") ),
            'chilliconfig' => array("href" => "chilliconfig", "label" => T_("Coova Chilli Settings") ),
            'loginconfig' => array("href" => "loginconfig", "label" => T_("Portal Customisation") ),
            'groups' => array("href" => "groupconfig", "label" => T_("Groups") ),	
            //'vouchers' => array("href" => "voucherconfig", "label" => T_("Vouchers") ), // DISABLED FOR RELEASE AS NOT YET READY FOR PRODUCTION

        )
            
	        		
	 );

// TODO: Bring links page back when sysstatus is fixed and more links are active	
//	$menubar['links'] = array("href" => "links", "label" => T_("Useful Links"));	
	$menubar['passwd'] = array("href" => "passwd", "label" => T_("Admin Users"),
	    "submenu" => array(
    	    'adminlog' => array("href" => "adminlog", "label" => T_("Admin Log") ),
	    )
	
	 );

	
	$menubar['logout'] = array("href" => "./?logoff", "label" => T_("Logoff") );
	
    // Filter out menu items user doesn't have access to
	$newmenubar = array();
	foreach($menubar as $label => $toplevel)
	{

        // If they don't have access to top level of a menu section, they also don't have access to the levels below it via the menu (still up to the PAGESACCESS to prevent access
	    if(check_level($PAGESACCESS[$label]))
	    {
	      $submenu = array();
	      
	      if(isset($toplevel['submenu']) && is_array($toplevel['submenu']))
	      {
		foreach($toplevel['submenu'] as $secondlabel => $secondlevel)
		{
			if(check_level($PAGESACCESS[$secondlabel]))
			    $submenu[$secondlabel] = $secondlevel;
		}
	      }
	      $item = $toplevel;
	      unset($item['submenu']);
	      if(sizeof($submenu))
		  $item['submenu'] = $submenu;	    	
	      $newmenubar[$label] = $item;
	    }
	}
	return $newmenubar;
}

function createusefullinks()
{
	$links['sysstatus'] = array("href" => "/grase/radmin/sysstatus", "label" => T_("System Status"));
	return $links;
}

// TODO rename datacosts to better reflect that it just has inherit added to datavals
function datacosts()
{
	global $datacosts;//, $pricemb, $currency;
	$datacosts['inherit'] = T_('Inherit from group');
	$datacosts = $datacosts + datavals();
	return $datacosts;
}

function datavals()
{
        global $mb_options;
	//$disp_currency = $CurrencySymbols[$currency];
	$datavals[''] = '';
	$mboptions = explode(" ", $mb_options);
	foreach($mboptions as $mb)
	{
		$datavals["$mb"] = Formatting::formatBytes($mb*1024*1024);;
	}
	return $datavals;
}

function timevals()
{
        global $time_options;
	$timevals[''] = '';
	$timeoptions = explode(" ", $time_options);
	foreach($timeoptions as $time)
	{
	    if($time >= 60)
	    {
		    $timevals["$time"] = $time/60 . " hours";	    
	    }
	    else
	    {
		    $timevals["$time"] = "$time mins";
		}
	}
	return $timevals;
}

function timecosts()
{
	global $timecosts;//, $pricetime, $currency, $time_options;
	$timecosts['inherit'] = T_('Inherit from group');
	$timecosts = $timecosts + timevals();
		
	return $timecosts;
}

function bandwidth_options()
{
    global $kbit_options;
    // kbits/second
    $kbits_options = explode(" ", $kbit_options);
    $options[''] = '';
    foreach($kbits_options as $kbits)
    {
        $bits = $kbits * 1024;
        $kbytes = $kbits/8;
        $mbmin = round($kbytes * 60 / 1024, 2);
        $label = Formatting::formatBits($bits) ." ($kbytes kbytes/sec, $mbmin MiB/min)";
        $options["$kbits"] = $label;
    }
    
    return $options;

}

function grouplist()
{
    // Makes an array of groupname=>groupname for html drop downs
    global $Settings;
    $groups = array();
    foreach($Settings->getGroup() as $groupname => $group)
    {
        $groups[$groupname] = $group['GroupLabel'];
    }
    return $groups;
}

function recurtimes()
{
    global $Recurtimes;
    // TODO: Dynamic this? This is for demo
    $Recurtimes = array(
        '' => '',
        'hour' => T_('Hour'),
        'day' => T_('Day'),
        'week' => T_('Week'),
        'month' => T_('Month'));
    return $Recurtimes;
}

function yesno()
{
    return array('yes' => T_('Yes'), 'no' => T_('No'));
}

function display_page($template)
{
	global $smarty;
	assign_vars();
	return $smarty->display($template);
}


require_once 'locale.inc.php';

$smarty = new SmartyBC();

//$smarty->error_reporting = E_ALL & ~E_NOTICE;
$smarty->compile_check = true;
//$smarty->register_outputfilter('smarty_outputfilter_strip');
//$smarty->registerPlugin('modifier', 'bytes', array("Formatting", "formatBytes"));
$smarty->register_modifier('bytes', array("Formatting", "formatBytes"));
$smarty->register_modifier('seconds', array("Formatting", "formatSec"));
$smarty->register_modifier('displayLocales', 'displayLocales');
$smarty->register_modifier('displayMoneyLocales', 'displayMoneyLocales');
$smarty->register_function('inputtype', 'input_type');

// i18n
//$locale = (!isset($_GET["l"]))?"en_GB":$_GET["l"];  
$smarty->register_block('t', 'smarty_block_t');

apply_locale($locale);

$smarty->assign("RealHostname", $realhostname);

// Initialise error variables 
	$errormessages = array();
	$successmessages = array();
	$warningmessages = array();


function assign_vars()
{
	global $smarty, $sellable_data, $useable_data, $used_data, $sold_data;
	global $location, $website_name, $website_link, $DEMO_SITE, $Settings;
	
	list($fileversions, $application_version)=css_file_version();
	$smarty->assign("radmincssversion", $fileversions['radmin.css']);
	$smarty->assign("hotspotcssversion", $fileversions['hotspot.css']);
	$smarty->assign("grasejsversion", $fileversions['grase.js']);
	$smarty->assign("radminjsversion", $fileversions['radmin.js']);
	$smarty->assign("application_version", $application_version);
	$smarty->assign("Application", APPLICATION_NAME);



	// Setup Menus
	$smarty->assign("MenuItems", createmenuitems());
	/*$smarty->assign("Usergroups", usergroups());*/


	// Costs
	//$smarty->assign("CurrencySymbols", currency_symbols());
	$smarty->assign("Datacosts", datacosts());
	$smarty->assign("GroupDatacosts", datavals());
	$smarty->assign("Datavals", datavals());
	$smarty->assign("Timecosts", timecosts());
	$smarty->assign("GroupTimecosts", timevals());
	$smarty->assign("Timevals", timevals());
	$smarty->assign("Bandwidthvals", bandwidth_options());
	$smarty->assign("Recurtimes",recurtimes()); 
	$smarty->assign("YesNo", yesno());


	// Settings
	$smarty->assign("Title", $location . " - " . APPLICATION_NAME);
	$smarty->assign("website_name", $website_name);
	$smarty->assign("website_link", $website_link);
    
	// Group data for displaying group properties	
	$smarty->assign("groupdata", DatabaseFunctions::getInstance()->getGroupAttributes());
	$smarty->assign("groupsettings", $Settings->getGroup());		
	$smarty->assign("groups", grouplist());	
	
	// Error, warning, success messages
	global $errormessages, $successmessages, $warningmessages;
	if(sizeof($errormessages) != 0)	$smarty->assign("error", $errormessages);
	if(sizeof($successmessages) != 0)	$smarty->assign("success", $successmessages);
	if(sizeof($warningmessages) != 0)	$smarty->assign("warningmessages", $warningmessages);		
	
	// DEMO SITE flag
	$smarty->assign("DEMOSITE", $DEMO_SITE);

	// Usermin assign vars
	if(function_exists('usermin_assign_vars')) usermin_assign_vars();
}


// These functions setup some globals that are used in validation functions, maybe we need to do it differently?
recurtimes();
//usergroups();
//groupexpirys();

?>
