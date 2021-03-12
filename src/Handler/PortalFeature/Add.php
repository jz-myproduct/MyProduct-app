<?php


namespace App\Handler\PortalFeature;


use App\Entity\Feature;
use App\Entity\File;
use App\Entity\PortalFeature;
use App\Services\FileUploader;
use App\Services\SlugService;
use Doctrine\ORM\EntityManagerInterface;
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

    public function __construct(EntityManagerInterface $manager, SlugService $slugService, FileUploader $fileUploader)
    {
        $this->manager = $manager;
        $this->slugService = $slugService;
        $this->fileUploader = $fileUploader;
    }

    public function handle(PortalFeature $portalFeature, Feature $feature, UploadedFile $imageFile = null)
    {

        if($imageFile){

            // TODO handle exception
            if(! $imageFileName = $this->fileUploader->upload($imageFile)){
                return false;
            }

        }

        $currentDateTime = new \DateTime();

        $portalFeature->setFeedbackCount(0);
        $portalFeature->setSlug(
            $this->slugService->createCommonSlug(
                $portalFeature->getName()
            )
        );

        $portalFeature->setFeature($feature);

        if($imageFile){
            $file = new File();
            $file->setName($imageFileName);

            $this->manager->persist($file);

            $portalFeature->setImage($file);
        }

        $portalFeature->setCreatedAt($currentDateTime);
        $portalFeature->setUpdatedAt($currentDateTime);

        $this->manager->persist($portalFeature);
        $this->manager->flush();

    }

}