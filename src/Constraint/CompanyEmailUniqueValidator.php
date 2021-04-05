<?php


namespace App\Constraint;


use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CompanyEmailUniqueValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var Security
     */
    private $security;

    public function __construct(EntityManagerInterface $manager, Security $security)
    {
        $this->manager = $manager;
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint)
    {

        $companyByEmail = $this->manager->getRepository(Company::class)->findOneBy(['email' => $value]);

        if($this->security->getUser()){

            $loggedInCompany = $this->manager->getRepository(Company::class)->findCompanyByEmail(
                $this->security->getUser()->getUsername());

            if($loggedInCompany === $companyByEmail) {
                return;
            }

        }

        if (!$companyByEmail) {
            return;
        }

        $this
            ->context
            ->buildViolation('Email is already used.')
            ->addViolation();

    }

}