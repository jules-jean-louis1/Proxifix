<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250813105632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Convert user roles from JSON to VARCHAR';
    }

    public function up(Schema $schema): void
    {
        // Ajouter une nouvelle colonne VARCHAR temporaire
        $this->addSql('ALTER TABLE "user" ADD COLUMN role_temp VARCHAR(50)');
        
        // Migrer les données JSON vers VARCHAR (prendre le premier rôle)
        $this->addSql("UPDATE \"user\" SET role_temp = CASE 
            WHEN roles->>0 = 'ROLE_SUPER_ADMIN' THEN 'ROLE_SUPER_ADMIN'
            WHEN roles->>0 = 'ROLE_ADMIN' THEN 'ROLE_ADMIN' 
            WHEN roles->>0 = 'ROLE_TECHNICIAN' THEN 'ROLE_TECHNICIAN'
            WHEN roles->>0 = 'ROLE_CUSTOMER' THEN 'ROLE_CUSTOMER'
            ELSE 'ROLE_CUSTOMER'
        END");
        
        // Supprimer l'ancienne colonne JSON
        $this->addSql('ALTER TABLE "user" DROP COLUMN roles');
        
        // Renommer la colonne temporaire
        $this->addSql('ALTER TABLE "user" RENAME COLUMN role_temp TO role');
        
        // Ajouter une contrainte NOT NULL
        $this->addSql('ALTER TABLE "user" ALTER COLUMN role SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // Ajouter une colonne JSON temporaire
        $this->addSql('ALTER TABLE "user" ADD COLUMN roles_temp JSON');
        
        // Migrer les données VARCHAR vers JSON
        $this->addSql('UPDATE "user" SET roles_temp = CONCAT(\'["\', role, \'"]\')::json');
        
        // Supprimer l\'ancienne colonne VARCHAR
        $this->addSql('ALTER TABLE "user" DROP COLUMN role');
        
        // Renommer la colonne temporaire
        $this->addSql('ALTER TABLE "user" RENAME COLUMN roles_temp TO roles');
        
        // Ajouter une contrainte NOT NULL
        $this->addSql('ALTER TABLE "user" ALTER COLUMN roles SET NOT NULL');
    }
}
