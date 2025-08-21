<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250402162120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment_request ADD equipment_id INT DEFAULT NULL');
        $this->addSql("INSERT INTO equipment (id, name, created_at, updated_at) VALUES (1, 'Default Equipment', NOW(), NOW())");
        $this->addSql('UPDATE appointment_request SET equipment_id = 1 WHERE equipment_id IS NULL');
        $this->addSql('DROP SEQUENCE appointment_equipment_id_seq CASCADE');
        $this->addSql('ALTER TABLE appointment_equipment DROP CONSTRAINT fk_46b2bb8a517fe9fe');
        $this->addSql('ALTER TABLE appointment_equipment DROP CONSTRAINT fk_46b2bb8ae5b533f9');
        $this->addSql('ALTER TABLE intervention_equipment DROP CONSTRAINT fk_9cdab4aa8eae3863');
        $this->addSql('ALTER TABLE intervention_equipment DROP CONSTRAINT fk_9cdab4aa517fe9fe');
        $this->addSql('DROP TABLE appointment_equipment');
        $this->addSql('DROP TABLE intervention_equipment');
        $this->addSql('ALTER TABLE appointment_request ALTER COLUMN equipment_id SET NOT NULL');
        $this->addSql('ALTER TABLE appointment_request ADD type_intervention_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE appointment_request ADD description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE appointment_request ADD CONSTRAINT FK_AAB4BDB7517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE appointment_request ADD CONSTRAINT FK_AAB4BDB7799AAC17 FOREIGN KEY (type_intervention_id) REFERENCES type_intervention (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_AAB4BDB7517FE9FE ON appointment_request (equipment_id)');
        $this->addSql('CREATE INDEX IDX_AAB4BDB7799AAC17 ON appointment_request (type_intervention_id)');
        $this->addSql('ALTER TABLE intervention DROP CONSTRAINT fk_d11814abc54c8c93');
        $this->addSql('DROP INDEX idx_d11814abc54c8c93');
        $this->addSql('ALTER TABLE intervention ADD equipment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE intervention ADD text VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE intervention ADD start_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE intervention ADD end_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE intervention RENAME COLUMN type_id TO type_intervention_id');
        $this->addSql('COMMENT ON COLUMN intervention.start_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN intervention.end_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB799AAC17 FOREIGN KEY (type_intervention_id) REFERENCES type_intervention (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D11814AB799AAC17 ON intervention (type_intervention_id)');
        $this->addSql('CREATE INDEX IDX_D11814AB517FE9FE ON intervention (equipment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE appointment_equipment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE appointment_equipment (id INT NOT NULL, equipment_id INT DEFAULT NULL, appointment_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_46b2bb8ae5b533f9 ON appointment_equipment (appointment_id)');
        $this->addSql('CREATE INDEX idx_46b2bb8a517fe9fe ON appointment_equipment (equipment_id)');
        $this->addSql('CREATE TABLE intervention_equipment (intervention_id INT NOT NULL, equipment_id INT NOT NULL, PRIMARY KEY(intervention_id, equipment_id))');
        $this->addSql('CREATE INDEX idx_9cdab4aa517fe9fe ON intervention_equipment (equipment_id)');
        $this->addSql('CREATE INDEX idx_9cdab4aa8eae3863 ON intervention_equipment (intervention_id)');
        $this->addSql('ALTER TABLE appointment_equipment ADD CONSTRAINT fk_46b2bb8a517fe9fe FOREIGN KEY (equipment_id) REFERENCES equipment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE appointment_equipment ADD CONSTRAINT fk_46b2bb8ae5b533f9 FOREIGN KEY (appointment_id) REFERENCES appointment_request (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervention_equipment ADD CONSTRAINT fk_9cdab4aa8eae3863 FOREIGN KEY (intervention_id) REFERENCES intervention (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervention_equipment ADD CONSTRAINT fk_9cdab4aa517fe9fe FOREIGN KEY (equipment_id) REFERENCES equipment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE appointment_request DROP CONSTRAINT FK_AAB4BDB7517FE9FE');
        $this->addSql('ALTER TABLE appointment_request DROP CONSTRAINT FK_AAB4BDB7799AAC17');
        $this->addSql('DROP INDEX IDX_AAB4BDB7517FE9FE');
        $this->addSql('DROP INDEX IDX_AAB4BDB7799AAC17');
        $this->addSql('ALTER TABLE appointment_request DROP equipment_id');
        $this->addSql('ALTER TABLE appointment_request DROP type_intervention_id');
        $this->addSql('ALTER TABLE appointment_request DROP description');
        $this->addSql('ALTER TABLE intervention DROP CONSTRAINT FK_D11814AB799AAC17');
        $this->addSql('ALTER TABLE intervention DROP CONSTRAINT FK_D11814AB517FE9FE');
        $this->addSql('DROP INDEX IDX_D11814AB799AAC17');
        $this->addSql('DROP INDEX IDX_D11814AB517FE9FE');
        $this->addSql('ALTER TABLE intervention DROP equipment_id');
        $this->addSql('ALTER TABLE intervention DROP text');
        $this->addSql('ALTER TABLE intervention DROP start_date');
        $this->addSql('ALTER TABLE intervention DROP end_date');
        $this->addSql('ALTER TABLE intervention RENAME COLUMN type_intervention_id TO type_id');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT fk_d11814abc54c8c93 FOREIGN KEY (type_id) REFERENCES type_intervention (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_d11814abc54c8c93 ON intervention (type_id)');
    }
}
