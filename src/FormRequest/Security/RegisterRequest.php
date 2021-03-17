<?php


namespace App\FormRequest\Security;

use Symfony\Component\Validator\Constraints as Assert;
use App\Constraints as Custom;



class RegisterRequest
{

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Assert\Length(max=255)
     * @Custom\CompanyEmailUnique()
     */
    public $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Assert\Length(
     *      min = 6,
     *      minMessage = "Heslo musí obsahovat minimálně 6 znaků",
     * )
     */
    public $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $name;

}