<?php

/* Copyright 2012 Timothy White */

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


function __autoload($class_name) {
    require_once './classes/' . $class_name . '.class.php';
}

require_once('php-gettext/gettext.inc');


require_once 'includes/load_settings.inc.php';
require_once 'includes/page_functions.inc.php';

require_once 'includes/misc_functions.inc.php';
require_once 'includes/database_functions.inc.php';;

// Need a session variable that we hold current place in wizard? Don't rely on posts to choose what we do in the wizard, always go to correct place in wizard and from there it can process post and redirect to wizard
session_start();

// Make sure we have initial page, or set one
if(!isset($_SESSION['wizardpage'])) $_SESSION['wizardpage'] = 'initialpage';

// Groups and vouchers stuff
$groups = array();
$vouchers = $Settings->getVoucher();
$grouped_vouchers = array();

foreach($vouchers  as $voucher)
{
        if($voucher['InitVoucher'])
        {
                $groups[$voucher['VoucherGroup']] = true;
                $grouped_vouchers[$voucher['VoucherGroup']][] = $voucher;
                $valid_vouchers[] = $voucher['VoucherName'];
        }
        
}

$groups_with_vouchers = array_intersect_key($Settings->getGroup(), $groups);

// Payment gateway stuff
$paymentgateways = array(
        'PayPal' => array(
                'Label' => 'PayPal',
                'Description' => 'Use your paypal account or credit card to pay',
        ),
        'Dummy' => array(
                'Label' => 'Dummy Gateway',
                'Description' => 'Dummy Gateway that always says you have paid',
        )
        
);
$freepaymentgateways = array(        
        'SMS' => array(
                'Label' => 'SMS Token',
                'Description' => 'Free account with limitations, details SMSed to your mobile',
        ),
        
        'Dummy' => array(
                'Label' => 'Dummy Gateway',
                'Description' => 'Dummy Gateway that always says you have paid',
        )
);

if(isset($_SESSION['selectedvoucher']))
{
        if($vouchers[$_SESSION['selectedvoucher']]['VoucherPrice'] == 0)
                $paymentgateways = $freepaymentgateways;

}


// Do the wizard page logic

switch($_SESSION['wizardpage'])
{
case 'initialpage':
    if(isset($_POST['voucherselected']))
    {
        // We have a form submitted, check it's valid and move to the next step
        if(in_array($_POST['voucherselected'], $valid_vouchers))
        {
                // Valid submission
                $_SESSION['wizardpage'] = 'nextpage';
                $_SESSION['selectedvoucher'] = $_POST['voucherselected'];
                $host  = $_SERVER['HTTP_HOST'];
                $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                $extra = 'purchase_wizard';
                header("Location: http://$host$uri/$extra");
                exit;
        }
    }
    $smarty->assign("groupsettings", $groups_with_vouchers);
    $smarty->assign("vouchers", $grouped_vouchers);
    $smarty->display('wizard_initial.tpl');
    break;
    
case 'nextpage':
    if(isset($_POST['gatewayselected']))
    {
        // We have a form submitted, check it's valid and move to the next step
        if(array_key_exists($_POST['gatewayselected'], $paymentgateways))
        {
                // Valid submission
                $_SESSION['wizardpage'] = 'confirmselectionpage';
                $_SESSION['selectedpaymentgateway'] = $_POST['gatewayselected'];
                $host  = $_SERVER['HTTP_HOST'];
                $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                $extra = 'purchase_wizard';
                header("Location: http://$host$uri/$extra");
                exit;
        }
    }    
    $smarty->assign('paymentgateways', $paymentgateways);
    $smarty->assign('selectedvoucher', $_SESSION['selectedvoucher']);
    $smarty->display('wizard_paymentgateway.tpl');

    break;

case 'confirmselectionpage':
    if(isset($_POST['selectionconfirmed']))
    {
        // We have a form submitted, check it's valid and move to the next step
        if($_POST['selectionconfirmed'] == 'correct')
        {
                // We can continue with payment
                $_SESSION['wizardpage'] = 'paymentpage';
                $_SESSION['selectionconfirmed'] = TRUE;
                // TODO store selection in database yet? i.e. make user but don't give them access to it's details until we've gotten payment?
                $host  = $_SERVER['HTTP_HOST'];
                $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                $extra = 'purchase_wizard';
                header("Location: http://$host$uri/$extra");
                exit;
        }else{
                // Selection is not confirmed, start again
                session_destroy();
                $host  = $_SERVER['HTTP_HOST'];
                $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                $extra = 'purchase_wizard';
                header("Location: http://$host$uri/$extra");
                exit;        
        }
    }    
    $smarty->assign('selectedgateway', $_SESSION['selectedpaymentgateway']);
    $smarty->assign('selectedvoucher', $_SESSION['selectedvoucher']);
    $smarty->display('wizard_confirmselection.tpl');
    
    break;

case 'paymentpage':
    var_dump($_SESSION);
    // Load payment gateway based on $_SESSION['selectedpaymentgateway']
    break;

}

?>
