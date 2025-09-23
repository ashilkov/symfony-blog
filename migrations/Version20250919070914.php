<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250919070914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post ADD user_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD blog_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE post ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE post ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN post.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN post.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D9D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D8FABDD9F FOREIGN KEY (blog_id_id) REFERENCES blog (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D9D86650F ON post (user_id_id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D8FABDD9F ON post (blog_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8D9D86650F');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8D8FABDD9F');
        $this->addSql('DROP INDEX IDX_5A8A6C8D9D86650F');
        $this->addSql('DROP INDEX IDX_5A8A6C8D8FABDD9F');
        $this->addSql('ALTER TABLE post DROP user_id_id');
        $this->addSql('ALTER TABLE post DROP blog_id_id');
        $this->addSql('ALTER TABLE post DROP title');
        $this->addSql('ALTER TABLE post DROP created_at');
        $this->addSql('ALTER TABLE post DROP updated_at');
    }
}
