<?php


namespace App\FormRequest\Settings;

use App\Entity\Company;
use Symfony\Component\Validator\Constraints as Assert;
use App\Constraint as Custom;

class InfoRequest
{

    /**
     * @Assert\NotBlank
     * @Assert\Email
     * @Assert\Length(max=255)
     * @Custom\CompanyEmailUnique()
     * @var string
     */
    public $username;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     * @var string
     */
    public $name;

    public static function fromCompany(Company $company)
    {
        $companyRequest = new self();

        $companyRequest->name = $company->getName();
        $companyRequest->username = $company->getUsername();

        return $companyRequest;
    }

}