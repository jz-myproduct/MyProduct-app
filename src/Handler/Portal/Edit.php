<?php


namespace App\Handler\Portal;


use App\Entity\Portal;
use App\FormRequest\Portal\SettingsRequest;
use App\Service\SlugService;
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

    public function handle(SettingsRequest $request, Portal $portal)
    {

        $portal->setName($request->name);
        $portal->setSlug(
            $this->slugService->createPortalSlug($portal)
        );
        $portal->setDisplay($request->display);

        $portal->setUpdatedAt(new \DateTime());

        $this->manager->flush();
    }

}