<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220808122636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE atividade ADD usuario_id INT NOT NULL');
        $this->addSql('ALTER TABLE atividade ADD CONSTRAINT FK_136416C2DB38439E FOREIGN KEY (usuario_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_136416C2DB38439E ON atividade (usuario_id)');
        $this->addSql('ALTER TABLE dia ADD usuario_id INT NOT NULL');
        $this->addSql('ALTER TABLE dia ADD CONSTRAINT FK_3E153BCEDB38439E FOREIGN KEY (usuario_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3E153BCEDB38439E ON dia (usuario_id)');
        $this->addSql('ALTER TABLE hora ADD usuario_id INT NOT NULL');
        $this->addSql('ALTER TABLE hora ADD CONSTRAINT FK_BBE1C657DB38439E FOREIGN KEY (usuario_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_BBE1C657DB38439E ON hora (usuario_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE hora DROP CONSTRAINT FK_BBE1C657DB38439E');
        $this->addSql('DROP INDEX IDX_BBE1C657DB38439E');
        $this->addSql('ALTER TABLE hora DROP usuario_id');
        $this->addSql('ALTER TABLE atividade DROP CONSTRAINT FK_136416C2DB38439E');
        $this->addSql('DROP INDEX IDX_136416C2DB38439E');
        $this->addSql('ALTER TABLE atividade DROP usuario_id');
        $this->addSql('ALTER TABLE dia DROP CONSTRAINT FK_3E153BCEDB38439E');
        $this->addSql('DROP INDEX IDX_3E153BCEDB38439E');
        $this->addSql('ALTER TABLE dia DROP usuario_id');
    }
}
