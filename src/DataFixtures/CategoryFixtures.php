<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Advert;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CategoryFixtures extends Fixture implements FixtureGroupInterface
{

    private ObjectManager $manager;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $number = 120;

        $this->generateCategories($number);

        $this->manager->flush();
    }

    public static function getGroups(): array
    {
        return ['category'];
    }

    private function generateCategories($number):void
    {
 
        $faker = Faker\Factory::create('fr_FR');
        $categories = [];

        for ($i = 0 ; $i < $number ; $i++) {
            $categories[$i] = new Category();
            $categories[$i]->setName($faker->word);
            $this->manager->persist($categories[$i]);
        } 
    }
}
