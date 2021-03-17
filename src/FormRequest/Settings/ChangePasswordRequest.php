<?php


namespace App\FormRequest\Settings;

use Symfony\Component\Validator\Constraints as Constraints;




class ChangePasswordRequest
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

    /**
     * @Constraints\NotBlank
     * @Constraints\Length(max=255)
     * @Constraints\Length(
     *      min = 6,
     *      minMessage = "Heslo musí obsahovat minimálně 6 znaků",
     * )
     * @var string
     */
    public $newPassword;




}