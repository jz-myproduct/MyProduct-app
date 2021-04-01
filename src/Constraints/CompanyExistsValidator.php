<?php


namespace App\Constraints;


use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CompanyExistsValidator extends ConstraintValidator
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint)
    {


        $company = $this->manager->getRepository(Company::class)->findOneBy(['email' => $value]);

        if($company) {
            return;
        }

        $this
            ->context
            ->buildViolation('Company with this email does not exist.')
            ->addViolation();
    }
}