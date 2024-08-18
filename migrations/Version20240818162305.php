<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240818162305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE inbox_item (id SERIAL NOT NULL, usuario_id INT NOT NULL, nome TEXT NOT NULL, origem VARCHAR(255) NOT NULL, categoria SMALLINT NOT NULL, acao TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D684AB84DB38439E ON inbox_item (usuario_id)');
        $this->addSql('COMMENT ON COLUMN inbox_item.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN inbox_item.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN inbox_item.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE inbox_item ADD CONSTRAINT FK_D684AB84DB38439E FOREIGN KEY (usuario_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE inbox_item DROP CONSTRAINT FK_D684AB84DB38439E');
        $this->addSql('DROP TABLE inbox_item');
    }
}
