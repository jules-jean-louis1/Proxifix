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

    public function customerList(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        return $this->createQueryBuilder('u')
            ->where('JSON_GET_TEXT(u.roles, 0) = :role')
            ->setParameter('role', 'ROLE_CUSTOMER')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function searchCustomer(string $searchQuery)
    {
        return $this->createQueryBuilder('u')
            ->where('JSON_GET_TEXT(u.roles, 0) = :role')
            ->andWhere('u.first_name = :searchQuery')
            ->orWhere('u.last_name = :searchQuery')
            ->setParameter('role', 'ROLE_CUSTOMER')
            ->setParameter('searchQuery', $searchQuery)
            ->getQuery()
            ->getResult();
    }

    public function getUsers(?int $companyId = null, ?string $searchQuery = '', ?int $page = 1, ?int $size = 25, ?string $order = '', ?string $role = 'ROLE_CUSTOMER'): array
    {
        $sql = 'SELECT u.* FROM "user" u WHERE 1=1';
        $params = [];

        if (null !== $companyId) {
            $sql .= ' AND u.company_id = :companyId';
            $params['companyId'] = $companyId;
        }

        if ($searchQuery) {
            $sql .= ' AND (UPPER(u.first_name) LIKE UPPER(:searchQuery) OR UPPER(u.last_name) LIKE UPPER(:searchQuery) OR UPPER(u.email) LIKE UPPER(:searchQuery))';
            $params['searchQuery'] = '%'.$searchQuery.'%';
        }

        if ($role) {
            $sql .= ' OR CAST(u.roles AS text) LIKE :rolePattern';
            $params['rolePattern'] = '%"'.$role.'"%';
        }

        $sql .= ' ORDER BY u.created_at DESC LIMIT :size OFFSET :offset';
        $params['size'] = $size;
        $params['offset'] = ($page - 1) * $size;

        $connection = $this->getEntityManager()->getConnection();
        $result = $connection->executeQuery($sql, $params);

        // Convertir les résultats en entités User
        $users = [];
        foreach ($result->fetchAllAssociative() as $row) {
            $users[] = $this->find($row['id']);
        }

        return $users;
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
