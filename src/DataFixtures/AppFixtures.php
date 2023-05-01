<?php

namespace App\DataFixtures;

use App\DataFixtures\Provider\BookmarkProvider;
use Faker\Factory;
use App\Entity\Book;
use App\Entity\Genre;
use App\Entity\Author;
use App\Entity\Editorial;
use App\Entity\Library;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Connection;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{

    private $connection;
    private $slugger;

    public function __construct(Connection $connection, SluggerInterface $slugger)
    {
        $this->connection = $connection;
        $this->slugger = $slugger;
    }

    private function truncate()
    {
        // Disabling FK constraint checking 
        $this->connection->executeQuery('SET foreign_key_checks = 0');
        // Truncation
        $this->connection->executeQuery('TRUNCATE TABLE author');
        $this->connection->executeQuery('TRUNCATE TABLE book');
        $this->connection->executeQuery('TRUNCATE TABLE genre');
        $this->connection->executeQuery('TRUNCATE TABLE library');
        $this->connection->executeQuery('TRUNCATE TABLE user');
        $this->connection->executeQuery('TRUNCATE TABLE editorial');
        $this->connection->executeQuery('SET foreign_key_checks = 1');
    }

    public function load(ObjectManager $manager): void
    {
        // Truncation
        $this->truncate();

        // French Faker
        $faker = Factory::create('fr_FR');

        // Provider
        $bookmarkProvider = new BookmarkProvider();
        $faker->addProvider($bookmarkProvider);

        // Create Editorials
        $editorial = new Editorial();
        $editorial->setTitle($faker->sentence(mt_rand(1, 4)));
        $editorial->setContent($faker->text());
        $editorial->setImage($faker->imageUrl());
        $manager->persist($editorial);

        // Create Genres
        $providerGenres = $bookmarkProvider->allBookGenre();
        $genresList = [];

        for ($g = 0; $g < count($providerGenres); $g++) {
            $genre = new Genre();
            $genre->setName($providerGenres[$g]);
            $genresList[] = $genre;
            $manager->persist($genre);
        }

        // Create Authors
        $authorsList = [];
        for ($a = 1; $a <= 15; $a++) {
            $author = new Author();
            $author->setLastname($faker->lastName());
            $author->setFirstname($faker->firstName());
            $authorsList[] = $author;
            $manager->persist($author);
        }

        // Create Books
        $booksList = [];
        for ($b = 1; $b <= 25; $b++) {
            $book = new Book();
            $book->setTitle($faker->sentence(mt_rand(1, 4)));
            $book->setEditor($faker->country());
            $book->setCollection($faker->city());
            $book->setPublicationDate(($faker->date('Y')));
            $book->setSummary($faker->text());
            $book->setIsbn($faker->randomNumber(6, false));
            $book->setPages($faker->randomNumber(3, false));
            $book->setPrice($faker->randomFloat(2) . " EUR");
            $book->setImage("https://catalogue.bnf.fr/couverture?&appName=NE&idArk=ark:/" .  $faker->randomNumber(5, true)  . "/cb44496975d&couverture=1");
            $book->setSlug($this->slugger->slug($book->getTitle())->lower());

            // Add authors to book
            for ($a = 1; $a <= mt_rand(1, 3); $a++) {
                $randomAuthor = $authorsList[mt_rand(0, count($authorsList) - 1)];
                $book->addAuthor($randomAuthor);
            }

            // Add genres to book
            for ($g = 1; $g <= mt_rand(1, 3); $g++) {
                $randomGenre = $genresList[mt_rand(0, count($genresList) - 1)];
                $book->addGenre($randomGenre);
            }

            $booksList[] = $book;

            $manager->persist($book);
        }

        // Create USER
        $user = new User();
        $user->setEmail('user@user.com');
        $user->setAlias('user');
        $user->setPassword('$2y$13$Tg1.AyawGux8ykl.DpBCluOasX7EWXRrwLPcsZg8CzI5w2rxBQ.Bm');
        $user->setRoles(["ROLE_USER"]);
        $manager->persist($user);

        // add Library to USER
        for ($i = 0; $i <= 7; $i++) {
            $randomBook = $faker->unique()->randomElement($booksList);
            $library = new Library();
            $library->setUser($user);
            $library->setBook($randomBook);
            $library->setComment($faker->text());
            $library->setQuote($faker->text());
            $library->setRate(mt_rand(0, 5));
            $library->setFavorite($faker->boolean());
            $library->setPurchased($faker->boolean());
            $library->setWishlist($faker->boolean());
            $library->setFinished($faker->boolean());
            $manager->persist($library);
        }

        // Create EDITOR
        $editor = new User();
        $editor->setEmail('editor@editor.com');
        $editor->setAlias('editor');
        $editor->setPassword('$2y$13$AhHpIRbtx5PLrjjN/uTHjetL9sPlEnmHbh0tdXbU37OpQez55gkby');
        $editor->setRoles(["ROLE_EDITOR"]);
        $manager->persist($editor);

        // Create ADMIN
        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setAlias('admin');
        $admin->setPassword('$2y$13$vMnkj4LRxWckp/O251JkBueRG8z6nPTwODUI5hT13Sd8TwUqRolbK');
        $admin->setRoles(["ROLE_ADMIN"]);
        $manager->persist($admin);

        $manager->flush();
    }
}
