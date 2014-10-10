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


require_once __DIR__.'/../../vendor/autoload.php';

function grase_autoload($class_name) {
    if( file_exists(__DIR__. '/classes/' . $class_name . '.class.php'))
    {
        include_once __DIR__. '/classes/' . $class_name . '.class.php';
    }
}

spl_autoload_register('grase_autoload');

require_once('php-gettext/gettext.inc');


require_once 'includes/load_settings.inc.php';
require_once 'includes/page_functions.inc.php';

require_once 'includes/misc_functions.inc.php';

// Need a session variable that we hold current place in wizard? Don't rely on posts to choose what we do in the wizard, always go to correct place in wizard and from there it can process post and redirect to wizard

session_name('GrasePurchaseWizard');
session_start();

$voucherWizard = new \Grase\VoucherWizard($Settings, $templateEngine, DatabaseFunctions::getInstance(), new \Grase\VoucherWizard\State());

echo serialize($voucherWizard->getState());
exit;

// Do the wizard page logic

switch($_SESSION['wizardpage'])
{
    case 'initialpage':
        if(isset($_POST['gotopayment']))
        {
            // Assume form is valid
            $valid = true;

            // We have a form submitted, check it's valid and move to the next step


            // Check voucher is valid
            if(!in_array($_POST['voucherselected'], $validVouchers))
            {
                $valid = false;
            }

            // Check payment gateway is valid
            if(!array_key_exists($_POST['gatewayselected'], $paymentgateways))
            {
                $valid = false;
            }

            // Check for "free" vouchers that the selected payment gateway supports free tickets

            if($vouchers[$_POST['voucherselected']]['VoucherPrice'] == 0 && !$paymentgateways[$_POST['gatewayselected']]['free'])
            {
                $valid = false;
                $error = T_("Your selected payment gateway doesn't support free vouchers");
            }

            // Check for "paid" vouchers that the selected payment gateway supports paid tickets

            if($vouchers[$_POST['voucherselected']]['VoucherPrice'] != 0 && !$paymentgateways[$_POST['gatewayselected']]['paid'])
            {
                $valid = false;
                $error = T_("Your selected payment gateway doesn't support paid vouchers");
            }

            // If we have a valid selection, continue

            if($valid)
            {
                // Valid submission

                $_SESSION['wizardpage']             = 'confirmselectionpage';
                $_SESSION['selectedvoucher']        = $_POST['voucherselected'];
                $_SESSION['selectedpaymentgateway'] = $_POST['gatewayselected'];
                reload_page();
                exit;
            } else
            {
                $error = $error ? $error : T_('Invalid Voucher or Payment Selection');
                $templateEngine->assign('error', $error);
            }
        }
        $templateEngine->assign("groupsettings", array_intersect_key($Settings->getGroup(), $voucherGroups));
        $templateEngine->assign("vouchers", $groupedVouchers);
        $templateEngine->assign('paymentgateways', $paymentgateways);
        $templateEngine->display('wizard_initial.tpl');
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

                reload_page();
                exit;
            } else
            {

                // Selection is not confirmed, start again
                restart_wizard();

            }
        }
        $templateEngine->assign('selectedgateway', $_SESSION['selectedpaymentgateway']);
        $templateEngine->assign('selectedvoucher', $_SESSION['selectedvoucher']);
        $templateEngine->display('wizard_confirmselection.tpl');
        
        break;

    
    case 'paymentpage':

        //TODO Create user account and lock it here, so it's ready for the plugin to do with as needed (i.e. send details)
        //var_dump($_SESSION);
        //var_dump($_POST);
        //var_dump($vouchers);

        if(!isset($_SESSION['PendingAccount']))
        {
            /* Create our locked random user */

            $MaxMb    = $vouchers[$_SESSION['selectedvoucher']]['MaxMb'];
            $MaxTime  = $vouchers[$_SESSION['selectedvoucher']]['MaxTime'];
            $Expiry   = expiry_for_group($vouchers[$_SESSION['selectedvoucher']]['VoucherGroup']);
            $Comment  = $_SESSION['selectedvoucher']." Voucher purchased ".date();
            $Username = \Grase\Util::randomUsername(5);
            $Password = \Grase\Util::randomPassword(6);

            // TODO Maybe set expiry to a few days so if payment isn't valid then we expire soon, and after sucessful payment we update expiry?

            DatabaseFunctions::getInstance()->createUser(// TODO: Check if valid
                $Username, 
                $Password, 
                $MaxMb, 
                $MaxTime, 
                $Expiry,
                false, // Don't currently have ExpireAfter for vouchers
                $vouchers[$_SESSION['selectedvoucher']]['VoucherGroup'], 
                $Comment
            );
            
            // Lock user account
            DatabaseFunctions::getInstance()->lockUser($Username, T_('Account Pending Payment and Activation'));
            
            // Store user account in session
            $_SESSION['PendingAccount'] = array('Username' => $Username, 'Password' => $Password);
        }

        /* */
        

        require_once('paymentgateways/PaymentGatewayPlugin.class.php');
        if(!is_file('paymentgateways/'.$paymentgateways[$_SESSION['selectedpaymentgateway']]['pluginfile']))
        {
            die('Invalid payment plugin<br/><form action="" method="POST"><input type="hidden" name="pgformsubmission" value="1"/><input name="restartwizard" type="submit" value="Restart Wizard"/>');
        }

        // TODO Clean up and make error detection lots lots better

        require_once('paymentgateways/'.$paymentgateways[$_SESSION['selectedpaymentgateway']]['pluginfile']);

        // Recreate object each time

        $classname = "PG_".$_SESSION['selectedpaymentgateway'];
        $paymentplugin = new $classname($_SESSION['PendingAccount'], $_SESSION['selectedvoucher']);

        //$paymentplugin-> // Load voucher and user details (at initilisation) TODO
        // Load state from SESSION

        if(isset($_SESSION['paymentGatewayPluginState']))
        {
            $paymentplugin->setState($_SESSION['paymentGatewayPluginState']);
        }
        
        $nextpage = $paymentplugin->currentPage();

        // Check if payment is complete

        if(!$paymentplugin->isPaymentCompleted())
        {
            // Payment isn't completed
            // Check for page submission

            if(isset($_POST['pgformsubmission']))
            {
                $nextpage = $paymentplugin->processPage($nextpage);

                // TODO After processing page, again check if payment is complete
            }
        }

        // Page has been processed, we now check if payment is complete and do what we need

        if($paymentplugin->isPaymentCompleted() && ! isset($_SESSION['AccountActivated']))
        {
            // Payment completed, display user details, activate user, cleanup

            // Activate the account. It's upto the plugin to display things
            DatabaseFunctions::getInstance()->unlockUser($_SESSION['PendingAccount']['Username']);
            
            $_SESSION['AccountActivated'] = true;
            // Expire session after 5 minutes to prevent others from seeing saved login details
            // TODO provide link to clear details
            $_SESSION['ExpireSession'] = time() + 300;
            
            // TODO Store purchase details in database, along with payment details including price and plugin used, and any reciept number
            //print $paymentplugin->getPaymentDetails(); TODO TODO TODO TODO
        }

        // Regardless of payment completion and page processing, we now display the page. If anything is wrong with the processing this page will let us know as the plugin handles which state we are in.

        $pagecontents = $paymentplugin->getPageContents($nextpage);
        echo '<form action="" method="POST"><input type="hidden" name="pgformsubmission" value="1"/>';
        echo $pagecontents;

        // Store state into SESSION

        $_SESSION['paymentGatewayPluginState'] = $paymentplugin->getState();

        // Load payment gateway based on $_SESSION['selectedpaymentgateway']

        break;
}

function restart_wizard()
{
    session_destroy();
    reload_page();
    exit;
}

function reload_page($extra = 'purchase_wizard')
{
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    header("Location: http://$host$uri/$extra");
    exit;
}

?>
