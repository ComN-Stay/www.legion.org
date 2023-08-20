<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230819051914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles ADD logo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168D943E582 FOREIGN KEY (fk_team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD31685741EEB9 FOREIGN KEY (fk_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F3CBDC372');
        $this->addSql('ALTER TABLE transactional DROP variables');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64966517770 FOREIGN KEY (fk_gender_id) REFERENCES gender (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168D943E582');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD31685741EEB9');
        $this->addSql('ALTER TABLE articles DROP logo');
        $this->addSql('ALTER TABLE transactional ADD variables JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64966517770');
    }
}
