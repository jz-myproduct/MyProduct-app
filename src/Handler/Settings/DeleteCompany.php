<?php


namespace App\Handler\Settings;


use App\Entity\Company;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DeleteCompany
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function handle(Company $company)
    {
        $session = new Session();
        $session->invalidate();

        $this->manager->remove($company);
        $this->manager->flush();
    }

}