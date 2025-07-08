<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250321130856 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE customer_id_seq CASCADE');
        $this->addSql('ALTER TABLE customer DROP CONSTRAINT fk_81398e09a76ed395');
        $this->addSql('ALTER TABLE equipment DROP CONSTRAINT IF EXISTS fk_D338D5839395C3F3');
        $this->addSql('DROP TABLE customer');
        $this->addSql('ALTER TABLE company ALTER about DROP NOT NULL');
        $this->addSql('ALTER TABLE company ALTER type DROP NOT NULL');
        $this->addSql('ALTER TABLE company ALTER website DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD zipcode VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD city VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD address VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE customer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE customer (id INT NOT NULL, user_id INT DEFAULT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, zip_code VARCHAR(255) NOT NULL, mobile VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_81398e09a76ed395 ON customer (user_id)');
        $this->addSql('COMMENT ON COLUMN customer.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN customer.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT fk_81398e09a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE company ALTER about SET NOT NULL');
        $this->addSql('ALTER TABLE company ALTER type SET NOT NULL');
        $this->addSql('ALTER TABLE company ALTER website SET NOT NULL');
        $this->addSql('ALTER TABLE "user" DROP zipcode');
        $this->addSql('ALTER TABLE "user" DROP city');
        $this->addSql('ALTER TABLE "user" DROP phone');
        $this->addSql('ALTER TABLE "user" DROP address');
    }
}
