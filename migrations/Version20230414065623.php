<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230414065623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE library (id INT AUTO_INCREMENT NOT NULL, book_id INT NOT NULL, user_id INT NOT NULL, finished TINYINT(1) DEFAULT 0 NOT NULL, purchased TINYINT(1) DEFAULT 0 NOT NULL, favorite TINYINT(1) DEFAULT 0 NOT NULL, wishlist TINYINT(1) DEFAULT 0 NOT NULL, comment LONGTEXT DEFAULT NULL, quote LONGTEXT DEFAULT NULL, rate SMALLINT DEFAULT NULL, INDEX IDX_A18098BC16A2B381 (book_id), INDEX IDX_A18098BCA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, avatar VARCHAR(2048) DEFAULT NULL, alias VARCHAR(128) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE library ADD CONSTRAINT FK_A18098BC16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE library ADD CONSTRAINT FK_A18098BCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE library DROP FOREIGN KEY FK_A18098BC16A2B381');
        $this->addSql('ALTER TABLE library DROP FOREIGN KEY FK_A18098BCA76ED395');
        $this->addSql('DROP TABLE library');
        $this->addSql('DROP TABLE user');
    }
}
