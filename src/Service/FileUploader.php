<?php


namespace App\Service;


use App\Entity\File;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    /**
     * @var SlugService
     */
    private $slugService;
    private $uploadDirectory;
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct($uploadDirectory, SlugService $slugService, Filesystem $filesystem)
    {
        $this->uploadDirectory = $uploadDirectory;
        $this->slugService = $slugService;
        $this->filesystem = $filesystem;
    }

    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugService->createCommonSlug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->uploadDirectory, $fileName);
        } catch (FileException $e){
            throw new \Exception(sprintf('Cannot upload a file'));
        }

        return $fileName;
    }

    public function delete(File $file)
    {
        $this->filesystem->remove(
            $this->getFilePath($file)
        );
    }

    public function getFilePath(File $file)
    {
        return $this->uploadDirectory.'/'.$file->getName();
    }


}