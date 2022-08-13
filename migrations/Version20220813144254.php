<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220813144254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invitation_token (id SERIAL NOT NULL, user_id INT DEFAULT NULL, invitation_token VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, active BOOLEAN DEFAULT true NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_33FC351AA76ED395 ON invitation_token (user_id)');
        $this->addSql('ALTER TABLE invitation_token ADD CONSTRAINT FK_33FC351AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE invitation_token DROP CONSTRAINT FK_33FC351AA76ED395');
        $this->addSql('DROP TABLE invitation_token');
    }
}
