<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class StrToTime extends Constraint
{
    /**
     * @return string
     */
    public function validatedBy()
    {
        return StrToTimeValidator::class;
    }
}
