<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250204204432 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE projetofoto (id SERIAL NOT NULL, usuario_id INT NOT NULL, projeto_id INT NOT NULL, descricao TEXT NOT NULL, link TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3C2F6C4CDB38439E ON projetofoto (usuario_id)');
        $this->addSql('CREATE INDEX IDX_3C2F6C4C43B58490 ON projetofoto (projeto_id)');
        $this->addSql('COMMENT ON COLUMN projetofoto.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN projetofoto.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN projetofoto.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE projetofoto ADD CONSTRAINT FK_3C2F6C4CDB38439E FOREIGN KEY (usuario_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE projetofoto ADD CONSTRAINT FK_3C2F6C4C43B58490 FOREIGN KEY (projeto_id) REFERENCES projeto (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        // $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        // $this->addSql('ALTER TABLE "user" ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE projetofoto DROP CONSTRAINT FK_3C2F6C4CDB38439E');
        $this->addSql('ALTER TABLE projetofoto DROP CONSTRAINT FK_3C2F6C4C43B58490');
        $this->addSql('DROP TABLE projetofoto');
        // $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        // $this->addSql('DROP INDEX "primary"');
    }
}
