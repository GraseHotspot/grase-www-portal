<?php

namespace Grase;

class VoucherWizard
{

    private $vouchers;
    private $voucherGroups = array();
    private $groupedVouchers = array();
    private $validVouchers;

    private $state;

    // Payment gateway stuff

    private $paymentgateways = array(
        'PayPal' => array(
            'Label' => 'PayPal',
            'Description' => 'Use your paypal account or credit card to pay',
            'paid' => true,
        ),
        'Dummy' => array(
            'Label' => 'Dummy Gateway',
            'Description' => 'Dummy Gateway that always says you have paid',
            'free' => true,
            'paid' => true,
            'pluginfile' => 'dummy.inc.php',
        ),
        'SMS' => array(
            'Label' => 'SMS Token',
            'Description' => 'Free account with limitations, details SMSed to your mobile',
            'free' => true,
        ),
    );


    public function __construct(Database\Radmin $Settings, $templateEngine, \DatabaseFunctions $databaseFunctions, VoucherWizard\State $state)
    {
        $this->wakeUp($Settings, $templateEngine, $databaseFunctions);
        $this->loadVouchers();

        $this->state = $state;

    }

    public function getState()
    {
        return $this->state;
    }

    public function wakeUp(Database\Radmin $Settings, $templateEngine, \DatabaseFunctions $databaseFunctions)
    {
        $this->Settings = $Settings;
        $this->templateEngine = $templateEngine;
        $this->DBF = $databaseFunctions;
    }

    private function loadVouchers()
    {
        $this->vouchers = $this->Settings->getVoucher();

        foreach ($this->vouchers as $voucher) {
            if ($voucher['InitVoucher']) {
                $this->voucherGroups[$voucher['VoucherGroup']] = true;
                $this->groupedVouchers[$voucher['VoucherGroup']][] = $voucher;
                $this->validVouchers[] = $voucher['VoucherName'];
            }
        }
    }

    public function pageLoad()
    {
        $this->checkSessionExpired();
        $this->checkWizardRestart();
    }

    private function processCurrectPage()
    {
        switch ($this->state->wizardPage) {
            case 'initialpage':
                $this->processInitialPage();
                break;
            case 'confirmselectionpage':
                $this->processConfirmSelectionPage();
                break;
            case 'paymentpage':
                $this->processPaymentPage();
                break;
            default:
                // Should never get here. Throw an exception?
        }
    }

    private function checkSessionExpired()
    {
        if (!is_null($this->state->sessionExpiry) && $this->state->sessionExpiry < time()) {
            // Session has expired. Destroy it
            $this->restartWizard();
        }
    }

    private function checkWizardRestart()
    {
        if (isset($_POST['restartwizard'])) {
            $this->restartWizard();
        }
    }

    private function processInitialPage()
    {
        if (isset($_POST['gotopayment'])) {
            // Assume form is valid
            $valid = true;

            // We have a form submitted, check it's valid and move to the next step
            // Check voucher is valid
            if (!in_array($_POST['voucherselected'], $this->validVouchers)) {
                $valid = false;
            }

            // Check payment gateway is valid
            if (!array_key_exists($_POST['gatewayselected'], $this->paymentgateways)) {
                $valid = false;
            }

            // Check for "free" vouchers that the selected payment gateway supports free tickets

            if (
                $this->vouchers[$_POST['voucherselected']]['VoucherPrice'] == 0 &&
                !$this->paymentgateways[$_POST['gatewayselected']]['free']
            ) {
                $valid = false;
                $error = T_("Your selected payment gateway doesn't support free vouchers");
            }

            // Check for "paid" vouchers that the selected payment gateway supports paid tickets

            if (
                $this->vouchers[$_POST['voucherselected']]['VoucherPrice'] != 0 &&
                !$this->paymentgateways[$_POST['gatewayselected']]['paid']
            ) {
                $valid = false;
                $error = T_("Your selected payment gateway doesn't support paid vouchers");
            }

            // If we have a valid selection, continue

            if ($valid) {
                // Valid submission

                $this->state->wizardPage = 'confirmselectionpage';
                $this->state->selectedVoucher = $_POST['voucherselected'];
                $this->state->selectedPaymentGateway = $_POST['gatewayselected'];
                $this->reloadPage();
                exit;
            } else {
                $error = $error ? $error : T_('Invalid Voucher or Payment Selection');
                $this->templateEngine->assign('error', $error);
            }
        }
        $this->templateEngine->assign(
            "groupsettings",
            array_intersect_key($this->Settings->getGroup(), $this->voucherGroups)
        );
        $this->templateEngine->assign("vouchers", $this->groupedVouchers);
        $this->templateEngine->assign('paymentgateways', $this->paymentgateways);
        $this->templateEngine->display('wizard_initial.tpl');
    }

    private function processConfirmSelectionPage()
    {
        if (isset($_POST['selectionconfirmed'])) {
            // We have a form submitted, check it's valid and move to the next step
            if ($_POST['selectionconfirmed'] == 'correct') {
                // We can continue with payment
                $this->state->wizardPage = 'paymentpage';
                $this->state->selectionConfirmed = true;
                // TODO store selection in database yet? i.e. make user but don't give them access to it's details until we've gotten payment?
                $this->reloadPage();
                exit;
            } else {
                // Selection is not confirmed, start again
                restart_wizard();
            }
        }
        $this->templateEngine->assign('selectedgateway', $this->state->selectedPaymentGateway);
        $this->templateEngine->assign('selectedvoucher', $this->state->selectedVoucher);
        $this->templateEngine->display('wizard_confirmselection.tpl');
    }

    private function processPaymentPage()
    {

        //TODO Create user account and lock it here, so it's ready for the plugin to do with as needed (i.e. send details)

        if ($this->state->pendingAccount === false) {
            /* Create our locked random user */

            $MaxMb = $this->vouchers[$this->state->selectedVoucher]['MaxMb'];
            $MaxTime = $this->vouchers[$this->state->selectedVoucher]['MaxTime'];
            $Expiry = expiry_for_group($this->vouchers[$this->state->selectedVoucher]['VoucherGroup']);
            $Comment = $this->state->selectedVoucher . " Voucher purchased " . date('c');
            $Username = Util::randomUsername(5);
            $Password = Util::randomPassword(6);

            // TODO Maybe set expiry to a few days so if payment isn't valid then we expire soon, and after sucessful payment we update expiry?

            // TODO: Check if valid
            $this->DBF->createUser(
                $Username,
                $Password,
                $MaxMb,
                $MaxTime,
                $Expiry,
                false, // Don't currently have ExpireAfter for vouchers
                $this->vouchers[$this->state->selectedVoucher]['VoucherGroup'],
                $Comment
            );

            // Lock user account
            $this->DBF->lockUser($Username, T_('Account Pending Payment and Activation'));

            // Store user account in session
            $this->state->pendingAccount = array('Username' => $Username, 'Password' => $Password);
        }

        /* */


        require_once('paymentgateways/PaymentGatewayPlugin.class.php');
        if (!is_file('paymentgateways/' . $paymentgateways[$this->state->selectedPaymentGateway]['pluginfile'])) {
            die('Invalid payment plugin<br/><form action="" method="POST"><input type="hidden" name="pgformsubmission" value="1"/><input name="restartwizard" type="submit" value="Restart Wizard"/>');
        }

        // TODO Clean up and make error detection lots lots better

        require_once('paymentgateways/' . $paymentgateways[$this->state->selectedPaymentGateway]['pluginfile']);

        // Recreate object each time

        $classname = "PG_" . $this->state->selectedPaymentGateway;
        $paymentplugin = new $classname($_SESSION['PendingAccount'], $_SESSION['selectedvoucher']);

        //$paymentplugin-> // Load voucher and user details (at initilisation) TODO
        // Load state from SESSION

        if (isset($_SESSION['paymentGatewayPluginState'])) {
            $paymentplugin->setState($_SESSION['paymentGatewayPluginState']);
        }

        $nextpage = $paymentplugin->currentPage();

        // Check if payment is complete

        if (!$paymentplugin->isPaymentCompleted()) {
            // Payment isn't completed
            // Check for page submission

            if (isset($_POST['pgformsubmission'])) {
                $nextpage = $paymentplugin->processPage($nextpage);

                // TODO After processing page, again check if payment is complete
            }
        }

        // Page has been processed, we now check if payment is complete and do what we need

        if ($paymentplugin->isPaymentCompleted() && !isset($_SESSION['AccountActivated'])) {
            // Payment completed, display user details, activate user, cleanup

            // Activate the account. It's upto the plugin to display things
            $this->DBF->unlockUser($_SESSION['PendingAccount']['Username']);

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

        // Load payment gateway based on $this->selectedPaymentGateway
    }

    private function restartWizard()
    {
        session_destroy();
        $this->reloadPage();
        exit;
    }

    private function reloadPage($extra = 'purchase_wizard')
    {
        $host = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("Location: http://$host$uri/$extra");
        exit;
    }
}
