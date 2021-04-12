<?php

namespace App\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * This annotation isn't currently used in any form, but it might be useful in the future.
 */
class CompanyExists extends Constraint
{
    public $message;
}