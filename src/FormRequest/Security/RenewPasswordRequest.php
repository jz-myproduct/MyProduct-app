<?php
namespace App\FormRequest\Security;

use Symfony\Component\Validator\Constraints as Constraints;
use App\Constraint as Custom;

class RenewPasswordRequest
{
    /**
     * @Constraints\NotBlank
     * @Constraints\Email
     * @Constraints\Length(max=255)
     * @var string
     */
    public $email;

}