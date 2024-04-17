<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240407101135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE historico (id SERIAL NOT NULL, usuario_id INT NOT NULL, modulo_id INT NOT NULL, modulo_tipo INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE, descricao TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8DAA356ADB38439E ON historico (usuario_id)');
        $this->addSql('COMMENT ON COLUMN historico.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN historico.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE historico ADD CONSTRAINT FK_8DAA356ADB38439E FOREIGN KEY (usuario_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE projeto ALTER data_prazo SET NOT NULL');
        $this->addSql('ALTER TABLE tarefa ALTER hora SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE historico DROP CONSTRAINT FK_8DAA356ADB38439E');
        $this->addSql('DROP TABLE historico');
        $this->addSql('ALTER TABLE projeto ALTER data_prazo DROP NOT NULL');
        $this->addSql('ALTER TABLE tarefa ALTER hora DROP NOT NULL');
    }
}
