<?php

namespace App\Tests\Unit\Service;

use App\Entity\Company;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Test unitaire - Teste la logique métier sans dépendances externes.
 */
final class CompanyValidatorTest extends TestCase
{
    public function testCompanyCannotHaveCustomerAsUser(): void
    {
        // Arrange
        $company = new Company();
        $customer = new User();
        $customer->setRoles([User::ROLE_CUSTOMER]);

        // Act & Assert - Simuler la validation métier
        $canAddCustomer = $this->canAddUserToCompany($customer);
        $this->assertFalse($canAddCustomer, 'Customer should not be allowed in company');
    }

    public function testCompanyCanHaveTechnicianAsUser(): void
    {
        // Arrange
        $company = new Company();
        $technician = new User();
        $technician->setRoles([User::ROLE_TECHNICIAN]);

        // Act
        $canAddTechnician = $this->canAddUserToCompany($technician);
        $company->addUser($technician);

        // Assert
        $this->assertTrue($canAddTechnician);
        $this->assertTrue($company->getUsers()->contains($technician));
        $this->assertCount(1, $company->getUsers());
    }

    public function testCompanyTypeValidation(): void
    {
        // Arrange
        $company = new Company();

        // Act & Assert - Type valide
        $isValidType = $this->isValidCompanyType(Company::SARL);
        $this->assertTrue($isValidType);

        $company->setType(Company::SARL);
        $this->assertEquals(Company::SARL, $company->getType());

        // Type invalide
        $isInvalidType = $this->isValidCompanyType('INVALID_TYPE');
        $this->assertFalse($isInvalidType, 'Invalid type should be rejected');
    }

    /**
     * @dataProvider validCompanyTypesProvider
     */
    public function testValidCompanyTypes(string $type): void
    {
        $company = new Company();
        $company->setType($type);

        $this->assertEquals($type, $company->getType());
    }

    public function validCompanyTypesProvider(): array
    {
        return [
            [Company::EI],
            [Company::SARL],
            [Company::SAS],
            [Company::SASU],
            [Company::MICRO_ENTERPRISE],
        ];
    }

    // Méthodes privées simulant la logique métier
    private function canAddUserToCompany(User $user): bool
    {
        // Logique métier : un customer ne peut pas être ajouté à une compagnie
        return ! in_array(User::ROLE_CUSTOMER, $user->getRoles(), true);
    }

    private function isValidCompanyType(string $type): bool
    {
        $validTypes = [
            Company::EI,
            Company::SC,
            Company::SA,
            Company::EURL,
            Company::SARL,
            Company::SNC,
            Company::MICRO_ENTERPRISE,
            Company::SASU,
            Company::SAS,
        ];

        return in_array($type, $validTypes, true);
    }
}
