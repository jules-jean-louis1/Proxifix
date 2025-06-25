<?php

namespace App\Repository;

use App\Entity\TypeEquipment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeEquipment>
 */
class TypeEquipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeEquipment::class);
    }

    public function getTypes(?int $id, ?int $page, ?int $size, ?string $name, ?string $order = 'ASC'): array
    {
        $offset = ($page - 1) * $size;
        $query  = $this->createQueryBuilder('t');

        if ($id !== null) {
            $query->andWhere('t.id = :id')
                ->setParameter('id', intval($id));
        }

        if ($name !== null && $name !== "") {
            $query->andWhere('LOWER(t.name) LIKE :name')
                ->setParameter('name', '%' . strtolower($name) . '%');
        }

        if ($order !== null) {
            $query->orderBy('t.name', $order);
        }
        // TODO: Add company filter
        // if ($companyId !== null) {
        //     $query->andWhere('t.company = :company_id')
        //         ->setParameter('company_id', $companyId);
        // }

        $query->setFirstResult($offset)
            ->setMaxResults($size);

        return $query->getQuery()->getResult();
    }

    //    /**
    //     * @return TypeEquipment[] Returns an array of TypeEquipment objects
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

    //    public function findOneBySomeField($value): ?TypeEquipment
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
