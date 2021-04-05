<?php


namespace App\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CompanyEmailUnique extends Constraint
{
    public $message;
}