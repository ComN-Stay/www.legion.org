<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230818143852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, fk_team_id INT DEFAULT NULL, fk_user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, short_description LONGTEXT NOT NULL, content LONGTEXT NOT NULL, date_add DATE NOT NULL, visits INT NOT NULL, status TINYINT(1) NOT NULL, meta_name VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, meta_keywords VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_BFDD3168D943E582 (fk_team_id), INDEX IDX_BFDD31685741EEB9 (fk_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles_tags (articles_id INT NOT NULL, tags_id INT NOT NULL, INDEX IDX_354053611EBAF6CC (articles_id), INDEX IDX_354053618D7B4FB4 (tags_id), PRIMARY KEY(articles_id, tags_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168D943E582 FOREIGN KEY (fk_team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD31685741EEB9 FOREIGN KEY (fk_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE articles_tags ADD CONSTRAINT FK_354053611EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_tags ADD CONSTRAINT FK_354053618D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F3CBDC372');
        $this->addSql('ALTER TABLE transactional DROP variables');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64966517770 FOREIGN KEY (fk_gender_id) REFERENCES gender (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168D943E582');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD31685741EEB9');
        $this->addSql('ALTER TABLE articles_tags DROP FOREIGN KEY FK_354053611EBAF6CC');
        $this->addSql('ALTER TABLE articles_tags DROP FOREIGN KEY FK_354053618D7B4FB4');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE articles_tags');
        $this->addSql('ALTER TABLE transactional ADD variables JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64966517770');
    }
}
