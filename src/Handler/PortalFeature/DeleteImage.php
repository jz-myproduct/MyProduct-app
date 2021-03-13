<?php


namespace App\Handler\PortalFeature;


use App\Entity\Feature;
use App\Entity\File;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;

class DeleteImage
{

    /**
     * @var FileUploader
     */
    private $fileUploader;
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(FileUploader $fileUploader, EntityManagerInterface $manager)
    {
        $this->fileUploader = $fileUploader;
        $this->manager = $manager;
    }

    public function handle(File $file, Feature $feature)
    {
        $this->fileUploader->delete($file);

        // for some reason delete cascade={"remove"} doesn't work here
        $feature->getPortalFeature()->setImage(null);

        $this->manager->remove($file);

        $this->manager->flush();
    }

}