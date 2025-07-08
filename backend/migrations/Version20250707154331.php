<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250707154331 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intervention DROP CONSTRAINT fk_d11814ab6bf700bd');
        $this->addSql('DROP SEQUENCE status_id_seq CASCADE');
        $this->addSql('DROP TABLE status');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B011DDC9989D9B62 ON company_specialization (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B011DDC9EA750E8 ON company_specialization (label)');
        $this->addSql('ALTER TABLE equipment ADD reference VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX idx_d11814ab6bf700bd');
        $this->addSql('ALTER TABLE intervention ADD status VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE intervention SET status = \'pending\' WHERE status IS NULL');
        $this->addSql('ALTER TABLE intervention DROP status_id');
        $this->addSql('ALTER TABLE task ALTER price TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE brand ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE brand ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN brand.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN brand.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('UPDATE brand SET created_at = NOW(), updated_at = NOW()');
        $this->addSql('ALTER TABLE brand ALTER COLUMN created_at SET NOT NULL');
        $this->addSql('ALTER TABLE brand ALTER COLUMN updated_at SET NOT NULL');
        
        $this->addSql('ALTER TABLE operating_system ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE operating_system ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN operating_system.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN operating_system.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('UPDATE operating_system SET created_at = NOW(), updated_at = NOW()');
        $this->addSql('ALTER TABLE operating_system ALTER COLUMN created_at SET NOT NULL');
        $this->addSql('ALTER TABLE operating_system ALTER COLUMN updated_at SET NOT NULL');
        
        $this->addSql('ALTER TABLE task_intervention ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE task_intervention ADD update_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN task_intervention.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN task_intervention.update_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('UPDATE task_intervention SET created_at = NOW(), update_at = NOW()');
        $this->addSql('ALTER TABLE task_intervention ALTER COLUMN created_at SET NOT NULL');
        $this->addSql('ALTER TABLE task_intervention ALTER COLUMN update_at SET NOT NULL');
        $this->addSql('ALTER TABLE company ADD is_delete BOOLEAN DEFAULT false NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE status_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE status (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE operating_system DROP created_at');
        $this->addSql('ALTER TABLE operating_system DROP updated_at');
        $this->addSql('ALTER TABLE task_intervention DROP created_at');
        $this->addSql('ALTER TABLE task_intervention DROP update_at');
        $this->addSql('ALTER TABLE equipment DROP reference');
        $this->addSql('ALTER TABLE task ALTER price TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE brand DROP created_at');
        $this->addSql('ALTER TABLE brand DROP updated_at');
        $this->addSql('ALTER TABLE intervention ADD status_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE intervention DROP status');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT fk_d11814ab6bf700bd FOREIGN KEY (status_id) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_d11814ab6bf700bd ON intervention (status_id)');
        $this->addSql('ALTER TABLE company DROP is_delete');
        $this->addSql('DROP INDEX UNIQ_B011DDC9989D9B62');
        $this->addSql('DROP INDEX UNIQ_B011DDC9EA750E8');
    }
}
