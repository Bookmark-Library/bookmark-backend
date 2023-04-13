<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Book;
use App\Entity\Genre;
use App\Entity\Author;
use App\Entity\Library;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setAlias('test');
        $user->setPassword('$2y$13$Tg1.AyawGux8ykl.DpBCluOasX7EWXRrwLPcsZg8CzI5w2rxBQ.Bm');
        $user->setRoles(["ROLE_USER"]);
        $manager->persist($user);


        $genresList = [];
        for ($g = 1; $g <= 10; $g++) {
            $genre = new Genre();
            $genre->setName($faker->word());
            //$genre->setHomeOrder(0);
            $genresList[] = $genre;
            $manager->persist($genre);
        }

        $authorsList = [];
        for ($a = 1; $a <= 10; $a++) {
            $author = new Author();
            $author->setLastname($faker->lastName());
            $author->setFirstname($faker->firstName());
            $authorsList[] = $author;

            $manager->persist($author);
        }

        for ($b = 1; $b <= 10; $b++) {
            $book = new Book();
            $book->setTitle($faker->sentence());
            $book->setEditor($faker->country());
            $book->setCollection($faker->city());
            $book->setPublicationDate(($faker->date('Y')));
            $book->setSummary($faker->text());
            $book->setIsbn($faker->phoneNumber());
            $book->setPages($faker->randomNumber(3, false));
            $book->setPrice($faker->randomFloat(2));
            $book->setImage("https://catalogue.bnf.fr/couverture?&appName=NE&idArk=ark:/" .  $faker->randomNumber(5, true)  . "/cb44496975d&couverture=1");

            for ($a = 1; $a <= mt_rand(1, 3); $a++) {
                $randomAuthor = $authorsList[mt_rand(0, count($authorsList) - 1)];
                $book->addAuthor($randomAuthor);
            }

            for ($g = 1; $g <= mt_rand(1, 3); $g++) {
                $randomGenre = $genresList[mt_rand(0, count($genresList) - 1)];
                $book->addGenre($randomGenre);
            }

            $library = new Library();
            $library->setUser($user);
            $library->setBook($book);
            $library->setFinished(true);
            $manager->persist($library);

            $manager->persist($book);
        }



        $manager->flush();
    }
}
