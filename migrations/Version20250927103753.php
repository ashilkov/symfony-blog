<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250927103753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_user DROP CONSTRAINT fk_6d435ad9a76ed395');
        $this->addSql('ALTER TABLE blog_user DROP CONSTRAINT FK_6D435AD9DAE07E97');
        $this->addSql('DROP INDEX idx_6d435ad9a76ed395');
        $this->addSql('ALTER TABLE blog_user ALTER role TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE blog_user ADD CONSTRAINT FK_6D435AD9DAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT fk_5a8a6c8da76ed395');
        $this->addSql('DROP INDEX idx_5a8a6c8da76ed395');
        $this->addSql('ALTER TABLE post ALTER user_id SET NOT NULL');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT fk_a3c664d37808b1ad');
        $this->addSql('DROP INDEX idx_a3c664d37808b1ad');
        $this->addSql('ALTER TABLE subscription ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE subscription DROP subscriber_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subscription ADD subscriber_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription DROP user_id');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT fk_a3c664d37808b1ad FOREIGN KEY (subscriber_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_a3c664d37808b1ad ON subscription (subscriber_id)');
        $this->addSql('ALTER TABLE post ALTER user_id DROP NOT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT fk_5a8a6c8da76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_5a8a6c8da76ed395 ON post (user_id)');
        $this->addSql('ALTER TABLE "blog_user" DROP CONSTRAINT fk_6d435ad9dae07e97');
        $this->addSql('ALTER TABLE "blog_user" ALTER role TYPE VARCHAR(50)');
        $this->addSql('ALTER TABLE "blog_user" ADD CONSTRAINT fk_6d435ad9a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "blog_user" ADD CONSTRAINT fk_6d435ad9dae07e97 FOREIGN KEY (blog_id) REFERENCES blog (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_6d435ad9a76ed395 ON "blog_user" (user_id)');
    }
}
