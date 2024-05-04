<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240125155608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE warranty ALTER id_user DROP NOT NULL');
        $this->addSql('ALTER TABLE warranty ADD CONSTRAINT FK_88D71CF26B3CA4B FOREIGN KEY (id_user) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_88D71CF26B3CA4B ON warranty (id_user)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE warranty DROP CONSTRAINT FK_88D71CF26B3CA4B');
        $this->addSql('DROP INDEX IDX_88D71CF26B3CA4B');
        $this->addSql('ALTER TABLE warranty ALTER id_user SET NOT NULL');
    }
}
