<?php

namespace App\DataFixtures\Provider;

class BookmarkProvider
{
    private $genres = [
        'Animaux',
        'Art',
        'Aventure',
        'Bande Dessinée',
        'Biographie',
        'Cinéma',
        'Comédie',
        'Conte',
        'Cuisine',
        'Dictionnaires & encyclopédies',
        'Drame',
        'Épistolaires',
        'Essai',
        'Fable',
        'Fantastique',
        'Fantasy',
        'Fiction',
        'Géographie',
        'Histoire',
        'Horreur',
        'Humour',
        'Langues',
        'Livres en langues étrangères',
        'Livre Audio',
        'Livre pour enfants',
        'Loisirs',
        'Management',
        'Manga',
        'Médias & Société',
        'Merveilleux',
        'Musique',
        'Mystère',
        'Nature',
        'Nouvelle',
        'Philosophie',
        'Poésie',
        'Polar',
        'Psychologie',
        'Religions',
        'Roman',
        'Romance',
        'Santé & Bien-être',
        'Sciences',
        'Science Fiction',
        'Sciences Humaines',
        'Sports',
        'Technologies',
        'Théâtre',
        'Tragédie',
        'Thriller',
        'Voyage',
        'Young adults'
    ];

    /**
     * Give back random genre
     */
    public function bookGenre()
    {
        return $this->genres[array_rand($this->genres)];
    }

    /**
     * Give back all genres
     */
    public function allBookGenre()
    {
        return $this->genres;
    }
}
