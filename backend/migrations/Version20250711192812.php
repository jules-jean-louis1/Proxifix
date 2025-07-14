<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250711192812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company RENAME COLUMN is_delete TO is_deleted');
        $this->addSql('ALTER TABLE equipment ADD COLUMN model VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE task_intervention RENAME COLUMN update_at TO updated_at');
        $this->addSql('DROP FUNCTION IF EXISTS get_free_slots(date, integer, integer);');
        $this->addSql('DROP FUNCTION IF EXISTS get_free_slots(date, integer, integer, time, time, text) CASCADE;');
        $this->addSql("CREATE OR REPLACE FUNCTION get_free_slots(
                                p_date DATE,
                                p_company_id INTEGER DEFAULT NULL,
                                p_interval_minutes INTEGER DEFAULT 30,
                                p_start_time TIME DEFAULT '09:00:00',
                                p_end_time TIME DEFAULT '18:00:00',
                                p_role_search TEXT DEFAULT NULL
                            )
                            RETURNS TABLE(start_time TIME WITHOUT TIME ZONE, end_time TIME WITHOUT TIME ZONE)
                            LANGUAGE plpgsql
                            AS
                            \$\$
                            DECLARE
                                current_start TIME;
                                current_end TIME;
                                intervention_count INTEGER;
                                technician_count INTEGER;
                            BEGIN
                                IF p_interval_minutes < 5 THEN
                                    RAISE EXCEPTION 'L''intervalle doit être d''au moins 5 minutes';
                                END IF;

                                -- Compter le nombre de techniciens dans l'entreprise
                                IF p_role_search IS NOT NULL THEN
                                    SELECT COUNT(u.id) INTO technician_count
                                    FROM \"user\" u
                                    WHERE u.company_id = p_company_id
                                    AND u.roles::jsonb @> to_jsonb(ARRAY[p_role_search]);
                                ELSE
                                    SELECT COUNT(u.id) INTO technician_count
                                    FROM \"user\" u
                                    WHERE u.company_id = p_company_id;
                                END IF;

                                current_start := p_start_time;

                                WHILE current_start < p_end_time LOOP
                                    current_end := current_start + make_interval(mins := p_interval_minutes);

                                    -- Compter le nombre d'interventions sur ce créneau
                                    SELECT COUNT(i.id) INTO intervention_count
                                    FROM intervention i
                                    WHERE DATE(i.start_date) = p_date
                                    AND (i.start_date::time, i.end_date::time) OVERLAPS (current_start, current_end)
                                    AND (p_company_id IS NULL OR i.company_id = p_company_id);

                                    -- Si le nombre d'interventions est inférieur au nombre de techniciens
                                    IF intervention_count < technician_count THEN
                                        start_time := current_start;
                                        end_time := current_end;
                                        RETURN NEXT;
                                    END IF;

                                    current_start := current_end;
                                END LOOP;
                            END;
                            \$\$;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipment DROP COLUMN model');
        $this->addSql('ALTER TABLE company RENAME COLUMN is_deleted TO is_delete');
        $this->addSql('ALTER TABLE task_intervention RENAME COLUMN updated_at TO update_at');
        $this->addSql('DROP FUNCTION IF EXISTS get_free_slots(date, integer, integer);');
        $this->addSql('DROP FUNCTION IF EXISTS get_free_slots(date, integer, integer, time, time, text) CASCADE;');
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
}
