<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220811021011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE atividade (id SERIAL NOT NULL, hora_id INT NOT NULL, usuario_id INT NOT NULL, descricao TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_136416C2451F5F98 ON atividade (hora_id)');
        $this->addSql('CREATE INDEX IDX_136416C2DB38439E ON atividade (usuario_id)');
        $this->addSql('COMMENT ON COLUMN atividade.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN atividade.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN atividade.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE dia (id SERIAL NOT NULL, usuario_id INT NOT NULL, data_completa TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3E153BCEDB38439E ON dia (usuario_id)');
        $this->addSql('COMMENT ON COLUMN dia.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN dia.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE hora (id SERIAL NOT NULL, dia_id INT NOT NULL, usuario_id INT NOT NULL, hora VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BBE1C657AC1F7597 ON hora (dia_id)');
        $this->addSql('CREATE INDEX IDX_BBE1C657DB38439E ON hora (usuario_id)');
        $this->addSql('COMMENT ON COLUMN hora.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN hora.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN hora.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('ALTER TABLE atividade ADD CONSTRAINT FK_136416C2451F5F98 FOREIGN KEY (hora_id) REFERENCES hora (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE atividade ADD CONSTRAINT FK_136416C2DB38439E FOREIGN KEY (usuario_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dia ADD CONSTRAINT FK_3E153BCEDB38439E FOREIGN KEY (usuario_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hora ADD CONSTRAINT FK_BBE1C657AC1F7597 FOREIGN KEY (dia_id) REFERENCES dia (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hora ADD CONSTRAINT FK_BBE1C657DB38439E FOREIGN KEY (usuario_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE hora DROP CONSTRAINT FK_BBE1C657AC1F7597');
        $this->addSql('ALTER TABLE atividade DROP CONSTRAINT FK_136416C2451F5F98');
        $this->addSql('ALTER TABLE atividade DROP CONSTRAINT FK_136416C2DB38439E');
        $this->addSql('ALTER TABLE dia DROP CONSTRAINT FK_3E153BCEDB38439E');
        $this->addSql('ALTER TABLE hora DROP CONSTRAINT FK_BBE1C657DB38439E');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP TABLE atividade');
        $this->addSql('DROP TABLE dia');
        $this->addSql('DROP TABLE hora');
        $this->addSql('DROP TABLE "user"');
    }
}
