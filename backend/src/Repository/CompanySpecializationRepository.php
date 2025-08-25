<?php

namespace App\Repository;

use App\Entity\CompanySpecialization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CompanySpecialization>
 */
class CompanySpecializationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanySpecialization::class);
    }

    /**
     * @return CompanySpecialization[]
     */
    public function get(?int $id = null, ?string $label = null): array
    {
        $qb = $this->createQueryBuilder('cs');

        if (null !== $id) {
            $qb->andWhere('cs.id = :id')
                ->setParameter('id', $id);
        }

        if (null !== $label) {
            $qb->andWhere('LOWER(cs.label) = :label')
                ->setParameter('label', strtolower($label));
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return CompanySpecialization[] Returns an array of CompanySpecialization objects
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

    //    public function findOneBySomeField($value): ?CompanySpecialization
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
