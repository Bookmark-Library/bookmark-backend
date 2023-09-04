# bookmark-backend

Ceci est le back-end de ce [projet](https://github.com/Bookmark-Library/bookmark-frontend)

## Objectif du projet
BOOKMark est une application pratique et efficace qui permet aux utilisateurs de gérer leur bibliothèque personnelle en toute simplicité.
Grâce à cette application, les utilisateurs peuvent facilement cataloguer leur collection de livres, qu’il s’agisse de ceux qu’ils ont déjà lu, de leur pile à lire ou de leur liste d’envie.
En plus de cela, BOOKMark offre également la possibilité aux utilisateurs d’ajouter des notes, des commentaires et des citations pour chaque livre, ce qui permet de garder une trace de ses pensées et impressions sur les
différentes lectures effectuées.
Avec BOOKMark, il est facile de suivre ses progrès de lecture et de trouver rapidement les livres dont on a besoin.
Qu’il s’agisse de lecteurs assidus ou occasionnels, BOOKMark est une application utile pour tous les amoureux du livre.

## Installation du projet

- `composer install`
- Créer la base de données : `bin/console doctrine:database:create`
- Créer un fichier .env.local à la racine du projet
- Renseigner la base de données dans le .env.local
- Exécuter les migrations : `bin/console doctrine:migrations:migrate`
- Charger les fixtures : `bin/console doctrine:fixtures:load -n`
- Générer les clefs JWT : `php bin/console lexik:jwt:generate-keypair`
- Gérer les envois de mail avec un serveur SMTP local (Mailhog ou Maildev)
