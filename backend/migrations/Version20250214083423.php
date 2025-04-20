<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250214083423 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE booking_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE task_intervention_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE booking (id INT NOT NULL, intervention_id INT NOT NULL, start_date TIME(0) WITHOUT TIME ZONE DEFAULT NULL, end_date TIME(0) WITHOUT TIME ZONE DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, all_day BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E00CEDDE8EAE3863 ON booking (intervention_id)');
        $this->addSql('CREATE TABLE intervention_equipment (intervention_id INT NOT NULL, equipment_id INT NOT NULL, PRIMARY KEY(intervention_id, equipment_id))');
        $this->addSql('CREATE INDEX IDX_9CDAB4AA8EAE3863 ON intervention_equipment (intervention_id)');
        $this->addSql('CREATE INDEX IDX_9CDAB4AA517FE9FE ON intervention_equipment (equipment_id)');
        $this->addSql('CREATE TABLE task_intervention (id INT NOT NULL, task_id INT NOT NULL, intervention_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_198930868DB60186 ON task_intervention (task_id)');
        $this->addSql('CREATE INDEX IDX_198930868EAE3863 ON task_intervention (intervention_id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE8EAE3863 FOREIGN KEY (intervention_id) REFERENCES intervention (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervention_equipment ADD CONSTRAINT FK_9CDAB4AA8EAE3863 FOREIGN KEY (intervention_id) REFERENCES intervention (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervention_equipment ADD CONSTRAINT FK_9CDAB4AA517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_intervention ADD CONSTRAINT FK_198930868DB60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task_intervention ADD CONSTRAINT FK_198930868EAE3863 FOREIGN KEY (intervention_id) REFERENCES intervention (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE role');
        $this->addSql('ALTER TABLE customer ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer DROP first_name');
        $this->addSql('ALTER TABLE customer DROP last_name');
        $this->addSql('ALTER TABLE customer DROP email');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81398E09A76ED395 ON customer (user_id)');
        $this->addSql('DROP INDEX idx_d338d5839395c3f3');
        $this->addSql('ALTER TABLE equipment ADD brand_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE equipment ALTER operating_system_id DROP NOT NULL');
        $this->addSql('ALTER TABLE equipment RENAME COLUMN customer_id TO user_id');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT FK_D338D583A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT FK_D338D58344F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D338D583A76ED395 ON equipment (user_id)');
        $this->addSql('CREATE INDEX IDX_D338D58344F5D008 ON equipment (brand_id)');
        $this->addSql('ALTER TABLE intervention ADD type_id INT NOT NULL');
        $this->addSql('ALTER TABLE intervention ADD status_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE intervention ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE intervention DROP type');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814ABC54C8C93 FOREIGN KEY (type_id) REFERENCES type_intervention (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB6BF700BD FOREIGN KEY (status_id) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814ABA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D11814ABC54C8C93 ON intervention (type_id)');
        $this->addSql('CREATE INDEX IDX_D11814AB6BF700BD ON intervention (status_id)');
        $this->addSql('CREATE INDEX IDX_D11814ABA76ED395 ON intervention (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE booking_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE task_intervention_id_seq CASCADE');
        $this->addSql('CREATE TABLE role (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDE8EAE3863');
        $this->addSql('ALTER TABLE intervention_equipment DROP CONSTRAINT FK_9CDAB4AA8EAE3863');
        $this->addSql('ALTER TABLE intervention_equipment DROP CONSTRAINT FK_9CDAB4AA517FE9FE');
        $this->addSql('ALTER TABLE task_intervention DROP CONSTRAINT FK_198930868DB60186');
        $this->addSql('ALTER TABLE task_intervention DROP CONSTRAINT FK_198930868EAE3863');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE intervention_equipment');
        $this->addSql('DROP TABLE task_intervention');
        $this->addSql('ALTER TABLE customer DROP CONSTRAINT FK_81398E09A76ED395');
        $this->addSql('DROP INDEX UNIQ_81398E09A76ED395');
        $this->addSql('ALTER TABLE customer ADD first_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD last_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer DROP user_id');
        $this->addSql('ALTER TABLE equipment DROP CONSTRAINT FK_D338D583A76ED395');
        $this->addSql('ALTER TABLE equipment DROP CONSTRAINT FK_D338D58344F5D008');
        $this->addSql('DROP INDEX IDX_D338D583A76ED395');
        $this->addSql('DROP INDEX IDX_D338D58344F5D008');
        $this->addSql('ALTER TABLE equipment ADD customer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE equipment DROP user_id');
        $this->addSql('ALTER TABLE equipment DROP brand_id');
        $this->addSql('ALTER TABLE equipment ALTER operating_system_id SET NOT NULL');
        $this->addSql('CREATE INDEX idx_d338d5839395c3f3 ON equipment (customer_id)');
        $this->addSql('ALTER TABLE intervention DROP CONSTRAINT FK_D11814ABC54C8C93');
        $this->addSql('ALTER TABLE intervention DROP CONSTRAINT FK_D11814AB6BF700BD');
        $this->addSql('ALTER TABLE intervention DROP CONSTRAINT FK_D11814ABA76ED395');
        $this->addSql('DROP INDEX IDX_D11814ABC54C8C93');
        $this->addSql('DROP INDEX IDX_D11814AB6BF700BD');
        $this->addSql('DROP INDEX IDX_D11814ABA76ED395');
        $this->addSql('ALTER TABLE intervention ADD type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE intervention DROP type_id');
        $this->addSql('ALTER TABLE intervention DROP status_id');
        $this->addSql('ALTER TABLE intervention DROP user_id');
    }
}
