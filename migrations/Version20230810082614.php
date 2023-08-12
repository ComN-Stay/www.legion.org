<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230810082614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adverts DROP FOREIGN KEY FK_8C88E77767F5D045');
        $this->addSql('ALTER TABLE adverts DROP FOREIGN KEY FK_8C88E777BD6B178D');
        $this->addSql('ALTER TABLE adverts ADD CONSTRAINT FK_8C88E77767F5D045 FOREIGN KEY (fk_company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE adverts ADD CONSTRAINT FK_8C88E777BD6B178D FOREIGN KEY (fk_pets_type_id) REFERENCES pets_type (id)');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F3CBDC272');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F3CBDC372');
        $this->addSql('ALTER TABLE medias DROP FOREIGN KEY FK_12D2AF81B1271B26');
        $this->addSql('ALTER TABLE medias ADD CONSTRAINT FK_12D2AF81B1271B26 FOREIGN KEY (fk_advert_id) REFERENCES adverts (id)');
        $this->addSql('ALTER TABLE reset_password_request CHANGE user_id user_id INT DEFAULT NULL, CHANGE team_id team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64966517770');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adverts DROP FOREIGN KEY FK_8C88E777BD6B178D');
        $this->addSql('ALTER TABLE adverts DROP FOREIGN KEY FK_8C88E77767F5D045');
        $this->addSql('ALTER TABLE adverts ADD CONSTRAINT FK_8C88E777BD6B178D FOREIGN KEY (fk_pets_type_id) REFERENCES pets_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE adverts ADD CONSTRAINT FK_8C88E77767F5D045 FOREIGN KEY (fk_company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE medias DROP FOREIGN KEY FK_12D2AF81B1271B26');
        $this->addSql('ALTER TABLE medias ADD CONSTRAINT FK_12D2AF81B1271B26 FOREIGN KEY (fk_advert_id) REFERENCES adverts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reset_password_request CHANGE user_id user_id INT NOT NULL, CHANGE team_id team_id INT NOT NULL');
    }
}
