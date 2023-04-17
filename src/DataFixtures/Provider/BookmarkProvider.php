<?php

namespace App\DataFixtures\Provider;

class BookmarkProvider
{
    // 80 genres
    private $genres = [
        'Romans',
        'Romans Poche',
        'Polar & Thriller',
        'Fantasy & SF',
        'BD & Humour',
        'Manga',
        'Ados & Young adults',
        'Livres enfants',
        'Actualité Média et Société',
        'Dictionnaires & Langues',
        'Sciences Humaines',
        'Histoire',
        'Entreprise, Management',
        'Poésie & Théâtre',
        'Art, Cinéma & Musique',
        'Tourisme & Voyages',
        'Santé & Bien-être',
        'Cuisine & Vins',
        'Sports & Loisirs',
        'Nature, Animaux & Jardin',
        'Livres anglais & étrangers',
        'Livre Audio'
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
