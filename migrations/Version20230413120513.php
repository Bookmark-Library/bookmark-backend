<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230413120513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE library ADD `read` TINYINT(1) DEFAULT 0 NOT NULL, ADD purchased TINYINT(1) DEFAULT 0 NOT NULL, ADD favorite TINYINT(1) DEFAULT 0 NOT NULL, ADD wishlist TINYINT(1) DEFAULT 0 NOT NULL, DROP is_read, DROP is_purchased, DROP is_favorite, DROP is_wanted');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE library ADD is_read TINYINT(1) NOT NULL, ADD is_purchased TINYINT(1) NOT NULL, ADD is_favorite TINYINT(1) NOT NULL, ADD is_wanted TINYINT(1) NOT NULL, DROP `read`, DROP purchased, DROP favorite, DROP wishlist');
    }
}
