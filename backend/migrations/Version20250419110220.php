<?php

declare (strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250419110220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE booking_id_seq CASCADE');
        $this->addSql('ALTER TABLE booking DROP CONSTRAINT fk_e00cedde8eae3863');
        $this->addSql('ALTER TABLE booking DROP CONSTRAINT fk_e00cedde2d234f6a');
        $this->addSql('ALTER TABLE booking DROP CONSTRAINT fk_e00cedde3b378a6f');
        $this->addSql('DROP TABLE booking');
        $this->addSql('ALTER TABLE appointment_request ADD approved_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE appointment_request ADD title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE appointment_request ADD CONSTRAINT FK_AAB4BDB72D234F6A FOREIGN KEY (approved_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_AAB4BDB72D234F6A ON appointment_request (approved_by_id)');
        $this->addSql('ALTER TABLE intervention ADD appointment_request_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB3B378A6F FOREIGN KEY (appointment_request_id) REFERENCES appointment_request (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D11814AB3B378A6F ON intervention (appointment_request_id)');
        $this->addSql("CREATE OR REPLACE FUNCTION get_free_slots(
                                p_date DATE,
                                p_company_id INTEGER DEFAULT NULL,
                                p_interval_minutes INTEGER DEFAULT 30
                            )
                            RETURNS TABLE(start_time TIME WITHOUT TIME ZONE, end_time TIME WITHOUT TIME ZONE)
                            LANGUAGE plpgsql
                            AS
                            $$
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
                                        SELECT 1
                                        FROM intervention i
                                        WHERE DATE(i.start_date) = p_date
                                        AND (i.start_date::time, i.end_date::time) OVERLAPS (current_start, current_end)
                                        AND (p_company_id IS NULL OR i.company_id = p_company_id)
                                    ) THEN
                                        start_time := current_start;
                                        end_time := current_end;
                                        RETURN NEXT;
                                    END IF;

                                    current_start := current_end;
                                END LOOP;
                            END;
                            $$;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE booking_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE booking (id INT NOT NULL, intervention_id INT NOT NULL, approved_by_id INT DEFAULT NULL, appointment_request_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, all_day BOOLEAN DEFAULT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_e00cedde3b378a6f ON booking (appointment_request_id)');
        $this->addSql('CREATE INDEX idx_e00cedde2d234f6a ON booking (approved_by_id)');
        $this->addSql('CREATE INDEX idx_e00cedde8eae3863 ON booking (intervention_id)');
        $this->addSql('COMMENT ON COLUMN booking.start_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN booking.end_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN booking.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN booking.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT fk_e00cedde8eae3863 FOREIGN KEY (intervention_id) REFERENCES intervention (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT fk_e00cedde2d234f6a FOREIGN KEY (approved_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT fk_e00cedde3b378a6f FOREIGN KEY (appointment_request_id) REFERENCES appointment_request (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervention DROP CONSTRAINT FK_D11814AB3B378A6F');
        $this->addSql('DROP INDEX IDX_D11814AB3B378A6F');
        $this->addSql('ALTER TABLE intervention DROP appointment_request_id');
        $this->addSql('ALTER TABLE appointment_request DROP CONSTRAINT FK_AAB4BDB72D234F6A');
        $this->addSql('DROP INDEX IDX_AAB4BDB72D234F6A');
        $this->addSql('ALTER TABLE appointment_request DROP approved_by_id');
        $this->addSql('ALTER TABLE appointment_request DROP title');
    }
}
