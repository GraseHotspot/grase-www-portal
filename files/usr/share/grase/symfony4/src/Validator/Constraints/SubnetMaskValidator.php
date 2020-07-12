<?php

namespace App\Validator\Constraints;

use App\Util\GraseUtil;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class SubnetMaskValidator extends ConstraintValidator
{
    /**
     * Validate a netmask. We expect a string netmask in the format of 255.255.255.0
     * CIDR netmasks aren't accepted, you can use the GraseUtil::CIDRtoMask() function to convert
     * A netmask isn't the same as an IP, but has the same format, so we can validate it as an IP
     * then add our extra checks
     *
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof SubnetMask) {
            throw new UnexpectedTypeException($constraint, SubnetMask::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        // First check if it's a valid IP address
        if (!filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ netmask }}', $this->formatValue($value))
                ->addViolation();

            return;
        }

        // Now check that if we convert to a CIDR (int) mask, we get an integer (no decimal places) between 8 and 30
        $cidrValue = GraseUtil::maskToCIDR($value);
        if (intval($cidrValue) != $cidrValue) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ netmask }}', $this->formatValue($value))
                ->addViolation();

            return;
        }

        // Check if it's between 8 and 30
        if ($cidrValue > 30 || $cidrValue < 8) {
            $this->context->buildViolation($constraint->messageCidrOutOfRange)
                ->setParameter('{{ netmask }}', $this->formatValue($value))
                ->addViolation();

            return;
        }
    }
}
