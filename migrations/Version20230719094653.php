<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230719094653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gender (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(25) NOT NULL, short_name VARCHAR(5) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE team ADD fk_gender_id INT NOT NULL, ADD firstname VARCHAR(50) NOT NULL, ADD lastname VARCHAR(50) NOT NULL, ADD phone VARCHAR(25) DEFAULT NULL');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F66517770 FOREIGN KEY (fk_gender_id) REFERENCES gender (id)');
        $this->addSql('CREATE INDEX IDX_C4E0A61F66517770 ON team (fk_gender_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE gender');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F66517770');
        $this->addSql('DROP INDEX IDX_C4E0A61F66517770 ON team');
        $this->addSql('ALTER TABLE team DROP fk_gender_id, DROP firstname, DROP lastname, DROP phone');
    }
}
