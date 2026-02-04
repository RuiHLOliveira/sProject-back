<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260204182104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE personagem (id SERIAL NOT NULL, usuario_id INT NOT NULL, createdat TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deletedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, nome VARCHAR(255) NOT NULL, nivel SMALLINT DEFAULT 1 NOT NULL, experiencia BIGINT DEFAULT 0 NOT NULL, ouro BIGINT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AE9CE39DB38439E ON personagem (usuario_id)');
        $this->addSql('COMMENT ON COLUMN personagem.createdat IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN personagem.updatedat IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN personagem.deletedat IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE personagem_historico (id SERIAL NOT NULL, personagem_id INT NOT NULL, createdat TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deletedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, tipohistorico SMALLINT NOT NULL, texto TEXT NOT NULL, dadosjson VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1C3A845BC154BB39 ON personagem_historico (personagem_id)');
        $this->addSql('COMMENT ON COLUMN personagem_historico.createdat IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN personagem_historico.updatedat IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN personagem_historico.deletedat IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE recompensa (id SERIAL NOT NULL, createdat TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deletedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, nome VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN recompensa.createdat IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN recompensa.updatedat IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN recompensa.deletedat IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE recompensaacao (id SERIAL NOT NULL, recompensa_id INT NOT NULL, createdat TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deletedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, tipoatividade VARCHAR(255) NOT NULL, quantidade SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_83BA8CB4C213D7C1 ON recompensaacao (recompensa_id)');
        $this->addSql('COMMENT ON COLUMN recompensaacao.createdat IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN recompensaacao.updatedat IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN recompensaacao.deletedat IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE personagem ADD CONSTRAINT FK_AE9CE39DB38439E FOREIGN KEY (usuario_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE personagem_historico ADD CONSTRAINT FK_1C3A845BC154BB39 FOREIGN KEY (personagem_id) REFERENCES personagem (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE recompensaacao ADD CONSTRAINT FK_83BA8CB4C213D7C1 FOREIGN KEY (recompensa_id) REFERENCES recompensa (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE personagem DROP CONSTRAINT FK_AE9CE39DB38439E');
        $this->addSql('ALTER TABLE personagem_historico DROP CONSTRAINT FK_1C3A845BC154BB39');
        $this->addSql('ALTER TABLE recompensaacao DROP CONSTRAINT FK_83BA8CB4C213D7C1');
        $this->addSql('DROP TABLE personagem');
        $this->addSql('DROP TABLE personagem_historico');
        $this->addSql('DROP TABLE recompensa');
        $this->addSql('DROP TABLE recompensaacao');
    }
}
