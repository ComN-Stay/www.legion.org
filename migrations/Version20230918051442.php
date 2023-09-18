<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230918051442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE consents (id INT AUTO_INCREMENT NOT NULL, fk_user_id INT NOT NULL, fk_type_id INT NOT NULL, date_add DATE NOT NULL, version VARCHAR(20) DEFAULT NULL, INDEX IDX_6DACD675741EEB9 (fk_user_id), INDEX IDX_6DACD673563B1BF (fk_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pages_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, type VARCHAR(50) NOT NULL, has_version TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE consents ADD CONSTRAINT FK_6DACD675741EEB9 FOREIGN KEY (fk_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE consents ADD CONSTRAINT FK_6DACD673563B1BF FOREIGN KEY (fk_type_id) REFERENCES pages_types (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consents DROP FOREIGN KEY FK_6DACD675741EEB9');
        $this->addSql('ALTER TABLE consents DROP FOREIGN KEY FK_6DACD673563B1BF');
        $this->addSql('DROP TABLE consents');
        $this->addSql('DROP TABLE pages_types');
    }
}
