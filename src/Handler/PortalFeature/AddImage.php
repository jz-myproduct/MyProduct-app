<?php


namespace App\Handler\PortalFeature;


use App\Entity\File;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AddImage
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

    public function handle(UploadedFile $uploadedFile)
    {
        /*
        try {
            $imageFileName = $this->fileUploader->upload($uploadedFile);
        } catch (\Exception $e) {
            throw new FileException('Image cannot be uploaded');
        }
        */

        throw new FileException('Image cannot be uploaded');

        $file = new File();
        $file->setName($imageFileName);

        $this->manager->persist($file);
        $this->manager->flush();

        return $file;
    }

}