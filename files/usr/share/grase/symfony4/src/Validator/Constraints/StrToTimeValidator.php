<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * This class is used to Validate the Group Expiry time strings
 */
class StrToTimeValidator extends ConstraintValidator
{
    /**
     * @param mixed      $value
     * @param Constraint $constraint
     *
     * @return bool
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return true;
        }
        if (strtotime($value) === false) {
            $this->context->buildViolation('grase.constraint.strtotime.invalid.%string%')
                ->setParameter('%string%', $value)
                ->addViolation();
        } elseif (strtotime($value) < time()) {
            $this->context->buildViolation('grase.constraint.strtotime.expiryInPast')
                ->addViolation();
        }
    }
}
