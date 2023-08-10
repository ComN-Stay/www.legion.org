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
        $this->addSql('CREATE TABLE `company` (
            `id` int NOT NULL,
            `fk_company_type_id` int NOT NULL,
            `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `email` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
            `phone` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `additional_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `zip_code` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
            `town` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
            `status` tinyint(1) NOT NULL,
            `longitude` double DEFAULT NULL,
            `latitude` double DEFAULT NULL,
            `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `facebook` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `tweeter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `instagram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `description` longtext COLLATE utf8mb4_unicode_ci,
            `short_description` longtext COLLATE utf8mb4_unicode_ci
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
        $this->addSql('ALTER TABLE `company` ADD PRIMARY KEY (`id`), ADD KEY `IDX_4FBF094F3CBDC271` (`fk_company_type_id`);');
        $this->addSql('ALTER TABLE `company` MODIFY `id` int NOT NULL AUTO_INCREMENT;');
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F3CBDC372 FOREIGN KEY (fk_company_type_id) REFERENCES company_type (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F3CBDC372');
    }
}
