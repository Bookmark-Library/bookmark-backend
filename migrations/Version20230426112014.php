<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230426112014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE library ADD genre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE library ADD CONSTRAINT FK_A18098BC4296D31F FOREIGN KEY (genre_id) REFERENCES genre (id)');
        $this->addSql('CREATE INDEX IDX_A18098BC4296D31F ON library (genre_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE library DROP FOREIGN KEY FK_A18098BC4296D31F');
        $this->addSql('DROP INDEX IDX_A18098BC4296D31F ON library');
        $this->addSql('ALTER TABLE library DROP genre_id');
    }
}
