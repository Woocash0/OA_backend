<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240103165509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE warranty_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE tag (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE warranty (id INT NOT NULL, category VARCHAR(100) NOT NULL, product_name VARCHAR(255) NOT NULL, purchase_date DATE NOT NULL, warranty_period INT NOT NULL, id_user INT NOT NULL, receipt VARCHAR(255) DEFAULT NULL, warranty_end_date DATE DEFAULT NULL, active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE warranty_tag (warranty_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(warranty_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_5AAB728B2EC1782C ON warranty_tag (warranty_id)');
        $this->addSql('CREATE INDEX IDX_5AAB728BBAD26311 ON warranty_tag (tag_id)');
        $this->addSql('ALTER TABLE warranty_tag ADD CONSTRAINT FK_5AAB728B2EC1782C FOREIGN KEY (warranty_id) REFERENCES warranty (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE warranty_tag ADD CONSTRAINT FK_5AAB728BBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE warranty_id_seq CASCADE');
        $this->addSql('ALTER TABLE warranty_tag DROP CONSTRAINT FK_5AAB728B2EC1782C');
        $this->addSql('ALTER TABLE warranty_tag DROP CONSTRAINT FK_5AAB728BBAD26311');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE warranty');
        $this->addSql('DROP TABLE warranty_tag');
    }
}
