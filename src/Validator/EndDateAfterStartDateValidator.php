<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use App\Entity\Request;

class EndDateAfterStartDateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof EndDateAfterStartDate) {
            throw new UnexpectedTypeException($constraint, EndDateAfterStartDate::class);
        }

        if (!$value instanceof Request) {
            return;
        }

        $startDate = $value->getStartDate();
        $endDate = $value->getEndDate();

        if ($startDate && $endDate && $endDate < $startDate) {
            $this->context->buildViolation($constraint->message)
                ->atPath('endDate')
                ->addViolation();
        }
    }
}