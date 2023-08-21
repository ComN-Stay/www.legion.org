<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230820065455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE articles_medias (id INT AUTO_INCREMENT NOT NULL, fk_article_id INT NOT NULL, file VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, INDEX IDX_A8EA7BAC82FA4C0F (fk_article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE articles_medias ADD CONSTRAINT FK_A8EA7BAC82FA4C0F FOREIGN KEY (fk_article_id) REFERENCES articles (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles_medias DROP FOREIGN KEY FK_A8EA7BAC82FA4C0F');
        $this->addSql('DROP TABLE articles_medias');
    }
}
