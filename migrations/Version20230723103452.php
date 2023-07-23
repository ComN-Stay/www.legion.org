<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230723103452 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, fk_company_type_id INT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(80) NOT NULL, phone VARCHAR(25) DEFAULT NULL, address VARCHAR(255) NOT NULL, additional_address VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(15) NOT NULL, town VARCHAR(80) NOT NULL, status TINYINT(1) NOT NULL, longitude DOUBLE PRECISION DEFAULT NULL, latitude DOUBLE PRECISION NOT NULL, INDEX IDX_4FBF094F3CBDC271 (fk_company_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F3CBDC271 FOREIGN KEY (fk_company_type_id) REFERENCES company_type (id)');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F66517770 FOREIGN KEY (fk_gender_id) REFERENCES gender (id)');
        $this->addSql('CREATE INDEX IDX_C4E0A61F66517770 ON team (fk_gender_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F3CBDC271');
        $this->addSql('DROP TABLE company');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F66517770');
        $this->addSql('DROP INDEX IDX_C4E0A61F66517770 ON team');
    }
}
