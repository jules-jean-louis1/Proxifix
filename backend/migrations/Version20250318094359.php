<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250318094359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('SET timezone = \'Europe/Paris\'');
        $this->addSql('CREATE SEQUENCE appointment_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE appointment_request (id INT NOT NULL, user_id INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AAB4BDB7A76ED395 ON appointment_request (user_id)');
        $this->addSql('COMMENT ON COLUMN appointment_request.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN appointment_request.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN appointment_request.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE appointment_request ADD CONSTRAINT FK_AAB4BDB7A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE booking ADD approved_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD appointment_request_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE booking DROP COLUMN start_date;');
        $this->addSql('ALTER TABLE booking DROP COLUMN end_date;');
        $this->addSql('ALTER TABLE booking ADD COLUMN start_date TIMESTAMPTZ DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD COLUMN end_date TIMESTAMPTZ DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD created_at TIMESTAMPTZ DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD updated_at TIMESTAMPTZ DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN booking.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN booking.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN booking.start_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN booking.end_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE2D234F6A FOREIGN KEY (approved_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE3B378A6F FOREIGN KEY (appointment_request_id) REFERENCES appointment_request (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E00CEDDE2D234F6A ON booking (approved_by_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E00CEDDE3B378A6F ON booking (appointment_request_id)');
        $this->addSql('ALTER TABLE brand ADD logo VARCHAR(255) DEFAULT NULL');
        $this->addSql("CREATE OR REPLACE FUNCTION get_free_slots(
            p_date DATE,
            p_company_id INT DEFAULT NULL,
            p_interval_minutes INT DEFAULT 30
        )
        RETURNS TABLE(start_time TIME, end_time TIME) AS $$
        DECLARE
            slot_start TIME := '09:00:00';
            slot_end TIME := '18:00:00';
            current_start TIME;
            current_end TIME;
        BEGIN
            IF p_interval_minutes < 5 THEN
                RAISE EXCEPTION 'L''intervalle doit être d''au moins 5 minutes';
            END IF;
        
            current_start := slot_start;
        
            WHILE current_start < slot_end LOOP
                current_end := current_start + make_interval(mins := p_interval_minutes);
                
                IF NOT EXISTS (
                    SELECT 1 FROM booking b
                    JOIN intervention i ON i.id = b.intervention_id
                    WHERE DATE(b.start_date) = p_date
                    AND (b.start_date::time, b.end_date::time) OVERLAPS (current_start, current_end)
                    AND (p_company_id IS NULL OR i.company_id = p_company_id)
                ) THEN
                    start_time := current_start;
                    end_time := current_end;
                    RETURN NEXT;
                END IF;
        
                current_start := current_end;
            END LOOP;
        END;
        $$ LANGUAGE plpgsql;");
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
        $this->addSql('ALTER TABLE booking ALTER start_date TYPE TIME(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE booking ALTER end_date TYPE TIME(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN booking.start_date IS NULL');
        $this->addSql('COMMENT ON COLUMN booking.end_date IS NULL');
        $this->addSql('DROP FUNCTION IF EXISTS get_free_slots(DATE, INT)');
    }
}
