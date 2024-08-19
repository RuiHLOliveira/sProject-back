<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240819215647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inbox_item ADD categoria_item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE inbox_item DROP categoria');
        $this->addSql('ALTER TABLE inbox_item ADD CONSTRAINT FK_D684AB841000ADF0 FOREIGN KEY (categoria_item_id) REFERENCES categoria_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D684AB841000ADF0 ON inbox_item (categoria_item_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE inbox_item DROP CONSTRAINT FK_D684AB841000ADF0');
        $this->addSql('DROP INDEX IDX_D684AB841000ADF0');
        $this->addSql('ALTER TABLE inbox_item ADD categoria SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE inbox_item DROP categoria_item_id');
    }
}
