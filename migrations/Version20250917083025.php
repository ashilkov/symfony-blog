<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250917083025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "blog_user" (blog_id INT NOT NULL, user_id INT NOT NULL, role VARCHAR(50) NOT NULL, PRIMARY KEY(blog_id, user_id))');
        $this->addSql('CREATE INDEX IDX_6D435AD9DAE07E97 ON "blog_user" (blog_id)');
        $this->addSql('CREATE INDEX IDX_6D435AD9A76ED395 ON "blog_user" (user_id)');
        $this->addSql('ALTER TABLE "blog_user" ADD CONSTRAINT FK_6D435AD9DAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "blog_user" ADD CONSTRAINT FK_6D435AD9A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "blog_user" DROP CONSTRAINT FK_6D435AD9DAE07E97');
        $this->addSql('ALTER TABLE "blog_user" DROP CONSTRAINT FK_6D435AD9A76ED395');
        $this->addSql('DROP TABLE blog');
        $this->addSql('DROP TABLE "blog_user"');
    }
}
