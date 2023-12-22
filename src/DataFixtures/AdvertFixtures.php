<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Advert;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use App\DataFixtures\CategoryFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class AdvertFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    private ObjectManager $manager;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->generateAdverts(130);

        $this->manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['advert'];
    }

    private function generateAdverts($number):void
    {

        $categories = $this->manager->getRepository(Category::class)->findAll();

        $faker = Faker\Factory::create('fr_FR');

        for($i = 0 ; $i < $number ; $i ++) {

            $advert = [];

            $advert[$i] = new Advert();

            [
                'dateObject' => $dateObject,
                'dateString'=> $dateString
            ] = $this->generateRandomDateBetweenRange('01/01/2023', '01/12/2023') ;

            $firstname = $faker->firstName;
            $lastname = $faker->lastName;
            $name = $firstname." ".$lastname;
            $email = $firstname.".".$lastname."@gmail.com";

            $advert[$i]->setTitle($faker->word."du {$dateString}")
            ->setContent($faker->paragraph())
            ->setAuthor($name)
            ->setEmail($email)
            ->setPrice($faker->numberBetween(1, 1000000))
            ->setState('draft')
            ->setCreatedAt($dateObject)
            ->setPublishedAt(null)
            ->setCategory($categories[array_rand($categories)]);


            $this->manager->persist($advert[$i]);

        }
    }

    private function generateRandomDateBetweenRange(string $start,string  $end):array
    {
        $startDateTimestamp = (\Datetime::createFromFormat('d/m/Y', $start))->getTimesTamp();
        $endDateTimestamp = (\Datetime::createFromFormat('d/m/Y', $end))->getTimesTamp();

        $randomTimestamp = mt_rand($startDateTimestamp, $endDateTimestamp);

        $dateTime = (new \DateTime())->setTimesTamp($randomTimestamp);

        return [
            'dateObject' => $dateTime,
            'dateString' => $dateTime->format('d-m-Y')
        ];
    }
}
