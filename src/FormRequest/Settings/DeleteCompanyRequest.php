<?php


namespace App\FormRequest\Settings;

use Symfony\Component\Validator\Constraints as Constraints;

class DeleteCompanyRequest
{
    /**
     * @Constraints\NotBlank
     * @var string
     */
    public $password;

}