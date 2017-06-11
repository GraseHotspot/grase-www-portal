<?php

namespace Grase\RadminBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class StrToTimeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if ($value === null) {
            return true;
        }
        if (strtotime($value) == false) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $value)
                ->addViolation();
        }
        if (strtotime($value) < time()) {
            $this->context->buildViolation($constraint->expiryInPastMessage)
                ->addViolation();
        }
    }
}
