<?php


namespace App\FormRequest\Security;

use Symfony\Component\Validator\Constraints as Constraints;


class SetNewPasswordRequest
{
    /**
     * @Constraints\NotBlank
     * @Constraints\Length(max=255)
     * @Constraints\Length(
     *      min = 6,
     *      minMessage = "The password must contain at least 6 characters",
     * )
     * @var string
     */
    public $password;
}