<?php

namespace App\DataFixtures;

use App\Entity\CompanySpecialization;
use App\Entity\Task;
use App\Entity\TypeIntervention;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CompanyFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Specializations
        $spec1 = new CompanySpecialization();
        $spec1->setLabel('Cybersécurité');
        $spec1->setSlug('cybersecurite');
        $manager->persist($spec1);

        $spec2 = new CompanySpecialization();
        $spec2->setLabel('Cloud');
        $spec2->setSlug('cloud');
        $manager->persist($spec2);

        // Tasks
        $task1 = new Task();
        $task1->setName('Installation firewall');
        $task1->setDescription('Mettre en place un firewall nouvelle génération.');
        $task1->setPrice(500);
        $manager->persist($task1);

        $task2 = new Task();
        $task2->setName('Formation utilisateurs');
        $task2->setDescription('Former les utilisateurs à la cybersécurité.');
        $task2->setPrice(300);
        $manager->persist($task2);

        // TypeIntervention
        $typeIntervention1 = new TypeIntervention();
        $typeIntervention1->setName('Installation');
        $typeIntervention1->setCreatedAt(new \DateTimeImmutable());
        $typeIntervention1->setUpdatedAt(new \DateTimeImmutable());
        $typeIntervention1->setDescription('Installation de matériel ou logiciel.');
        $manager->persist($typeIntervention1);

        $typeIntervention2 = new TypeIntervention();
        $typeIntervention2->setName('Maintenance');
        $typeIntervention2->setCreatedAt(new \DateTimeImmutable());
        $typeIntervention2->setUpdatedAt(new \DateTimeImmutable());
        $typeIntervention2->setDescription('Maintenance de matériel ou logiciel.');
        $manager->persist($typeIntervention2);

        // Users
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('User');
        $admin->setRole('ROLE_ADMIN');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'adminpass'));
        $admin->setCreatedAt(new \DateTimeImmutable());
        $admin->setUpdatedAt(new \DateTimeImmutable());
        $manager->persist($admin);

        $customer = new User();
        $customer->setEmail('customer@example.com');
        $customer->setFirstName('Customer');
        $customer->setLastName('User');
        $customer->setRole('ROLE_CUSTOMER');
        $customer->setPassword($this->passwordHasher->hashPassword($customer, 'customerpass'));
        $customer->setCreatedAt(new \DateTimeImmutable());
        $customer->setUpdatedAt(new \DateTimeImmutable());
        $manager->persist($customer);

        $manager->flush();
    }
}
