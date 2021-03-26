<?php


namespace App\Handler\Settings;


use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ChangePassword
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->manager = $manager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function handle(Company $company, $password)
    {
        $company->setPassword(
            $this->passwordEncoder->encodePassword(
                $company,
                $password
            )
        );

        $company->setUpdatedAt(new \DateTime());

        $this->manager->flush();

    }

}