<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250708095612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment_request DROP CONSTRAINT FK_AAB4BDB7979B1AD6');
        $this->addSql('ALTER TABLE appointment_request ADD CONSTRAINT FK_AAB4BDB7979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE equipment DROP CONSTRAINT FK_D338D583A391D4AD');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT FK_D338D583A391D4AD FOREIGN KEY (operating_system_id) REFERENCES operating_system (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervention DROP CONSTRAINT FK_D11814AB979B1AD6');
        $this->addSql('ALTER TABLE intervention ALTER status SET NOT NULL');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649979B1AD6');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipment DROP CONSTRAINT fk_d338d583a391d4ad');
        $this->addSql('ALTER TABLE equipment ADD CONSTRAINT fk_d338d583a391d4ad FOREIGN KEY (operating_system_id) REFERENCES operating_system (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d649979b1ad6');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d649979b1ad6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE appointment_request DROP CONSTRAINT fk_aab4bdb7979b1ad6');
        $this->addSql('ALTER TABLE appointment_request ADD CONSTRAINT fk_aab4bdb7979b1ad6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervention DROP CONSTRAINT fk_d11814ab979b1ad6');
        $this->addSql('ALTER TABLE intervention ALTER status DROP NOT NULL');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT fk_d11814ab979b1ad6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
