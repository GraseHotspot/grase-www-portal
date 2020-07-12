<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class SubnetMask extends Constraint
{
    public $message = 'The netmask {{ netmask }} is invalid.';
    public $messageCidrOutOfRange = 'The netmask {{ netmask }} is too large or small.';
}
