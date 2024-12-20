<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EndDateAfterStartDate extends Constraint
{
    public $message = 'End date cannot be before start date.';
}
