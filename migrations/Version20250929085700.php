<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250929085700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D3DAE07E97');
        $this->addSql('ALTER TABLE subscription ALTER blog_id SET NOT NULL');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3DAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT UNIQ_SUBSCRIPTION_BLOG_USER UNIQUE (blog_id, user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "subscription" DROP CONSTRAINT UNIQ_SUBSCRIPTION_BLOG_USER');
        $this->addSql('ALTER TABLE "subscription" DROP CONSTRAINT fk_a3c664d3dae07e97');
        $this->addSql('ALTER TABLE "subscription" ALTER blog_id DROP NOT NULL');
        $this->addSql('ALTER TABLE "subscription" ADD CONSTRAINT fk_a3c664d3dae07e97 FOREIGN KEY (blog_id) REFERENCES blog (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
