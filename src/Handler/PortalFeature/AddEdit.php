<?php


namespace App\Handler\PortalFeature;


use App\Entity\Feature;
use App\Entity\File;
use App\Entity\PortalFeature;
use App\FormRequest\PortalFeature\AddEditRequest;
use App\Service\FileUploader;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
    /**
     * @var SlugService
     */
    private $slugService;

    public function __construct(FileUploader $fileUploader, EntityManagerInterface $manager, SlugService $slugService)
    {
        $this->fileUploader = $fileUploader;
        $this->manager = $manager;
        $this->slugService = $slugService;
    }

    public function handle(AddEditRequest $request, PortalFeature $portalFeature, Feature $feature, UploadedFile $uploadedFile = null)
    {

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
        $portalFeature->setName($request->name);
        $portalFeature->setDescription($request->description);
        $portalFeature->setDisplay($request->display);
        $portalFeature->setState($request->state);
        $portalFeature->setSlug(
            $this->slugService->createCommonSlug($request->name)
        );
        $feature->setUpdatedAt(new \DateTime());

        //handle new portal feature
        if(! $portalFeature->getFeature())
        {
            $portalFeature->setFeedbackCount(0);
            $portalFeature->setCreatedAt(new \DateTime());
            $portalFeature->setFeature($feature);

            $this->manager->persist($portalFeature);
        }

        $this->manager->flush();

        return $portalFeature;
    }
}