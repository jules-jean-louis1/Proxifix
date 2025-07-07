<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250703132924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE company_specialization_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE company_company_specialization (company_id INT NOT NULL, company_specialization_id INT NOT NULL, PRIMARY KEY(company_id, company_specialization_id))');
        $this->addSql('CREATE INDEX IDX_2E6C796F979B1AD6 ON company_company_specialization (company_id)');
        $this->addSql('CREATE INDEX IDX_2E6C796FA619F3EF ON company_company_specialization (company_specialization_id)');
        $this->addSql('CREATE TABLE company_specialization (id INT NOT NULL, slug VARCHAR(255) DEFAULT NULL, label VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE company_company_specialization ADD CONSTRAINT FK_2E6C796F979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE company_company_specialization ADD CONSTRAINT FK_2E6C796FA619F3EF FOREIGN KEY (company_specialization_id) REFERENCES company_specialization (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE appointment_request ALTER title SET DEFAULT \'\'');
        $this->addSql('ALTER TABLE appointment_request ALTER title SET NOT NULL');
        $this->addSql('ALTER TABLE company ADD open_days VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD open_hours VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD logo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD mobile VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE refresh_token ALTER refresh_token TYPE VARCHAR(128)');
        $this->addSql('ALTER TABLE task ADD company_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_527EDB25979B1AD6 ON task (company_id)');
        $this->addSql('ALTER TABLE type_equipment ADD company_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE type_equipment ADD CONSTRAINT FK_67FE9493979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_67FE9493979B1AD6 ON type_equipment (company_id)');
        $this->addSql('ALTER TABLE type_intervention ADD company_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE type_intervention ADD description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE type_intervention ADD CONSTRAINT FK_565BAEAE979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_565BAEAE979B1AD6 ON type_intervention (company_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE company_specialization_id_seq CASCADE');
        $this->addSql('ALTER TABLE company_company_specialization DROP CONSTRAINT FK_2E6C796F979B1AD6');
        $this->addSql('ALTER TABLE company_company_specialization DROP CONSTRAINT FK_2E6C796FA619F3EF');
        $this->addSql('DROP TABLE company_company_specialization');
        $this->addSql('DROP TABLE company_specialization');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25979B1AD6');
        $this->addSql('DROP INDEX IDX_527EDB25979B1AD6');
        $this->addSql('ALTER TABLE task DROP company_id');
        $this->addSql('ALTER TABLE type_intervention DROP CONSTRAINT FK_565BAEAE979B1AD6');
        $this->addSql('DROP INDEX IDX_565BAEAE979B1AD6');
        $this->addSql('ALTER TABLE type_intervention DROP company_id');
        $this->addSql('ALTER TABLE type_intervention DROP description');
        $this->addSql('ALTER TABLE appointment_request ALTER title DROP DEFAULT');
        $this->addSql('ALTER TABLE appointment_request ALTER title DROP NOT NULL');
        $this->addSql('ALTER TABLE company DROP open_days');
        $this->addSql('ALTER TABLE company DROP open_hours');
        $this->addSql('ALTER TABLE company DROP logo');
        $this->addSql('ALTER TABLE company DROP phone');
        $this->addSql('ALTER TABLE company DROP mobile');
        $this->addSql('ALTER TABLE refresh_token ALTER refresh_token TYPE VARCHAR(512)');
        $this->addSql('ALTER TABLE type_equipment DROP CONSTRAINT FK_67FE9493979B1AD6');
        $this->addSql('DROP INDEX IDX_67FE9493979B1AD6');
        $this->addSql('ALTER TABLE type_equipment DROP company_id');
    }
}
