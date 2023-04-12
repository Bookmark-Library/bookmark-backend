<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Book;
use DateTimeInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');



        for ($b = 1; $b <= 10; $b++) {

            $book = new Book();
            $book->setTitle($faker->sentence());
            $book->setEditor($faker->country());
            $book->setCollection($faker->city());
            $book->setPublicationDate(new DateTime($faker->date('Y')));
            $book->setSummary($faker->text());
            $book->setIsbn($faker->phoneNumber());
            $book->setPages($faker->randomNumber(3, false));
            $book->setPrice($faker->randomFloat(2));
            $book->setImage("https://catalogue.bnf.fr/couverture?&appName=NE&idArk=ark:/" .  $faker->randomNumber(5, true)  . "/cb44496975d&couverture=1");

            $manager->persist($book);
        }

        $manager->flush();
    }
}
