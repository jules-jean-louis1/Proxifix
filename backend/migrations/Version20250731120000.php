<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to remove PostgreSQL get_free_slots function
 * This function is no longer needed as the logic has been moved to PHP services.
 */
final class Version20250731120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Supprime la fonction PostgreSQL get_free_slots car la logique a été déplacée vers le service PHP AvailabilityService pour améliorer la maintenabilité';
    }

    public function up(Schema $schema): void
    {
        // Suppression de la fonction PostgreSQL get_free_slots
        $this->addSql('DROP FUNCTION IF EXISTS get_free_slots(date, integer, integer, time, time, text) CASCADE;');
        $this->addSql('DROP FUNCTION IF EXISTS get_free_slots(date, integer, integer) CASCADE;');

        // Correction de la relation OneToOne entre AppointmentRequest et Intervention
        // Ajout d'une contrainte UNIQUE sur appointment_request_id dans la table intervention
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT UNIQ_D11814AB9D6A1065 UNIQUE (appointment_request_id)');
    }

    public function down(Schema $schema): void
    {
        // Suppression de la contrainte UNIQUE
        $this->addSql('ALTER TABLE intervention DROP CONSTRAINT IF EXISTS UNIQ_D11814AB9D6A1065');

        // Recréation de la fonction en cas de rollback
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
}
