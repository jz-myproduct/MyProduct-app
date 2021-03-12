<?php


namespace App\Handler\PortalFeature;


use App\Entity\PortalFeature;
use App\Services\FileUploader;
use App\Services\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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

    public function handle(PortalFeature $portalFeature, UploadedFile $imageFile)
    {

        if($imageFile){

            if(! $imageFileName = $this->fileUploader->upload($imageFile)){
                return false;
            }

        }

        $portalFeature->setUpdatedAt(new \DateTime());
        $portalFeature->setSlug(
          $portalFeature->getName()
        );

        if($imageFile){
            $portalFeature->setImage($imageFileName);
        }

        $this->manager->flush();
    }


}