<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250317155937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE appointment_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE appointment_request (id INT NOT NULL, user_id INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AAB4BDB7A76ED395 ON appointment_request (user_id)');
        $this->addSql('COMMENT ON COLUMN appointment_request.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN appointment_request.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE appointment_request ADD CONSTRAINT FK_AAB4BDB7A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE booking ADD approved_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD appointment_request_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD created_at TIME(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD updated_at TIME(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE2D234F6A FOREIGN KEY (approved_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE3B378A6F FOREIGN KEY (appointment_request_id) REFERENCES appointment_request (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E00CEDDE2D234F6A ON booking (approved_by_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E00CEDDE3B378A6F ON booking (appointment_request_id)');
        $this->addSql('ALTER TABLE brand ADD logo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDE3B378A6F');
        $this->addSql('DROP SEQUENCE appointment_request_id_seq CASCADE');
        $this->addSql('ALTER TABLE appointment_request DROP CONSTRAINT FK_AAB4BDB7A76ED395');
        $this->addSql('DROP TABLE appointment_request');
        $this->addSql('ALTER TABLE brand DROP logo');
        $this->addSql('ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDE2D234F6A');
        $this->addSql('DROP INDEX IDX_E00CEDDE2D234F6A');
        $this->addSql('DROP INDEX UNIQ_E00CEDDE3B378A6F');
        $this->addSql('ALTER TABLE booking DROP approved_by_id');
        $this->addSql('ALTER TABLE booking DROP appointment_request_id');
        $this->addSql('ALTER TABLE booking DROP created_at');
        $this->addSql('ALTER TABLE booking DROP updated_at');
    }
}
