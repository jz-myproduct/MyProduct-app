<?php


namespace App\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CompanyEmailUnique extends Constraint
{
    public $message;
}