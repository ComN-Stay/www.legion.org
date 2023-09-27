<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230927035053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adverts ADD nb_likes INT NOT NULL, ADD nb_shares INT NOT NULL');
        $this->addSql('ALTER TABLE articles ADD nb_likes INT NOT NULL, ADD nb_shares INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adverts DROP nb_likes, DROP nb_shares');
        $this->addSql('ALTER TABLE articles DROP nb_likes, DROP nb_shares');
    }
}
