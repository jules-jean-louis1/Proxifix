<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (! $user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @return array<User>
     */
    public function customerList(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        return $this->createQueryBuilder('u')
            ->where('u.role = :role')
            ->setParameter('role', 'ROLE_CUSTOMER')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<User>
     */
    public function searchCustomer(string $searchQuery): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.role = :role')
            ->andWhere('u.first_name = :searchQuery')
            ->orWhere('u.last_name = :searchQuery')
            ->setParameter('role', 'ROLE_CUSTOMER')
            ->setParameter('searchQuery', $searchQuery)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<User>
     */
    public function getUsers(?int $companyId = null, ?string $searchQuery = '', ?int $page = 1, ?int $size = 25, ?string $order = '', ?string $role = 'ROLE_CUSTOMER', ?int $customerCompanyId = null): array
    {
        $qb = $this->createQueryBuilder('u');
        
        // Filtre par company_id si fourni
        if (null !== $companyId) {
            $qb->andWhere('u.company = :companyId')
               ->setParameter('companyId', $companyId);
        }

        // Recherche par nom, prénom ou email
        if (!empty($searchQuery)) {
            $qb->andWhere('(UPPER(u.first_name) LIKE UPPER(:searchQuery) OR UPPER(u.last_name) LIKE UPPER(:searchQuery) OR UPPER(u.email) LIKE UPPER(:searchQuery))')
               ->setParameter('searchQuery', '%' . $searchQuery . '%');
        }

        // Filtre par rôle
        if ($role) {
            $qb->andWhere('u.role = :role')
               ->setParameter('role', $role);
        }

        // Filtrer par les clients qui ont des interventions ou des rendez-vous avec une entreprise spécifique
        if ($customerCompanyId) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->exists('SELECT 1 FROM App\Entity\Intervention i WHERE i.customer = u.id AND i.company = :customerCompanyId'),
                $qb->expr()->exists('SELECT 1 FROM App\Entity\AppointmentRequest ar WHERE ar.user = u.id AND ar.company = :customerCompanyId')
            ))
            ->setParameter('customerCompanyId', $customerCompanyId);
        }

        // Pagination
        $offset = ($page - 1) * $size;
        $qb->setFirstResult($offset)
           ->setMaxResults($size);

        // Ordre
        $qb->orderBy('u.created_at', 'DESC');

        return $qb->getQuery()->getResult();
    }
    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
