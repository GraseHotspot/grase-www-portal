<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 10/10/14
 * Time: 5:41 PM
 */

namespace Grase\VoucherWizard;


class State
{
    public $wizardPage = 'initialpage';
    public $sessionExpiry = null;
    public $selectedVoucher;
    public $selectedPaymentGateway;
    public $selectionConfirmed = false;
    public $pendingAccount = false;
}
