<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230805052539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pets_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(75) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64966517770 FOREIGN KEY (fk_gender_id) REFERENCES gender (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64966517770 ON user (fk_gender_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE pets_type');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64966517770');
        $this->addSql('DROP INDEX IDX_8D93D64966517770 ON user');
    }
}
