<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230921055222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pages ADD fk_status_id INT NOT NULL');
        $this->addSql('ALTER TABLE pages ADD CONSTRAINT FK_2074E575AAED72D FOREIGN KEY (fk_status_id) REFERENCES status (id)');
        $this->addSql('CREATE INDEX IDX_2074E575AAED72D ON pages (fk_status_id)');
        $this->addSql('ALTER TABLE pages_types DROP version_number');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E575AAED72D');
        $this->addSql('DROP INDEX IDX_2074E575AAED72D ON pages');
        $this->addSql('ALTER TABLE pages DROP fk_status_id');
        $this->addSql('ALTER TABLE pages_types ADD version_number VARCHAR(15) DEFAULT NULL');
    }
}
