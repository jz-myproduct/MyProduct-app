<?php


namespace App\Handler\Portal;


use App\Entity\Portal;
use App\Services\SlugService;
use Doctrine\ORM\EntityManagerInterface;

class Edit
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var SlugService
     */
    private $slugService;

    public function __construct(EntityManagerInterface $manager, SlugService $slugService)
    {
        $this->manager = $manager;
        $this->slugService = $slugService;
    }

    public function handle(Portal $portal)
    {
        $portal->setSlug(
            $this->slugService->createPortalSlug($portal)
        );
        $portal->setUpdatedAt(new \DateTime());

        $this->manager->flush();
    }

}