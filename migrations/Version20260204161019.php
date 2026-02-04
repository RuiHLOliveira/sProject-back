<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260204161019 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inbox_item DROP CONSTRAINT fk_d684ab841000adf0');
        $this->addSql('ALTER SEQUENCE categoria_item_id_seq RENAME TO inboxitem_categoria_id_seq');
        
        // $this->addSql('CREATE TABLE inboxitem_categoria (id SERIAL NOT NULL, usuario_id INT NOT NULL, categoria TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE categoria_item RENAME TO inboxitem_categoria;');
        $this->addSql('CREATE INDEX IDX_DBF75212DB38439E ON inboxitem_categoria (usuario_id)');
        $this->addSql('COMMENT ON COLUMN inboxitem_categoria.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN inboxitem_categoria.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN inboxitem_categoria.deleted_at IS \'(DC2Type:datetime_immutable)\'');

        // $this->addSql('ALTER TABLE inboxitem_categoria ADD CONSTRAINT FK_DBF75212DB38439E FOREIGN KEY (usuario_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        // $this->addSql('ALTER TABLE categoria_item DROP CONSTRAINT fk_f3a3e05fdb38439e');
        // $this->addSql('DROP TABLE categoria_item');
        // $this->addSql('DROP INDEX idx_d684ab841000adf0');

        $this->addSql('ALTER TABLE inbox_item RENAME COLUMN categoria_item_id TO inboxitem_categoria_id');
        $this->addSql('ALTER TABLE inbox_item ADD CONSTRAINT FK_D684AB84D2B7EA9C FOREIGN KEY (inboxitem_categoria_id) REFERENCES inboxitem_categoria (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D684AB84D2B7EA9C ON inbox_item (inboxitem_categoria_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER SEQUENCE inboxitem_categoria_id_seq RENAME TO categoria_item_id_seq');
        $this->addSql('ALTER TABLE inbox_item DROP CONSTRAINT FK_D684AB84D2B7EA9C');
        $this->addSql('ALTER TABLE inboxitem_categoria RENAME TO categoria_item;');
        $this->addSql('CREATE INDEX idx_f3a3e05fdb38439e ON categoria_item (usuario_id)');

        $this->addSql('COMMENT ON COLUMN categoria_item.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN categoria_item.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN categoria_item.deleted_at IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql('DROP INDEX IDX_D684AB84D2B7EA9C');
        $this->addSql('ALTER TABLE inbox_item RENAME COLUMN inboxitem_categoria_id TO categoria_item_id');
        $this->addSql('ALTER TABLE inbox_item ADD CONSTRAINT fk_d684ab841000adf0 FOREIGN KEY (categoria_item_id) REFERENCES categoria_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_d684ab841000adf0 ON inbox_item (categoria_item_id)');
    }
}
