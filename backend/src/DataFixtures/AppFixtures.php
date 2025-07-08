<?php
namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Company;
use App\Entity\CompanySpecialization;
use App\Entity\Equipment;
use App\Entity\OperatingSystem;
use App\Entity\Task;
use App\Entity\TypeEquipment;
use App\Entity\TypeIntervention;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create a default company
        // This is just an example, you can modify the details as needed
        $company = new Company();
        $company->setName('Tech Solutions');
        $company->setAbout('Tech Solutions est une entreprise spécialisée dans la réparation et la maintenance de matériel informatique. Nous offrons des services de qualité pour les particuliers et les entreprises.');
        $company->setType(Company::SARL);
        $company->setAddress('123 Rue de l\\\'Innovation');
        $company->setZipCode('75001');
        $company->setLogo('logo.png');
        $company->setPhone('0123456789');
        $company->setMobile('0987654321');
        $company->setWebsite('https://www.techsolutions.com');
        $company->setCity('Paris');
        $company->setIsDelete(false);
        $company->setCreatedAt(new \DateTimeImmutable());
        $company->setUpdatedAt(new \DateTimeImmutable());
        // Create a default user admin
        $adminUser = new User();
        $adminUser->setEmail('admin@techsolutions.com');
        $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, 'password'));
        $adminUser->setRoles(['ROLE_SUPER_ADMIN']);
        $adminUser->setFirstName('Admin');
        $adminUser->setLastName('User');
        $adminUser->setCompany($company);
        $manager->persist($adminUser);

        $data = [
            TypeEquipment::class   => ['Ordinateur de bureau', 'Ordinateur portable', 'Tablette', 'Smartphone', 'Imprimante', 'Serveur', 'Switch', 'SSD Externe', 'HDD Externe', 'Ecran'],
            OperatingSystem::class => ['Windows 11 Home', 'Windows 11 Pro', 'Windows 10 Pro', 'Windows 10 Home', 'Windows 8.1', 'Windows 7 Pro', 'Windows 7 Édition Familiale Basique', 'Windows 7 Édition Starter', 'Windows 7 Édition Intégrale', 'Windows XP Professionnel', 'Windows XP Familiale', 'macOS Big Sur', 'Linux Mint', 'macOS Monterey', 'Elementary OS', 'Ubuntu', 'Xubuntu', 'Xubuntu Eoan', 'Pop!_OS', 'Fedora', 'Manjaro', 'Debian', 'Arch Linux', 'Android', 'iOS', 'iPadOS'],
            Brand::class           => ['Apple', 'Samsung', 'Dell', 'HP', 'Lenovo', 'Asus', 'Acer', 'Microsoft', 'Huawei', 'Xiaomi', 'Sony', 'LG', 'OnePlus', 'Google', 'Nokia', 'Epson'],
        ];

        foreach ($data as $entityClass => $values) {
            foreach ($values as $value) {
                $entity = new $entityClass();
                $entity->setName($value);
                if ($entityClass === TypeEquipment::class) {
                    $entity->setCompany($company);
                }
                $manager->persist($entity);
            }
        }

        $tasks = ['Tentative de réinitialisation', 'Mises à jour Windows', 'Mises à jour Mac OS', 'Mises à jour Linux', 'Installation de logiciels', 'Installation de drivers', 'Installation de périphériques', 'Installation des mises à jour et pilotes', 'Installation Windows 11', 'Installation Windows 10', 'Nettoyage de disque', 'Mise en place SSD', 'Activation de windows 10', 'Récupération de données', 'Formatage HDD', 'Installation/Activation Office 2021 Pro Plus', 'Nettoyage du boitier', 'Restauration sauvegarde HDD vers SSD', 'Nettoyage ventirad CPU', 'Sauvegarde des données utilisateur', 'Premier test de démarrage'];

        foreach ($tasks as $taskName) {
            $task = new Task();
            $task->setName($taskName);
            $task->setPrice(mt_rand(0, 50));
            $task->setDescription('');
            $task->setCompany($company);
            $manager->persist($task);
        }

        $typesIntervention = ['Assemblage', 'Devis', 'Dépannage', 'Expertise', 'Réparation', 'Visite périodique', 'Préparation de poste'];

        foreach ($typesIntervention as $typeName) {
            $typeIntervention = new TypeIntervention();
            $typeIntervention->setName($typeName);
            $typeIntervention->setCreatedAt(new \DateTimeImmutable());
            $typeIntervention->setUpdatedAt(new \DateTimeImmutable());
            $typeIntervention->setCompany($company);
            $manager->persist($typeIntervention);
        }

        $specializations = ['Informatique', 'Téléphonie', 'Réseau', 'Sécurité', 'Bureautique', 'Domotique', 'Maintenance', 'Photocopieur'];
        foreach ($specializations as $specializationName) {
            $specialization = new CompanySpecialization();
            $specialization->setLabel($specializationName);
            $specialization->setSlug(strtolower(str_replace(' ', '-', $specializationName)));
            $specialization->addCompany($company);
            $manager->persist($specialization);
        }

        $superAdmin = new User();
        $superAdmin->setEmail('superadmin@test.com');
        $superAdmin->setRoles(['ROLE_SUPER_ADMIN']);
        $superAdmin->setFirstName('Super');
        $superAdmin->setLastName('Admin');
        $superAdmin->setPassword($this->passwordHasher->hashPassword($superAdmin, 'superadminpass'));
        $manager->persist($superAdmin);

        $admin = new User();
        $admin->setEmail('admin@test.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setFirstName('Admin');
        $admin->setLastName('User');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'adminpass'));
        $manager->persist($admin);

        
        // Create a technician user
        $technician = new User();
        $technician->setEmail('technician@test.com');
        $technician->setRoles(['ROLE_TECHNICIAN']);
        $technician->setFirstName('Technician');
        $technician->setLastName('User');
        $technician->setPassword($this->passwordHasher->hashPassword($technician, 'technicianpass'));
        $manager->persist($technician);
        
        for ($i = 1; $i <= 10; $i++) {
            $customer = new User();
            $customer->setEmail('customer' . $i . '@test.com');
            $customer->setRoles(['ROLE_CUSTOMER']);
            $customer->setFirstName('Customer' . $i);
            $customer->setLastName('User');
            $customer->setPassword($this->passwordHasher->hashPassword($customer, 'customerpass'));
            $manager->persist($customer);
        }

        for ($i = 1; $i <= 5; $i++) {
            $equipment = new Equipment();
            $equipment->setName('Equipment ' . $i);
            $equipment->setReference('SN' . str_pad($i, 5, '0', STR_PAD_LEFT));
            $equipment->setUser($customer);
            $manager->persist($equipment);
        }
        

        $manager->flush();
    }
}