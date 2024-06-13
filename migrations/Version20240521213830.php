<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240521213830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE habito_realizado (id SERIAL NOT NULL, habito_id INT NOT NULL, usuario_id INT NOT NULL, realizado_em TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_406B251D8362911 ON habito_realizado (habito_id)');
        $this->addSql('CREATE INDEX IDX_406B251DB38439E ON habito_realizado (usuario_id)');
        $this->addSql('COMMENT ON COLUMN habito_realizado.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN habito_realizado.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN habito_realizado.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE habito_realizado ADD CONSTRAINT FK_406B251D8362911 FOREIGN KEY (habito_id) REFERENCES habito (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE habito_realizado ADD CONSTRAINT FK_406B251DB38439E FOREIGN KEY (usuario_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE habito_realizado DROP CONSTRAINT FK_406B251D8362911');
        $this->addSql('ALTER TABLE habito_realizado DROP CONSTRAINT FK_406B251DB38439E');
        $this->addSql('DROP TABLE habito_realizado');
    }
}
