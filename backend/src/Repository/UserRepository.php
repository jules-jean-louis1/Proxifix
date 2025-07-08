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
        if (!$user instanceof User) {
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
            ->where("JSON_GET_TEXT(u.roles, 0) = :role")
            ->setParameter('role', 'ROLE_CUSTOMER')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function searchCustomer(string $searchQuery)
    {
        return $this->createQueryBuilder('u')
            ->where("JSON_GET_TEXT(u.roles, 0) = :role")
            ->andWhere("u.first_name = :searchQuery")
            ->orWhere("u.last_name = :searchQuery")
            ->setParameter('role', 'ROLE_CUSTOMER')
            ->setParameter('searchQuery', $searchQuery)
            ->getQuery()
            ->getResult();
    }

    public function getUsers(?int $id, ?string $searchQuery = "", ?int $page = 1, ?int $size = 25, ?string $order = "", ?string $role = "ROLE_CUSTOMER"): array
    {
        $query = $this->createQueryBuilder('u')
            ->setFirstResult(($page - 1) * $size)
            ->setMaxResults($size);

        if ($searchQuery) {
            $query->andWhere('UPPER(u.first_name) LIKE UPPER(:searchQuery)')
                ->orWhere('UPPER(u.last_name) LIKE UPPER(:searchQuery)')
                ->orWhere('UPPER(u.email) LIKE UPPER(:searchQuery)')
                ->setParameter('searchQuery', '%' . $searchQuery . '%');
        }

        if ($order) {
            $query->orderBy('u.:order', 'ASC')
                ->setParameter('order', $order);
        }

        if ($id) {
            $query->andWhere('u.id = :id')
                ->setParameter('id', $id);
        }

        if ($role) {
            $query->andWhere('JSON_GET_TEXT(u.roles, 0) = :role')
                ->setParameter('role', $role);
        }
        return $query->getQuery()->getResult();
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
