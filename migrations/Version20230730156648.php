<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230730156648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer ADD fk_gender_id INT NOT NULL, ADD fk_company_id INT NOT NULL, ADD firstname VARCHAR(75) NOT NULL, ADD lastname VARCHAR(75) NOT NULL, ADD status TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E0966517770 FOREIGN KEY (fk_gender_id) REFERENCES gender (id)');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E0967F5D045 FOREIGN KEY (fk_company_id) REFERENCES company (id)');
        $this->addSql('CREATE INDEX IDX_81398E0966517770 ON customer (fk_gender_id)');
        $this->addSql('CREATE INDEX IDX_81398E0967F5D045 ON customer (fk_company_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E0966517770');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E0967F5D045');
        $this->addSql('DROP INDEX IDX_81398E0966517770 ON customer');
        $this->addSql('DROP INDEX IDX_81398E0967F5D045 ON customer');
        $this->addSql('ALTER TABLE customer DROP fk_gender_id, DROP fk_company_id, DROP firstname, DROP lastname, DROP status');
    }
}
