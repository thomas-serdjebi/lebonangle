<?php

namespace App\Service;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private SluggerInterface $slugger;

    private string $uploadsDirectory;

    public function __construct(SluggerInterface $slugger, string $uploadsDirectory)
    {
        $this->slugger = $slugger;
        $this->uploadsDirectory = $uploadsDirectory;
    }

    /**
     * Upload a file to the server
     *
     * @param UploadedFile $file
     * @return array(fileName: string, filePath: string)
     */
    public function upload(UploadedFile $file):array
    {
        $fileName = $this->generateUniqFileName($file);

        try {
            $file->move($this->uploadsDirectory, $fileName);
  
        } catch (FileException $fileException) {
           throw $fileException;
        }

        return [
            'fileName' => $fileName,
        ];
    }

    /**
     * Generate a unique file name
     *
     * @param UploadedFile $file
     * @return string
     */
    public function generateUniqFileName(UploadedFile $file):string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $originalFileNameSlugged = $this->slugger->slug(strtolower($originalFilename));

        $randomId = uniqId();

        return "{$originalFileNameSlugged}-{$randomId}.{$file->guessExtension()}";

    }


}
?>