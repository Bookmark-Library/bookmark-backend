<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230413121502 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE library ADD `check` TINYINT(1) DEFAULT 0 NOT NULL, DROP `read`, CHANGE purchased purchased TINYINT(1) DEFAULT 0 NOT NULL, CHANGE favorite favorite TINYINT(1) DEFAULT 0 NOT NULL, CHANGE wishlist wishlist TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE library ADD `read` TINYINT(1) NOT NULL, DROP `check`, CHANGE purchased purchased TINYINT(1) NOT NULL, CHANGE favorite favorite TINYINT(1) NOT NULL, CHANGE wishlist wishlist TINYINT(1) NOT NULL');
    }
}
