<?php

/* Copyright 2014 Timothy White */

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

$PAGE = 'ticketprintconfig';
require_once 'includes/pageaccess.inc.php';
require_once 'includes/session.inc.php';
require_once 'includes/misc_functions.inc.php';

$error = array();
$success = array();


// Options for ticket print Config that can only be one
$singleTicketPrintOptions = array(
    'printSSID' => array(
        "label" => T_("Wireless Network Name (SSID)"),
        "description" => T_(
            "Wireless Network Name that clients connect to. Currently does not modify any settings but if set will print
            on the tickets"
        ),
        "type" => "string"
    ),
    'printGroup' => array(
        "label" => T_("Print Ticket Type"),
        "description" => T_("Print the ticket type (Group name) on tickets"),
        "type" => "bool"
    ),
    'printExpiry' => array(
        "label" => T_("Print Ticket Expiry"),
        "description" => T_("Print the expiry on tickets"),
        "type" => "bool"
    ),

);


// Templates
$templateTicketPrintOptions = array(
    'preTicketHTML' => array(
        "label" => T_("Pre Ticket HTML"),
        "description" => T_("HTML to insert before the main ticket components"),
        "type" => "html"
    ),
    'postTicketHTML' => array(
        "label" => T_("Post Ticket HTML"),
        "description" => T_("HTML to insert after the main ticket components"),
        "type" => "html"
    ),
    'ticketPrintCSS' => array(
        "label" => T_("Ticket Printing CSS"),
        "description" => T_("CSS that is applied to the ticket printing page for printing batches and groups of tickets."),
        "type" => "css"
    ),

);

// Load all ticket print options
foreach ($singleTicketPrintOptions as $singleOption => $attributes) {
    $singleTicketPrintOptions[$singleOption]['value'] =
        $Settings->getSetting($singleOption);

}

// Load all templates
foreach ($templateTicketPrintOptions as $template => $attributes) {
    $templateTicketPrintOptions[$template]['value'] =
        $Settings->getTemplate($template);
}

if (isset($_POST['submit'])) {
    foreach ($singleTicketPrintOptions as $singleOption => $attributes) {
        switch ($attributes['type']) {
            default:
            case "string":
                $postValue = trim(\Grase\Clean::text($_POST[$singleOption]));
                break;
            case "int":
                $postValue = trim(clean_int($_POST[$singleOption]));
                break;
            case "number":
                $postValue = trim(clean_number($_POST[$singleOption]));
                break;
            case "bool":
                if (isset($_POST[$singleOption])) {
                    $postValue = true;
                } else {
                    $postValue = false;
                }
                break;

        }

        if ($postValue != $attributes['value']) {
            // Update options in database
            $Settings->setSetting($singleOption, $postValue);

            $success[] = sprintf(
                T_("%s ticket print config option update"),
                $attributes['label']
            );
        }

    }

    foreach ($templateTicketPrintOptions as $templateOption => $attributes) {
        // TODO: check that length isn't longer than maximum database length as it will be truncated
        $postValue = trim($_POST[$templateOption]);

        if ($postValue != $attributes['value']) {
            // Update options in database
            $Settings->setTemplate($templateOption, $postValue);

            $success[] = sprintf(
                T_("%s ticket print config option update"),
                $attributes['label']
            );
        }

    }

    // Reload due to posted data
    // Load all ticket print options
    foreach ($singleTicketPrintOptions as $singleOption => $attributes) {
        $singleTicketPrintOptions[$singleOption]['value'] =
            $Settings->getSetting($singleOption);
    }

// Load all templates
    foreach ($templateTicketPrintOptions as $template => $attributes) {
        $templateTicketPrintOptions[$template]['value'] =
            $Settings->getTemplate($template);
    }
}

if (sizeof($error) > 0) {
    $templateEngine->assign("error", $error);
}
if (sizeof($success) > 0) {
    $templateEngine->assign("success", $success);
}

$templateEngine->assign("singleTicketPrintOptions", $singleTicketPrintOptions);
$templateEngine->assign("templateTicketPrintOptions", $templateTicketPrintOptions);
$templateEngine->displayPage('ticketprintconfig.tpl');
