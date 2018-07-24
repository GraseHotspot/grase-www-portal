<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class StrToTime extends Constraint
{
    public $message = '"%string%" does not contain a valid StrToTime format';
    public $expiryInPastMessage = 'Expiry can not be in the past';

    public function validatedBy()
    {
        return get_class($this) . 'Validator';
    }
}
