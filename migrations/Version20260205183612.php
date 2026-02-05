<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260205183612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE recompensaacao_id_seq CASCADE');
        $this->addSql('ALTER TABLE recompensaacao DROP CONSTRAINT fk_83ba8cb4c213d7c1');
        $this->addSql('DROP TABLE recompensaacao');
        $this->addSql('DROP TABLE recompensa');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE recompensaacao_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE recompensaacao (id SERIAL NOT NULL, recompensa_id INT NOT NULL, createdat TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deletedat TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, tipoatividade VARCHAR(255) NOT NULL, quantidade SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_83ba8cb4c213d7c1 ON recompensaacao (recompensa_id)');
        $this->addSql('COMMENT ON COLUMN recompensaacao.createdat IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN recompensaacao.updatedat IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN recompensaacao.deletedat IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE recompensaacao ADD CONSTRAINT fk_83ba8cb4c213d7c1 FOREIGN KEY (recompensa_id) REFERENCES recompensa (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
