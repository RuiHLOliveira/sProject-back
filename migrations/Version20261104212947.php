<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261104212947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE objetivo (id SERIAL NOT NULL, usuario_id INT NOT NULL, objetivopai_id INT DEFAULT NULL, objetivosfilhos_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, nome VARCHAR(255) NOT NULL, anotacoes TEXT NOT NULL, respostas JSON DEFAULT NULL, fotos JSON DEFAULT NULL, situacao SMALLINT DEFAULT 0 NOT NULL, horizonte SMALLINT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8F4E816EDB38439E ON objetivo (usuario_id)');
        $this->addSql('CREATE INDEX IDX_8F4E816EEE46D28F ON objetivo (objetivopai_id)');
        $this->addSql('CREATE INDEX IDX_8F4E816EB1392732 ON objetivo (objetivosfilhos_id)');
        $this->addSql('COMMENT ON COLUMN objetivo.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN objetivo.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE objetivo ADD CONSTRAINT FK_8F4E816EDB38439E FOREIGN KEY (usuario_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE objetivo ADD CONSTRAINT FK_8F4E816EEE46D28F FOREIGN KEY (objetivopai_id) REFERENCES objetivo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE objetivo ADD CONSTRAINT FK_8F4E816EB1392732 FOREIGN KEY (objetivosfilhos_id) REFERENCES objetivo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX idx_d684ab841000adf0');
        $this->addSql('DROP INDEX idx_f3a3e05fdb38439e');
        $this->addSql('ALTER TABLE projeto ADD objetivo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE projeto ADD CONSTRAINT FK_A0559D9497F4E608 FOREIGN KEY (objetivo_id) REFERENCES objetivo (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A0559D9497F4E608 ON projeto (objetivo_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE projeto DROP CONSTRAINT FK_A0559D9497F4E608');
        $this->addSql('ALTER TABLE objetivo DROP CONSTRAINT FK_8F4E816EDB38439E');
        $this->addSql('ALTER TABLE objetivo DROP CONSTRAINT FK_8F4E816EEE46D28F');
        $this->addSql('ALTER TABLE objetivo DROP CONSTRAINT FK_8F4E816EB1392732');
        $this->addSql('DROP TABLE objetivo');
        $this->addSql('DROP INDEX IDX_A0559D9497F4E608');
        $this->addSql('ALTER TABLE projeto DROP objetivo_id');
        $this->addSql('CREATE INDEX idx_d684ab841000adf0 ON inbox_item (inboxitem_categoria_id)');
        $this->addSql('CREATE INDEX idx_f3a3e05fdb38439e ON inboxitem_categoria (usuario_id)');
        $this->addSql('DROP INDEX "primary"');
    }
}
