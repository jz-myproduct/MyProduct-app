<?php


namespace App\Handler\Company;


use App\Entity\Company;
use App\Entity\Portal;
use App\FormRequest\Security\RegisterRequest;
use App\Services\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Add
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var SlugService
     */
    private $slugService;

    public function __construct(
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $passwordEncoder,
        SlugService $slugService)
    {
        $this->manager = $manager;
        $this->passwordEncoder = $passwordEncoder;
        $this->slugService = $slugService;
    }

    public function handle(RegisterRequest $request)
    {
        $company = new Company();

        $company->setName($request->name);
        $company->setEmail($request->email);
        $company->setPassword(
            $this->passwordEncoder->encodePassword($company, $request->password)
        );
        $company->setSlug(
            $this->slugService->createCompanySlug(
                $request->name
            )
        );
        $currentDateTime = new \DateTime();
        $company->setCreatedAt($currentDateTime);
        $company->setCreatedAt($currentDateTime);
        $company->setRoles([Company::ROLE_USER]);

        $this->manager->persist($company);

        $portal = new Portal();
        $portal->setName(
            $company->getName()
        );
        $portal->setSlug(
            $this->slugService->createInitialPortalSlug(
                $company->getName()
            )
        );
        $portal->setDisplay(false);
        $portal->setCreatedAt($currentDateTime);
        $portal->setUpdatedAt($currentDateTime);
        $company->setPortal($portal);

        $this->manager->persist($portal);
        $this->manager->flush();

        return $company;
    }
}