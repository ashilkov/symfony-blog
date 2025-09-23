<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250919112223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP CONSTRAINT fk_5a8a6c8d8fabdd9f');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT fk_5a8a6c8d9d86650f');
        $this->addSql('DROP INDEX idx_5a8a6c8d8fabdd9f');
        $this->addSql('DROP INDEX idx_5a8a6c8d9d86650f');
        $this->addSql('ALTER TABLE post ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD blog_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post DROP user_id_id');
        $this->addSql('ALTER TABLE post DROP blog_id_id');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DDAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DA76ED395 ON post (user_id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DDAE07E97 ON post (blog_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8DA76ED395');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8DDAE07E97');
        $this->addSql('DROP INDEX IDX_5A8A6C8DA76ED395');
        $this->addSql('DROP INDEX IDX_5A8A6C8DDAE07E97');
        $this->addSql('ALTER TABLE post ADD user_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD blog_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post DROP user_id');
        $this->addSql('ALTER TABLE post DROP blog_id');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT fk_5a8a6c8d8fabdd9f FOREIGN KEY (blog_id_id) REFERENCES blog (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT fk_5a8a6c8d9d86650f FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_5a8a6c8d8fabdd9f ON post (blog_id_id)');
        $this->addSql('CREATE INDEX idx_5a8a6c8d9d86650f ON post (user_id_id)');
    }
}
