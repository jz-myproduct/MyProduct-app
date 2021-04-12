<?php


namespace App\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class FeatureTagNameUnique extends Constraint
{
    public $message;
}