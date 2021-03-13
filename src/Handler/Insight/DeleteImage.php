<?php


namespace App\Handler\Insight;


use App\Entity\Feature;
use App\Entity\File;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

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

        $this->manager->remove($file);

        $this->manager->flush();
    }
}