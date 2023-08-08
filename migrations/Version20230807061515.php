<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230807061515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adverts (id INT AUTO_INCREMENT NOT NULL, fk_pets_type_id INT NOT NULL, fk_company_id INT NOT NULL, title VARCHAR(255) NOT NULL, short_description LONGTEXT NOT NULL, description LONGTEXT NOT NULL, lof TINYINT(1) NOT NULL, lof_number INT DEFAULT NULL, lof_identifier INT DEFAULT NULL, lof_father_name VARCHAR(75) DEFAULT NULL, lof_father_identifier INT DEFAULT NULL, lof_mother_name VARCHAR(75) DEFAULT NULL, lof_mother_identifier INT DEFAULT NULL, name VARCHAR(75) DEFAULT NULL, birth_date DATE DEFAULT NULL, identified TINYINT(1) NOT NULL, vaccinated TINYINT(1) NOT NULL, price DOUBLE PRECISION DEFAULT NULL, is_pro TINYINT(1) NOT NULL, INDEX IDX_8C88E777BD6B178D (fk_pets_type_id), INDEX IDX_8C88E77767F5D045 (fk_company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adverts ADD CONSTRAINT FK_8C88E777BD6B178D FOREIGN KEY (fk_pets_type_id) REFERENCES pets_type (id)');
        $this->addSql('ALTER TABLE adverts ADD CONSTRAINT FK_8C88E77767F5D045 FOREIGN KEY (fk_company_id) REFERENCES company (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adverts DROP FOREIGN KEY FK_8C88E777BD6B178D');
        $this->addSql('ALTER TABLE adverts DROP FOREIGN KEY FK_8C88E77767F5D045');
        $this->addSql('DROP TABLE adverts');
    }
}
