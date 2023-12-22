<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\AdminUser;
use App\Entity\Picture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AdminUserFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{

    private ObjectManager $manager;
  

    
    public function load(ObjectManager $manager): void
    {
    
        $this->manager = $manager;

        $this->generateAdmin();

        $manager->flush();
    }

    private function generateAdmin(): void
    {
        

        for ($i = 0; $i < 5 ; $i++) {
            
            $admin = new AdminUser();

            $admin->setUsername("admin{$i}")
            ->setPlainPassword('Marseille13')
            ->setEmail('thomas@gmail.com')
            ->setRoles(['ROLE_ADMIN']);

            $this->manager->persist($admin);

        }
    }

    public function getDependencies()
    {
        return [
            PictureFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['adminUser'];
    }


}