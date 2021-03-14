<?php
namespace App\FormRequest;

use Symfony\Component\Validator\Constraints as Constraints;
use App\Constraints as Custom;

class RenewPasswordRequest
{
    /**
     * @Constraints\NotBlank
     * @Constraints\Email
     * @Constraints\Length(max=255)
     * @Custom\CompanyExists()
     * @var string
     */
    public $email;

}