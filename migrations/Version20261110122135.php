<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261110122135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE area (id SERIAL NOT NULL, usuario_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, nome VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D7943D68DB38439E ON area (usuario_id)');
        $this->addSql('COMMENT ON COLUMN area.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN area.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE area ADD CONSTRAINT FK_D7943D68DB38439E FOREIGN KEY (usuario_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE objetivo DROP CONSTRAINT fk_8f4e816eee46d28f');
        $this->addSql('ALTER TABLE objetivo DROP CONSTRAINT fk_8f4e816eb1392732');
        $this->addSql('DROP INDEX idx_8f4e816eee46d28f');
        $this->addSql('DROP INDEX idx_8f4e816eb1392732');
        $this->addSql('ALTER TABLE objetivo ADD area_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE objetivo DROP objetivopai_id');
        $this->addSql('ALTER TABLE objetivo DROP objetivosfilhos_id');
        $this->addSql('ALTER TABLE objetivo ADD CONSTRAINT FK_8F4E816EBD0F409C FOREIGN KEY (area_id) REFERENCES area (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8F4E816EBD0F409C ON objetivo (area_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE objetivo DROP CONSTRAINT FK_8F4E816EBD0F409C');
        $this->addSql('ALTER TABLE area DROP CONSTRAINT FK_D7943D68DB38439E');
        $this->addSql('DROP TABLE area');
        $this->addSql('DROP INDEX IDX_8F4E816EBD0F409C');
        $this->addSql('ALTER TABLE objetivo ADD objetivosfilhos_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE objetivo RENAME COLUMN area_id TO objetivopai_id');
        $this->addSql('ALTER TABLE objetivo ADD CONSTRAINT fk_8f4e816eee46d28f FOREIGN KEY (objetivopai_id) REFERENCES objetivo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE objetivo ADD CONSTRAINT fk_8f4e816eb1392732 FOREIGN KEY (objetivosfilhos_id) REFERENCES objetivo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8f4e816eee46d28f ON objetivo (objetivopai_id)');
        $this->addSql('CREATE INDEX idx_8f4e816eb1392732 ON objetivo (objetivosfilhos_id)');
    }
}
