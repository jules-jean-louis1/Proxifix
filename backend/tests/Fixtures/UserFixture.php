<?php

namespace App\Tests\Fixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $superAdmin = new User();
        $superAdmin->setEmail('superadmin@test.com');
        $superAdmin->setRoles(['ROLE_SUPER_ADMIN']);
        $superAdmin->setPassword($this->hasher->hashPassword($superAdmin, 'superadminpass'));
        $manager->persist($superAdmin);

        $admin = new User();
        $admin->setEmail('admin@test.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'adminpass'));
        $manager->persist($admin);

        $customer = new User();
        $customer->setEmail('customer@test.com');
        $customer->setRoles(['ROLE_CUSTOMER']);
        $customer->setPassword($this->hasher->hashPassword($customer, 'customerpass'));
        $manager->persist($customer);

        $technician = new User();
        $technician->setEmail('technician@test.com');
        $technician->setRoles(['ROLE_TECHNICIAN']);
        $technician->setPassword($this->hasher->hashPassword($technician, 'technicianpass'));
        $manager->persist($technician);

        $manager->flush();
    }
}
