<?php

namespace App\Repository;

use App\Entity\TypeIntervention;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeIntervention>
 */
class TypeInterventionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeIntervention::class);
    }

    public function save(TypeIntervention $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TypeIntervention $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<TypeIntervention>
     */
    public function getTypeInterventions(
        ?int $id = null,
        ?string $name = null,
        ?int $companyId = null,
        int $page = 1,
        int $size = 10,
        string $order = 'asc'
    ): array {
        $qb = $this->createQueryBuilder('t');

        if ($id) {
            $qb->andWhere('t.id = :id')
                ->setParameter('id', $id);
        }

        if ($name) {
            $qb->andWhere('t.name LIKE :name')
                ->setParameter('name', '%'.$name.'%');
        }

        if ($companyId) {
            $qb->andWhere('t.Company = :companyId')
                ->setParameter('companyId', $companyId);
        }

        $qb->orderBy('t.name', $order)
            ->setFirstResult(($page - 1) * $size)
            ->setMaxResults($size);

        return $qb->getQuery()->getResult();
    }
    //    /**
    //     * @return TypeIntervention[] Returns an array of TypeIntervention objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TypeIntervention
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
