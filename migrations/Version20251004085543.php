<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251004085543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_user DROP CONSTRAINT fk_6d435ad9dae07e97');
        $this->addSql('DROP INDEX idx_6d435ad9dae07e97');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT fk_9474526c4b89032c');
        $this->addSql('DROP INDEX idx_9474526c4b89032c');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT fk_5a8a6c8ddae07e97');
        $this->addSql('DROP INDEX idx_5a8a6c8ddae07e97');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT fk_a3c664d3dae07e97');
        $this->addSql('DROP INDEX idx_a3c664d3dae07e97');
        $this->addSql('ALTER TABLE subscription ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription ALTER blog_id DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN subscription.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN subscription.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subscription DROP created_at');
        $this->addSql('ALTER TABLE subscription DROP updated_at');
        $this->addSql('ALTER TABLE subscription ALTER blog_id SET NOT NULL');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT fk_a3c664d3dae07e97 FOREIGN KEY (blog_id) REFERENCES blog (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_a3c664d3dae07e97 ON subscription (blog_id)');
        $this->addSql('ALTER TABLE blog_user ADD CONSTRAINT fk_6d435ad9dae07e97 FOREIGN KEY (blog_id) REFERENCES blog (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_6d435ad9dae07e97 ON blog_user (blog_id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT fk_5a8a6c8ddae07e97 FOREIGN KEY (blog_id) REFERENCES blog (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_5a8a6c8ddae07e97 ON post (blog_id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT fk_9474526c4b89032c FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_9474526c4b89032c ON comment (post_id)');
    }
}
