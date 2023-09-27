<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230927050444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adverts_likes (adverts_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8FE3F867C5A3D550 (adverts_id), INDEX IDX_8FE3F867A76ED395 (user_id), PRIMARY KEY(adverts_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE adverts_shares (adverts_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6DFBA1B0C5A3D550 (adverts_id), INDEX IDX_6DFBA1B0A76ED395 (user_id), PRIMARY KEY(adverts_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles_likes (articles_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_A12866891EBAF6CC (articles_id), INDEX IDX_A1286689A76ED395 (user_id), PRIMARY KEY(articles_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles_shares (articles_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_2A67A5511EBAF6CC (articles_id), INDEX IDX_2A67A551A76ED395 (user_id), PRIMARY KEY(articles_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adverts_likes ADD CONSTRAINT FK_8FE3F867C5A3D550 FOREIGN KEY (adverts_id) REFERENCES adverts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE adverts_likes ADD CONSTRAINT FK_8FE3F867A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE adverts_shares ADD CONSTRAINT FK_6DFBA1B0C5A3D550 FOREIGN KEY (adverts_id) REFERENCES adverts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE adverts_shares ADD CONSTRAINT FK_6DFBA1B0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_likes ADD CONSTRAINT FK_A12866891EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_likes ADD CONSTRAINT FK_A1286689A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_shares ADD CONSTRAINT FK_2A67A5511EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_shares ADD CONSTRAINT FK_2A67A551A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adverts_likes DROP FOREIGN KEY FK_8FE3F867C5A3D550');
        $this->addSql('ALTER TABLE adverts_likes DROP FOREIGN KEY FK_8FE3F867A76ED395');
        $this->addSql('ALTER TABLE adverts_shares DROP FOREIGN KEY FK_6DFBA1B0C5A3D550');
        $this->addSql('ALTER TABLE adverts_shares DROP FOREIGN KEY FK_6DFBA1B0A76ED395');
        $this->addSql('ALTER TABLE articles_likes DROP FOREIGN KEY FK_A12866891EBAF6CC');
        $this->addSql('ALTER TABLE articles_likes DROP FOREIGN KEY FK_A1286689A76ED395');
        $this->addSql('ALTER TABLE articles_shares DROP FOREIGN KEY FK_2A67A5511EBAF6CC');
        $this->addSql('ALTER TABLE articles_shares DROP FOREIGN KEY FK_2A67A551A76ED395');
        $this->addSql('DROP TABLE adverts_likes');
        $this->addSql('DROP TABLE adverts_shares');
        $this->addSql('DROP TABLE articles_likes');
        $this->addSql('DROP TABLE articles_shares');
    }
}
