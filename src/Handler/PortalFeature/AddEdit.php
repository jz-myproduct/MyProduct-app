<?php


namespace App\Handler\PortalFeature;


use App\Entity\Feature;
use App\Entity\File;
use App\Entity\PortalFeature;
use App\Services\FileUploader;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\DateTime;

class AddEdit
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var FileUploader
     */
    private $fileUploader;

    public function __construct(FileUploader $fileUploader, EntityManagerInterface $manager)
    {
        $this->fileUploader = $fileUploader;
        $this->manager = $manager;
    }

    public function handle(PortalFeature $portalFeature, Feature $feature, UploadedFile $uploadedFile = null)
    {
        // TODO refactor to private functions

        $currentDateTime = new \DateTime();

        // handle uploaded image
        if($uploadedFile)
        {
            try{
                $imageFileName = $this->fileUploader->upload($uploadedFile);
                
                $file = new File();
                $file->setName($imageFileName);

                $this->manager->persist($file);

            } catch (\Exception $e){
                return false;
            }

            $portalFeature->setImage($file);
        }

        // handle actions common both new and edited portal feature
        $portalFeature->setUpdatedAt(new \DateTime());
        $portalFeature->setSlug(
            $portalFeature->getName()
        );

        //handle new portal feature
        if($feature)
        {
            $portalFeature->setFeedbackCount(0);
            $portalFeature->setCreatedAt($currentDateTime);
            $portalFeature->setFeature($feature);

            $this->manager->persist($portalFeature);
        }

        $this->manager->flush();

        return $portalFeature;
    }
}