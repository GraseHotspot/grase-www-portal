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

require_once __DIR__.'/../../../vendor/autoload.php';
require_once('includes/accesscheck.inc.php');
require_once('includes/site_settings.inc.php');
require_once 'includes/pageaccess.inc.php';

// We require misc_functions due to locale stuff
require_once 'includes/misc_functions.inc.php';




// TODO: Move smarty_sortby.php into a proper class and fix up Page
require_once 'smarty_sortby.php';


function css_file_version()
{
    $resourcefiles = array(
        '/usr/share/grase/www/hotspot.css',
        '/usr/share/grase/www/radmin/radmin.css',
        '/usr/share/grase/www/js/grase.js',
        '/usr/share/grase/www/radmin/js/radmin.js'
        );
    foreach ($resourcefiles as $file) {
        $fileversions[basename($file)] = date("YmdHis", filemtime($file));
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
            'createmachine' => array("href" => "newuser?computer", "label" => T_("Computer Account"))
            )
        );
    $menubar['sessions'] = array("href" => "sessions", "label" => T_("Monitor Sessions"),
        "submenu" => array(
            'reports' => array("href" => "reports", "label" => T_("Reports")),
            'dhcpleases' => array("href" => "dhcpleases", "label" =>T_("DHCP Leases")),
            //'monthly_accounts' => array("href" => "datausage", "label" => "Monthly Reports"); // Not working atm TODO:
            
        )
        
        );
    
    $menubar['settings'] = array("href" => "settings", "label" => T_("Settings"),
        "submenu" => array(
            'uploadlogo' => array("href" => "uploadlogo", "label" => T_("Site Logo") ),
            'netconfig' => array("href" => "netconfig", "label" => T_("Network Settings") ),
            'chilliconfig' => array("href" => "chilliconfig", "label" => T_("Coova Chilli Settings") ),
            'loginconfig' => array("href" => "loginconfig", "label" => T_("Portal Customisation") ),
            'ticketprintconfig' => array("href" => "ticketprintconfig.php", "label" => T_("Ticket Print Settings") ),
            'groups' => array("href" => "groupconfig", "label" => T_("Groups") ),
            //'vouchers' => array("href" => "voucherconfig", "label" => T_("Vouchers") ), // DISABLED FOR RELEASE AS NOT YET READY FOR PRODUCTION

        )
            
                    
     );

    $menubar['passwd'] = array("href" => "passwd", "label" => T_("Admin Users"),
        "submenu" => array(
            'adminlog' => array("href" => "adminlog", "label" => T_("Admin Log") ),
        )
    
     );

    
    $menubar['logout'] = array("href" => "./?logoff", "label" => T_("Logoff") );
    
    // Filter out menu items user doesn't have access to
    $newmenubar = array();
    foreach ($menubar as $label => $toplevel) {
    // If they don't have access to top level of a menu section, they also don't have access to the levels below it via the menu (still up to the PAGESACCESS to prevent access
        if (check_level($PAGESACCESS[$label])) {
            $submenu = array();
          
            if (isset($toplevel['submenu']) && is_array($toplevel['submenu'])) {
                foreach ($toplevel['submenu'] as $secondlabel => $secondlevel) {
                    if (check_level($PAGESACCESS[$secondlabel])) {
                        $submenu[$secondlabel] = $secondlevel;
                    }
                }
            }
            $item = $toplevel;
            unset($item['submenu']);
            if (sizeof($submenu)) {
                $item['submenu'] = $submenu;
            }
            $newmenubar[$label] = $item;
        }
    }
    return $newmenubar;
}

// TODO rename datacosts to better reflect that it just has inherit added to datavals
function datacosts()
{
    global $datacosts;
    $datacosts['inherit'] = T_('Inherit from group');
    $datacosts = $datacosts + datavals();
    return $datacosts;
}

function datavals()
{
    global $Settings;
    $datavals[''] = '';
    $mboptions = explode(" ", $Settings->getSetting('mbOptions'));
    foreach ($mboptions as $mb) {
        $datavals["$mb"] = \Grase\Util::formatBytes($mb*1024*1024);
        ;
    }
    return $datavals;
}

function timevals()
{
        global $Settings;
    $timevals[''] = '';
    $timeoptions = explode(" ", $Settings->getSetting('timeOptions'));
    foreach ($timeoptions as $time) {
        if ($time >= 60) {
            $timevals["$time"] = $time/60 . " hours";
        } else {
            $timevals["$time"] = "$time mins";
        }
    }
    return $timevals;
}

function timecosts()
{
    global $timecosts;
    $timecosts['inherit'] = T_('Inherit from group');
    $timecosts = $timecosts + timevals();
        
    return $timecosts;
}

function bandwidth_options()
{
    global $Settings;
    // kbits/second
    $kbits_options = explode(" ", $Settings->getSetting('kBitOptions'));
    $options[''] = '';
    foreach ($kbits_options as $kbits) {
        $bits = $kbits * 1024;
        $kbytes = $kbits/8;
        $mbmin = round($kbytes * 60 / 1024, 2);
        $label = \Grase\Util::formatBits($bits) ." ($kbytes kbytes/sec, $mbmin MiB/min)";
        $options["$kbits"] = $label;
    }
    
    return $options;

}

function grouplist()
{
    // Makes an array of groupname=>groupname for html drop downs
    global $Settings;
    $groups = array();
    foreach ($Settings->getGroup() as $groupname => $group) {
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

$templateEngine = new \Grase\Page();

\Grase\Locale::applyLocale($Settings->getSetting('locale'));




function assign_vars($templateEngine)
{
    global $Settings;
    
    list($fileversions, $application_version)=css_file_version();
    $templateEngine->assign("radmincssversion", $fileversions['radmin.css']);
    $templateEngine->assign("hotspotcssversion", $fileversions['hotspot.css']);
    $templateEngine->assign("grasejsversion", $fileversions['grase.js']);
    $templateEngine->assign("radminjsversion", $fileversions['radmin.js']);
    $templateEngine->assign("application_version", $application_version);
    $templateEngine->assign("Application", APPLICATION_NAME);



    // Setup Menus
    $templateEngine->assign("MenuItems", createmenuitems());
    /*$smarty->assign("Usergroups", usergroups());*/


    // Costs
    //$smarty->assign("CurrencySymbols", currency_symbols());
    $templateEngine->assign("Datacosts", datacosts());
    $templateEngine->assign("GroupDatacosts", datavals());
    $templateEngine->assign("Datavals", datavals());
    $templateEngine->assign("Timecosts", timecosts());
    $templateEngine->assign("GroupTimecosts", timevals());
    $templateEngine->assign("Timevals", timevals());
    $templateEngine->assign("Bandwidthvals", bandwidth_options());
    $templateEngine->assign("Recurtimes", recurtimes());


    // Settings
    $templateEngine->assign("Title", $Settings->getSetting('locationName') . " - " . APPLICATION_NAME);
    $templateEngine->assign("website_name", $Settings->getSetting('websiteName'));
    $templateEngine->assign("website_link", $Settings->getSetting('websiteLink'));
    
    // Group data for displaying group properties
    $templateEngine->assign("groupdata", DatabaseFunctions::getInstance()->getGroupAttributes());
    $templateEngine->assign("groupsettings", $Settings->getGroup());
    $templateEngine->assign("groups", grouplist());
    
    // DEMO SITE flag
    // Allow extra things on Demo site (piwik tracking of admin interface)
    $templateEngine->assign("DEMOSITE", $Settings->getSetting('demosite'));

    // Usermin assign vars
    if (function_exists('usermin_assign_vars')) {
        usermin_assign_vars();
    }
}


// These functions setup some globals that are used in validation functions, maybe we need to do it differently?
recurtimes();
//usergroups();
//groupexpirys();
