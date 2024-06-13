<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240521115146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE habito (id SERIAL NOT NULL, usuario_id INT NOT NULL, descricao TEXT NOT NULL, hora TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, situacao INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B1407D75DB38439E ON habito (usuario_id)');
        $this->addSql('COMMENT ON COLUMN habito.hora IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN habito.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN habito.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN habito.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE habito ADD CONSTRAINT FK_B1407D75DB38439E FOREIGN KEY (usuario_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE habito DROP CONSTRAINT FK_B1407D75DB38439E');
        $this->addSql('DROP TABLE habito');
    }
}
