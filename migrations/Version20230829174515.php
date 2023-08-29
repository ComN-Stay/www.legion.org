<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230829174515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adverts (id INT AUTO_INCREMENT NOT NULL, fk_pets_type_id INT NOT NULL, fk_company_id INT NOT NULL, fk_status_id INT NOT NULL, title VARCHAR(255) NOT NULL, short_description LONGTEXT NOT NULL, description LONGTEXT NOT NULL, lof TINYINT(1) NOT NULL, lof_number INT DEFAULT NULL, lof_identifier INT DEFAULT NULL, lof_father_name VARCHAR(75) DEFAULT NULL, lof_father_identifier INT DEFAULT NULL, lof_mother_name VARCHAR(75) DEFAULT NULL, lof_mother_identifier INT DEFAULT NULL, name VARCHAR(75) DEFAULT NULL, birth_date DATE DEFAULT NULL, identified TINYINT(1) NOT NULL, vaccinated TINYINT(1) NOT NULL, price DOUBLE PRECISION DEFAULT NULL, is_pro TINYINT(1) NOT NULL, visits SMALLINT NOT NULL, INDEX IDX_8C88E777BD6B178D (fk_pets_type_id), INDEX IDX_8C88E77767F5D045 (fk_company_id), INDEX IDX_8C88E777AAED72D (fk_status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, fk_team_id INT DEFAULT NULL, fk_user_id INT DEFAULT NULL, fk_status_id INT NOT NULL, title VARCHAR(255) NOT NULL, short_description LONGTEXT NOT NULL, content LONGTEXT NOT NULL, date_add DATE NOT NULL, visits INT NOT NULL, meta_name VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, meta_keywords VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, INDEX IDX_BFDD3168D943E582 (fk_team_id), INDEX IDX_BFDD31685741EEB9 (fk_user_id), INDEX IDX_BFDD3168AAED72D (fk_status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles_tags (articles_id INT NOT NULL, tags_id INT NOT NULL, INDEX IDX_354053611EBAF6CC (articles_id), INDEX IDX_354053618D7B4FB4 (tags_id), PRIMARY KEY(articles_id, tags_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles_medias (id INT AUTO_INCREMENT NOT NULL, fk_article_id INT NOT NULL, file VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, INDEX IDX_A8EA7BAC82FA4C0F (fk_article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, fk_company_type_id INT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(80) NOT NULL, phone VARCHAR(25) DEFAULT NULL, address VARCHAR(255) NOT NULL, additional_address VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(15) NOT NULL, town VARCHAR(80) NOT NULL, status TINYINT(1) NOT NULL, longitude DOUBLE PRECISION DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, facebook VARCHAR(255) DEFAULT NULL, tweeter VARCHAR(255) DEFAULT NULL, instagram VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, short_description LONGTEXT DEFAULT NULL, INDEX IDX_4FBF094F3CBDC271 (fk_company_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gender (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(25) NOT NULL, short_name VARCHAR(5) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medias (id INT AUTO_INCREMENT NOT NULL, fk_advert_id INT NOT NULL, title VARCHAR(100) NOT NULL, filename VARCHAR(255) NOT NULL, INDEX IDX_12D2AF81B1271B26 (fk_advert_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE petitions (id INT AUTO_INCREMENT NOT NULL, fk_user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, link VARCHAR(255) NOT NULL, picture VARCHAR(255) DEFAULT NULL, date_add DATE NOT NULL, status TINYINT(1) NOT NULL, INDEX IDX_82F8C73A5741EEB9 (fk_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pets_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(75) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, team_id INT DEFAULT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), INDEX IDX_7CE748A296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statistics (id INT AUTO_INCREMENT NOT NULL, day DATE NOT NULL, visits INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(50) NOT NULL, article_qte SMALLINT NOT NULL, slug VARCHAR(255) NOT NULL, meta_name VARCHAR(75) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, meta_keyword VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, fk_gender_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, phone VARCHAR(25) DEFAULT NULL, UNIQUE INDEX UNIQ_C4E0A61FE7927C74 (email), INDEX IDX_C4E0A61F66517770 (fk_gender_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transactional (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, template VARCHAR(50) NOT NULL, subject VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, fk_gender_id INT NOT NULL, fk_company_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(75) NOT NULL, lastname VARCHAR(75) NOT NULL, token VARCHAR(255) NOT NULL, is_company_admin TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D64966517770 (fk_gender_id), INDEX IDX_8D93D64967F5D045 (fk_company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE visitors (id INT AUTO_INCREMENT NOT NULL, ip VARCHAR(100) NOT NULL, date_add DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adverts ADD CONSTRAINT FK_8C88E777BD6B178D FOREIGN KEY (fk_pets_type_id) REFERENCES pets_type (id)');
        $this->addSql('ALTER TABLE adverts ADD CONSTRAINT FK_8C88E77767F5D045 FOREIGN KEY (fk_company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE adverts ADD CONSTRAINT FK_8C88E777AAED72D FOREIGN KEY (fk_status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168D943E582 FOREIGN KEY (fk_team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD31685741EEB9 FOREIGN KEY (fk_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168AAED72D FOREIGN KEY (fk_status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE articles_tags ADD CONSTRAINT FK_354053611EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_tags ADD CONSTRAINT FK_354053618D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_medias ADD CONSTRAINT FK_A8EA7BAC82FA4C0F FOREIGN KEY (fk_article_id) REFERENCES articles (id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F3CBDC271 FOREIGN KEY (fk_company_type_id) REFERENCES company_type (id)');
        $this->addSql('ALTER TABLE medias ADD CONSTRAINT FK_12D2AF81B1271B26 FOREIGN KEY (fk_advert_id) REFERENCES adverts (id)');
        $this->addSql('ALTER TABLE petitions ADD CONSTRAINT FK_82F8C73A5741EEB9 FOREIGN KEY (fk_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748A296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F66517770 FOREIGN KEY (fk_gender_id) REFERENCES gender (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64966517770 FOREIGN KEY (fk_gender_id) REFERENCES gender (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64967F5D045 FOREIGN KEY (fk_company_id) REFERENCES company (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adverts DROP FOREIGN KEY FK_8C88E777BD6B178D');
        $this->addSql('ALTER TABLE adverts DROP FOREIGN KEY FK_8C88E77767F5D045');
        $this->addSql('ALTER TABLE adverts DROP FOREIGN KEY FK_8C88E777AAED72D');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168D943E582');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD31685741EEB9');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168AAED72D');
        $this->addSql('ALTER TABLE articles_tags DROP FOREIGN KEY FK_354053611EBAF6CC');
        $this->addSql('ALTER TABLE articles_tags DROP FOREIGN KEY FK_354053618D7B4FB4');
        $this->addSql('ALTER TABLE articles_medias DROP FOREIGN KEY FK_A8EA7BAC82FA4C0F');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F3CBDC271');
        $this->addSql('ALTER TABLE medias DROP FOREIGN KEY FK_12D2AF81B1271B26');
        $this->addSql('ALTER TABLE petitions DROP FOREIGN KEY FK_82F8C73A5741EEB9');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748A296CD8AE');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61F66517770');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64966517770');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64967F5D045');
        $this->addSql('DROP TABLE adverts');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE articles_tags');
        $this->addSql('DROP TABLE articles_medias');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE company_type');
        $this->addSql('DROP TABLE gender');
        $this->addSql('DROP TABLE medias');
        $this->addSql('DROP TABLE petitions');
        $this->addSql('DROP TABLE pets_type');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE statistics');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE transactional');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE visitors');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
