<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 *
 * @Annotation
 */
class StrToTime extends Constraint
{
    // TODO Ensure this can be translated
    public $message = '"%string%" does not contain a valid StrToTime format';
    public $expiryInPastMessage = 'Expiry can not be in the past';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return StrToTimeValidator::class;
    }
}
