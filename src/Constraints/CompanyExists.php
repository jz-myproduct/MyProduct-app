<?php

namespace App\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CompanyExists extends Constraint
{
    public $message;
}