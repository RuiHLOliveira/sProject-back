<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231002153754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE atividade (id SERIAL NOT NULL, usuario_id INT NOT NULL, dia_id INT NOT NULL, descricao TEXT NOT NULL, hora TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, situacao INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_136416C2DB38439E ON atividade (usuario_id)');
        $this->addSql('CREATE INDEX IDX_136416C2AC1F7597 ON atividade (dia_id)');
        $this->addSql('COMMENT ON COLUMN atividade.hora IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN atividade.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN atividade.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN atividade.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE dia (id SERIAL NOT NULL, usuario_id INT NOT NULL, data_completa TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3E153BCEDB38439E ON dia (usuario_id)');
        $this->addSql('COMMENT ON COLUMN dia.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN dia.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE invitation_token (id SERIAL NOT NULL, user_id INT DEFAULT NULL, invitation_token VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, active BOOLEAN DEFAULT true NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_33FC351AA76ED395 ON invitation_token (user_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('ALTER TABLE atividade ADD CONSTRAINT FK_136416C2DB38439E FOREIGN KEY (usuario_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE atividade ADD CONSTRAINT FK_136416C2AC1F7597 FOREIGN KEY (dia_id) REFERENCES dia (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dia ADD CONSTRAINT FK_3E153BCEDB38439E FOREIGN KEY (usuario_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invitation_token ADD CONSTRAINT FK_33FC351AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('ALTER TABLE atividade DROP CONSTRAINT FK_136416C2DB38439E');
        $this->addSql('ALTER TABLE atividade DROP CONSTRAINT FK_136416C2AC1F7597');
        $this->addSql('ALTER TABLE dia DROP CONSTRAINT FK_3E153BCEDB38439E');
        $this->addSql('ALTER TABLE invitation_token DROP CONSTRAINT FK_33FC351AA76ED395');
        $this->addSql('DROP TABLE atividade');
        $this->addSql('DROP TABLE dia');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('DROP TABLE invitation_token');
        $this->addSql('DROP INDEX "primary"');
        $this->addSql('DROP TABLE "user"');
    }
}
