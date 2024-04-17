<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240405004735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE projeto (id SERIAL NOT NULL, usuario_id INT NOT NULL, data_prazo TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, nome VARCHAR(255) NOT NULL, anotacoes TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A0559D94DB38439E ON projeto (usuario_id)');
        $this->addSql('COMMENT ON COLUMN projeto.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN projeto.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE tarefa (id SERIAL NOT NULL, usuario_id INT NOT NULL, projeto_id INT NOT NULL, descricao TEXT NOT NULL, hora TIMESTAMP(0) WITHOUT TIME ZONE, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, situacao INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_31B4CBADB38439E ON tarefa (usuario_id)');
        $this->addSql('CREATE INDEX IDX_31B4CBA43B58490 ON tarefa (projeto_id)');
        $this->addSql('COMMENT ON COLUMN tarefa.hora IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN tarefa.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN tarefa.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN tarefa.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE projeto ADD CONSTRAINT FK_A0559D94DB38439E FOREIGN KEY (usuario_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tarefa ADD CONSTRAINT FK_31B4CBADB38439E FOREIGN KEY (usuario_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tarefa ADD CONSTRAINT FK_31B4CBA43B58490 FOREIGN KEY (projeto_id) REFERENCES projeto (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE projeto DROP CONSTRAINT FK_A0559D94DB38439E');
        $this->addSql('ALTER TABLE tarefa DROP CONSTRAINT FK_31B4CBADB38439E');
        $this->addSql('ALTER TABLE tarefa DROP CONSTRAINT FK_31B4CBA43B58490');
        $this->addSql('DROP TABLE projeto');
        $this->addSql('DROP TABLE tarefa');
    }
}
