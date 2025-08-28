<?php

namespace App\DataFixtures;

use App\Entity\AppointmentRequest;
use App\Entity\Brand;
use App\Entity\Company;
use App\Entity\CompanySpecialization;
use App\Entity\Equipment;
use App\Entity\Intervention;
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
        $adminUser->setEmail('super_admin@test.com');
        $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, 'password'));
        $adminUser->setRole('ROLE_SUPER_ADMIN');
        $adminUser->setFirstName('Super');
        $adminUser->setLastName('Admin');
        $manager->persist($adminUser);

        $data = [
            TypeEquipment::class => ['Ordinateur de bureau', 'Ordinateur portable', 'Tablette', 'Smartphone', 'Imprimante', 'Serveur', 'Switch', 'SSD Externe', 'HDD Externe', 'Ecran'],
            OperatingSystem::class => ['Windows 11 Home', 'Windows 11 Pro', 'Windows 10 Pro', 'Windows 10 Home', 'Windows 8.1', 'Windows 7 Pro', 'Windows 7 Édition Familiale Basique', 'Windows 7 Édition Starter', 'Windows 7 Édition Intégrale', 'Windows XP Professionnel', 'Windows XP Familiale', 'macOS Big Sur', 'Linux Mint', 'macOS Monterey', 'Elementary OS', 'Ubuntu', 'Xubuntu', 'Xubuntu Eoan', 'Pop!_OS', 'Fedora', 'Manjaro', 'Debian', 'Arch Linux', 'Android', 'iOS', 'iPadOS'],
            Brand::class => ['Apple', 'Samsung', 'Dell', 'HP', 'Lenovo', 'Asus', 'Acer', 'Microsoft', 'Huawei', 'Xiaomi', 'Sony', 'LG', 'OnePlus', 'Google', 'Nokia', 'Epson'],
        ];

        foreach ($data as $entityClass => $values) {
            foreach ($values as $value) {
                switch ($entityClass) {
                    case TypeEquipment::class:
                        $entity = new TypeEquipment();
                        $entity->setName($value);
                        $entity->setCompany($company);

                        break;
                    case Brand::class:
                        $entity = new Brand();
                        $entity->setName($value);

                        break;
                    case OperatingSystem::class:
                        $entity = new OperatingSystem();
                        $entity->setName($value);

                        break;
                    default:
                        continue 2; // Skip unknown entity types
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
        $superAdmin->setRole('ROLE_SUPER_ADMIN');
        $superAdmin->setFirstName('Super');
        $superAdmin->setLastName('Admin');
        $superAdmin->setPassword($this->passwordHasher->hashPassword($superAdmin, 'superadminpass'));
        $manager->persist($superAdmin);

        $admin = new User();
        $admin->setEmail('admin@test.com');
        $admin->setRole('ROLE_ADMIN');
        $admin->setFirstName('Admin');
        $admin->setLastName('User');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'adminpass'));
        $manager->persist($admin);

        $adminTechSolution = new User();
        $adminTechSolution->setEmail('admin@techsolutions.com');
        $adminTechSolution->setRole('ROLE_ADMIN');
        $adminTechSolution->setFirstName('Admin');
        $adminTechSolution->setLastName('User');
        $adminTechSolution->setPassword($this->passwordHasher->hashPassword($admin, 'adminpass'));
        $adminTechSolution->setCompany($company);
        $manager->persist($adminTechSolution);

        $technicianTechSolution = new User();
        $technicianTechSolution->setEmail('technician@techsolutions.com');
        $technicianTechSolution->setRole('ROLE_TECHNICIAN');
        $technicianTechSolution->setFirstName('Technician');
        $technicianTechSolution->setLastName('User');
        $technicianTechSolution->setPassword($this->passwordHasher->hashPassword($technicianTechSolution, 'technicianpass'));
        $technicianTechSolution->setCompany($company);
        $manager->persist($technicianTechSolution);

        // Create a technician user
        $technician = new User();
        $technician->setEmail('technician@test.com');
        $technician->setRole('ROLE_TECHNICIAN');
        $technician->setFirstName('Technician');
        $technician->setLastName('User');
        $technician->setPassword($this->passwordHasher->hashPassword($technician, 'technicianpass'));
        $manager->persist($technician);

        // Create customers with equipment, appointments, and interventions
        $customers = [];
        for ($i = 1; $i <= 10; ++$i) {
            $customer = new User();
            $customer->setEmail('customer'.$i.'@test.com');
            $customer->setRole('ROLE_CUSTOMER');
            $customer->setFirstName('Customer'.$i);
            $customer->setLastName('User');
            $customer->setPassword($this->passwordHasher->hashPassword($customer, 'customerpass'));
            $customer->setPhone('0'.rand(100000000, 999999999));
            $customer->setAddress(rand(1, 999).' Rue des Clients');
            $customer->setCity('Paris');
            $customer->setZipcode('750'.sprintf('%02d', rand(1, 20)));
            $manager->persist($customer);
            $customers[] = $customer;
        }

        // Create equipment for customers
        $equipments = [];
        foreach ($customers as $index => $customer) {
            for ($j = 1; $j <= 2; ++$j) {
                $equipment = new Equipment();
                $equipment->setName('Equipment '.$j.' - '.$customer->getFirstName());
                $equipment->setReference('SN'.str_pad((string) (($index * 2) + $j), 5, '0', STR_PAD_LEFT));
                $equipment->setUser($customer);
                $manager->persist($equipment);
                $equipments[] = $equipment;
            }
        }

        // Create appointments and interventions for first 2 customers with TechSolutions
        $typeIntervention = $manager->getRepository(TypeIntervention::class)->findOneBy(['name' => 'Dépannage']) ?? new TypeIntervention();
        if (! $typeIntervention->getId()) {
            $typeIntervention->setName('Dépannage');
            $typeIntervention->setCreatedAt(new \DateTimeImmutable());
            $typeIntervention->setUpdatedAt(new \DateTimeImmutable());
            $typeIntervention->setCompany($company);
            $manager->persist($typeIntervention);
        }

        // Customer 1 - Appointment and Intervention
        $customer1 = $customers[0];
        $equipment1 = $equipments[0]; // First equipment of customer 1

        // Appointment for customer 1
        $appointment1 = new AppointmentRequest();
        $appointment1->setTitle('Réparation PC portable - '.$customer1->getFirstName());
        $appointment1->setDescription('PC qui ne démarre plus, écran noir au démarrage');
        $appointment1->setUser($customer1);
        $appointment1->setCompany($company);
        $appointment1->setEquipment($equipment1);
        $appointment1->setStatus(AppointmentRequest::ACCEPTED);
        $appointment1->setCreatedAt(new \DateTimeImmutable('-7 days'));
        $appointment1->setUpdatedAt(new \DateTimeImmutable('-7 days'));
        $appointment1->setDate(new \DateTimeImmutable('+2 days'));
        $manager->persist($appointment1);

        // Intervention for customer 1
        $intervention1 = new Intervention();
        $intervention1->setTitle('Dépannage PC - '.$customer1->getFirstName());
        $intervention1->setDescription('Diagnostic et réparation du PC portable');
        $intervention1->setCustomer($customer1);
        $intervention1->setTechnician($technicianTechSolution);
        $intervention1->setCompany($company);
        $intervention1->setEquipment($equipment1);
        $intervention1->setAppointmentRequest($appointment1);
        $intervention1->setTypeIntervention($typeIntervention);
        $intervention1->setStatus(Intervention::IN_PROGRESS);
        $intervention1->setCreatedAt(new \DateTimeImmutable('-3 days'));
        $intervention1->setUpdatedAt(new \DateTimeImmutable('-1 day'));
        $intervention1->setStartDate(new \DateTimeImmutable('-3 days'));
        $manager->persist($intervention1);

        // Customer 2 - Appointment and Intervention
        $customer2 = $customers[1];
        $equipment2 = $equipments[2]; // First equipment of customer 2

        // Appointment for customer 2
        $appointment2 = new AppointmentRequest();
        $appointment2->setTitle('Installation SSD - '.$customer2->getFirstName());
        $appointment2->setDescription('Remplacement du disque dur par un SSD et migration des données');
        $appointment2->setUser($customer2);
        $appointment2->setCompany($company);
        $appointment2->setEquipment($equipment2);
        $appointment2->setStatus(AppointmentRequest::PENDING);
        $appointment2->setCreatedAt(new \DateTimeImmutable('-2 days'));
        $appointment2->setUpdatedAt(new \DateTimeImmutable('-2 days'));
        $appointment2->setDate(new \DateTimeImmutable('+5 days'));
        $manager->persist($appointment2);

        // Intervention for customer 2
        $intervention2 = new Intervention();
        $intervention2->setTitle('Upgrade SSD - '.$customer2->getFirstName());
        $intervention2->setDescription('Installation SSD et migration des données système');
        $intervention2->setCustomer($customer2);
        $intervention2->setTechnician($adminTechSolution);
        $intervention2->setCompany($company);
        $intervention2->setEquipment($equipment2);
        $intervention2->setAppointmentRequest($appointment2);
        $intervention2->setTypeIntervention($typeIntervention);
        $intervention2->setStatus(Intervention::ASSIGNED);
        $intervention2->setCreatedAt(new \DateTimeImmutable('-1 day'));
        $intervention2->setUpdatedAt(new \DateTimeImmutable('-1 day'));
        $manager->persist($intervention2);

        // Additional intervention without appointment for customer 1
        $intervention3 = new Intervention();
        $intervention3->setTitle('Maintenance préventive - '.$customer1->getFirstName());
        $intervention3->setDescription('Nettoyage et mise à jour du système');
        $intervention3->setCustomer($customer1);
        $intervention3->setCompany($company);
        $intervention3->setEquipment($equipments[1]); // Second equipment of customer 1
        $intervention3->setTypeIntervention($typeIntervention);
        $intervention3->setStatus(Intervention::COMPLETED);
        $intervention3->setCreatedAt(new \DateTimeImmutable('-10 days'));
        $intervention3->setUpdatedAt(new \DateTimeImmutable('-8 days'));
        $intervention3->setStartDate(new \DateTimeImmutable('-10 days'));
        $intervention3->setEndDate(new \DateTimeImmutable('-8 days'));
        $manager->persist($intervention3);

        $manager->flush();
    }
}
