<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * @extends ServiceEntityRepository<Company>
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function getCompanies(
        ?int $id = null,
        bool $pending = false,
        ?int $specializationId = null,
        int $page = 1,
        int $size = 25,
        ?string $name = null,
        string $order = 'ASC',
        bool $isDeleted = false
    ): array {
        $qb = $this->createQueryBuilder('c')
            ->setFirstResult(($page - 1) * $size)
            ->setMaxResults($size)
            ->orderBy('c.id', $order);

        if ($id !== null) {
            $qb->andWhere('c.id = :id')
                ->setParameter('id', $id);
        }

        if ($pending) {
            $qb->andWhere('c.pending = true');
        }
        
        if ($specializationId !== null) {
            $qb->join('c.specialization', 's')
            ->andWhere('s.id = :specializationId')
            ->setParameter('specializationId', $specializationId);
        }

        if ($name !== null) {
            $qb->andWhere('c.name LIKE :name')
                ->setParameter('name', '%' . $name . '%');
        }

        if ($isDeleted) {
            $qb->andWhere('c.is_deleted = true');
        } else {
            $qb->andWhere('c.is_deleted = false');
        }

        return $qb->getQuery()->getResult();
        
    }
    //    /**
    //     * @return Company[] Returns an array of Company objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Company
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
