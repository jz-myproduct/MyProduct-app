<?php


namespace App\FormRequest;

use Symfony\Component\Validator\Constraints as Constraints;


class SetNewPasswordRequest
{
    /**
     * @Constraints\NotBlank
     * @Constraints\Length(max=255)
     * @Constraints\Length(
     *      min = 6,
     *      minMessage = "Heslo musí obsahovat minimálně 6 znaků",
     * )
     * @var string
     */
    public $password;
}