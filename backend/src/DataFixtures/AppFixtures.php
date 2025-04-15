<?php
namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\OperatingSystem;
use App\Entity\Role;
use App\Entity\Status;
use App\Entity\Task;
use App\Entity\TypeEquipment;
use App\Entity\TypeIntervention;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $data = [
            TypeEquipment::class   => ['Ordinateur de bureau', 'Ordinateur portable', 'Tablette', 'Smartphone', 'Imprimante', 'Serveur', 'Switch', 'SSD Externe', 'HDD Externe', 'Ecran'],
            OperatingSystem::class => ['Windows 11 Home', 'Windows 11 Pro', 'Windows 10 Pro', 'Windows 10 Home', 'Windows 8.1', 'Windows 7 Pro', 'Windows 7 Édition Familiale Basique', 'Windows 7 Édition Starter', 'Windows 7 Édition Intégrale', 'Windows XP Professionnel', 'Windows XP Familiale', 'macOS Big Sur', 'Linux Mint', 'macOS Monterey', 'Elementary OS', 'Ubuntu', 'Xubuntu', 'Xubuntu Eoan', 'Pop!_OS', 'Fedora', 'Manjaro', 'Debian', 'Arch Linux', 'Android', 'iOS', 'iPadOS'],
            Brand::class           => ['Apple', 'Samsung', 'Dell', 'HP', 'Lenovo', 'Asus', 'Acer', 'Microsoft', 'Huawei', 'Xiaomi', 'Sony', 'LG', 'OnePlus', 'Google', 'Nokia', 'Epson'],
            Status::class          => ['En attente', 'En attente de récupération', 'En traitement', 'Complété(e)', 'Annulé(e)'],
        ];

        foreach ($data as $entityClass => $values) {
            foreach ($values as $value) {
                $entity = new $entityClass();
                $entity->setName($value);
                $manager->persist($entity);
            }
        }

        $tasks = ['Tentative de réinitialisation', 'Mises à jour Windows', 'Mises à jour Mac OS', 'Mises à jour Linux', 'Installation de logiciels', 'Installation de drivers', 'Installation de périphériques', 'Installation des mises à jour et pilotes', 'Installation Windows 11', 'Installation Windows 10', 'Nettoyage de disque', 'Mise en place SSD', 'Activation de windows 10', 'Récupération de données', 'Formatage HDD', 'Installation/Activation Office 2021 Pro Plus', 'Nettoyage du boitier', 'Restauration sauvegarde HDD vers SSD', 'Nettoyage ventirad CPU', 'Sauvegarde des données utilisateur', 'Premier test de démarrage'];

        foreach ($tasks as $taskName) {
            $task = new Task();
            $task->setName($taskName);
            $task->setPrice(mt_rand(0, 50));
            $task->setDescription('');
            $manager->persist($task);
        }

        $typesIntervention = ['Assemblage', 'Devis', 'Dépannage', 'Expertise', 'Réparation', 'Visite périodique', 'Préparation de poste'];

        foreach ($typesIntervention as $typeName) {
            $typeIntervention = new TypeIntervention();
            $typeIntervention->setName($typeName);
            $typeIntervention->setCreatedAt(new \DateTimeImmutable());
            $typeIntervention->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($typeIntervention);
        }

        $manager->flush();
    }
}
