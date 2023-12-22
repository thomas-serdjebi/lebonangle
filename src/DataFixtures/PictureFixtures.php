<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Finder\Finder;
use App\Entity\Picture;
use App\Entity\Advert;
use App\Service\FileUploader;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class PictureFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{

    private ObjectManager $manager;
    private string $filestoUploadDirectory;
    private FileUploader $fileUploader;
    private Finder $finder;
    private array $fileArray;

    public function __construct(FileUploader $fileUploader, KernelInterface $kernel)
    {
        $this->fileUploader = $fileUploader;
        $this->filestoUploadDirectory = "{$kernel->getProjectDir()}/public/to-upload/";
    }

    
    public function load(ObjectManager $manager): void
    {
    
        $this->manager = $manager;

        $this->fileArray = $this->generateAdvertPicture();

        $manager->flush();

        $this->movePicturesFiles($this->fileArray);

    }

    private function generateAdvertPicture(): array
    {
        $this->finder = new Finder();
        $this->finder->files()->in($this->filestoUploadDirectory);	
        $fileArray = iterator_to_array($this->finder);

        $adverts = $this->manager->getRepository(Advert::class)->findAll();
        $adverts = array_slice($adverts, 0, 50);
        shuffle($adverts);

        foreach($fileArray as $key => $pictureFile) {

            $advertPicture = new Picture();

            $uploadedFile = new UploadedFile($pictureFile->getRealPath(), $pictureFile->getBasename(), null, null, true);

            [
                'fileName' => $pictureName,   
            ] = $this->fileUploader->upload($uploadedFile);
            
            // var_dump($advertPicture);
            $this->addReference("picture{$key}", $advertPicture);

            $advertPicture->setPath("uploads/{$pictureName}")
                          ->setCreatedAt(new \DateTimeImmutable())
                          ->setAdvert($adverts[array_rand($adverts)]);

            $this->manager->persist($advertPicture);
        }

        return $fileArray;

 
    }

    public function getDependencies()
    {
        return [
            AdvertFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['picture'];
    }

    private function movePicturesFiles($fileArray){

        $lastFile = end($fileArray);

        foreach ($fileArray as $file) {
            $newFilePath = 'public/uploads/' . $file->getBasename();
            if (!empty($file->getRealPath()) && file_exists($file->getRealPath())) {
                rename($file->getRealPath(), $newFilePath);
            }

            if($file === $lastFile) {
                array_map('unlink', glob('public/to-upload/*'));
            }
        }
        
    }

}