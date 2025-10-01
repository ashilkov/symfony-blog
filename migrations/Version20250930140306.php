<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250930140306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE blog ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN blog.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN blog.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE blog_user DROP CONSTRAINT blog_user_pkey');
        $this->addSql('ALTER TABLE blog_user ADD PRIMARY KEY (user_id, blog_id)');
        $this->addSql('ALTER TABLE comment ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE comment ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE post ALTER content DROP NOT NULL');
        $this->addSql('ALTER TABLE post ALTER title DROP NOT NULL');
        $this->addSql('ALTER TABLE post ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE post ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE subscription DROP created_at');
        $this->addSql('ALTER TABLE subscription DROP updated_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subscription ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE subscription ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN subscription.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN subscription.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE blog DROP created_at');
        $this->addSql('ALTER TABLE blog DROP updated_at');
        $this->addSql('DROP INDEX blog_user_pkey');
        $this->addSql('ALTER TABLE blog_user ADD PRIMARY KEY (blog_id, user_id)');
        $this->addSql('ALTER TABLE post ALTER content SET NOT NULL');
        $this->addSql('ALTER TABLE post ALTER title SET NOT NULL');
        $this->addSql('ALTER TABLE post ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE post ALTER updated_at SET NOT NULL');
        $this->addSql('ALTER TABLE comment ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE comment ALTER updated_at SET NOT NULL');
    }
}
