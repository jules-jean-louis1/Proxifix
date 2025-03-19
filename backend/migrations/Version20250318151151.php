<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250318151151 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE appointment_equipment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE appointment_equipment (id INT NOT NULL, equipment_id INT DEFAULT NULL, appointment_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_46B2BB8A517FE9FE ON appointment_equipment (equipment_id)');
        $this->addSql('CREATE INDEX IDX_46B2BB8AE5B533F9 ON appointment_equipment (appointment_id)');
        $this->addSql('ALTER TABLE appointment_equipment ADD CONSTRAINT FK_46B2BB8A517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE appointment_equipment ADD CONSTRAINT FK_46B2BB8AE5B533F9 FOREIGN KEY (appointment_id) REFERENCES appointment_request (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE appointment_request ADD company_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE appointment_request ADD CONSTRAINT FK_AAB4BDB7979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_AAB4BDB7979B1AD6 ON appointment_request (company_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE appointment_equipment_id_seq CASCADE');
        $this->addSql('ALTER TABLE appointment_equipment DROP CONSTRAINT FK_46B2BB8A517FE9FE');
        $this->addSql('ALTER TABLE appointment_equipment DROP CONSTRAINT FK_46B2BB8AE5B533F9');
        $this->addSql('DROP TABLE appointment_equipment');
        $this->addSql('ALTER TABLE appointment_request DROP CONSTRAINT FK_AAB4BDB7979B1AD6');
        $this->addSql('DROP INDEX IDX_AAB4BDB7979B1AD6');
        $this->addSql('ALTER TABLE appointment_request DROP company_id');
    }
}
