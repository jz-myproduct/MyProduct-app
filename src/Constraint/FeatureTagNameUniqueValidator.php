<?php


namespace App\Constraint;


use App\Entity\Company;
use App\Entity\FeatureTag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FeatureTagNameUniqueValidator extends ConstraintValidator
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
        $featureTag = $this->manager->getRepository(FeatureTag::class)
            ->findBy([
                'name' => $value,
                'company' => $this->getLoggedInCompany()
            ]);

        if(! $featureTag){
            return;
        }

        $this
            ->context
            ->buildViolation('Tag already exists.')
            ->addViolation();
    }

    private function getLoggedInCompany()
    {
        return
            $this->manager->getRepository(Company::class)
                ->findCompanyByEmail($this->security->getUser()->getUsername());
    }
}