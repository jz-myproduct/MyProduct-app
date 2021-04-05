<?php

namespace App\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CompanyExists extends Constraint
{
    public $message;
}