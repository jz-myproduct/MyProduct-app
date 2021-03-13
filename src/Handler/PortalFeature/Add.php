<?php


namespace App\Handler\PortalFeature;


use App\Entity\Feature;
use App\Entity\File;
use App\Entity\PortalFeature;
use App\Services\FileUploader;
use App\Services\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Add
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var SlugService
     */
    private $slugService;
    /**
     * @var FileUploader
     */
    private $fileUploader;
    /**
     * @var AddImage
     */
    private $handler;

    public function __construct(
        EntityManagerInterface $manager,
        FileUploader $fileUploader,
        SlugService $slugService,
        AddImage $handler)
    {
        $this->manager = $manager;
        $this->fileUploader = $fileUploader;
        $this->slugService = $slugService;
        $this->handler = $handler;
    }

    public function handle(PortalFeature $portalFeature, Feature $feature, UploadedFile $imageFile = null)
    {
        dump($imageFile);

        if($imageFile)
        {
            dump('ahoj');

            try{
                $file = $this->handler->handle($imageFile);
            } catch (FileException $e){
                return false;
            }

            $portalFeature->setImage($file);
        }

        $currentDateTime = new \DateTime();



        $portalFeature->setFeedbackCount(0);
        $portalFeature->setSlug(
            $this->slugService->createCommonSlug(
                $portalFeature->getName()
            )
        );

        $portalFeature->setFeature($feature);

        $portalFeature->setCreatedAt($currentDateTime);
        $portalFeature->setUpdatedAt($currentDateTime);

        $this->manager->persist($portalFeature);
        $this->manager->flush();

        return $portalFeature;

    }

}