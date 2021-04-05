<?php


namespace App\FormRequest\Security;

use Symfony\Component\Validator\Constraints as Assert;
use App\Constraint as Custom;



class RegisterCompanyRequest
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
     *      minMessage = "The password must contain at least 6 characters",
     * )
     */
    public $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $name;

}