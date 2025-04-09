<?php

namespace App\Repository;

use App\Entity\Intervention;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Intervention>
 */
class InterventionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Intervention::class);
    }
        /**
     * Récupère les interventions pour un utilisateur donné.
     *
     * @param int $userId
     * @return Intervention[]
     */
    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.status', 's')
            ->addSelect('s')
            ->andWhere('i.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findByCompanyId(int $companyId, int $page, int $limit, string $order, ?string $status): array
    {
        $offset = ($page - 1) * $limit;
    
        $qb = $this->createQueryBuilder('i')
            ->where('i.company = :companyId')
            ->setParameter('companyId', $companyId)
            ->orderBy('i.created_at', $order)
            ->setFirstResult($offset)
            ->setMaxResults($limit);
    
        if ($status !== "all") {
            $qb->leftJoin('i.status', 's')
               ->andWhere('s.name = :status')
               ->setParameter('status', $status);
        }
    
        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Intervention[] Returns an array of Intervention objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Intervention
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
