<?php

namespace App\Repository;

use App\Entity\Equipment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Equipment>
 */
class EquipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipment::class);
    }

    /**
     * Trouve tous les équipements pour un utilisateur donné.
     *
     * @return Equipment[]
     */
    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.customer = :userId')
            ->orWhere('e.technician = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<Equipment>
     */
    public function getEquipments(?int $id, ?int $userId, ?int $brandId, ?int $typeEquipmentId, ?int $page, ?int $size, ?string $name, ?string $order = 'ASC', ?string $reference = ''): array
    {
        $offset = ($page - 1) * $size;
        $query = $this->createQueryBuilder('e');

        if (null !== $order) {
            $query->orderBy('e.name', $order);
        }

        $query->setFirstResult($offset)
            ->setMaxResults($size);

        if (null !== $id) {
            $query->andWhere('e.id = :id')
                ->setParameter('id', intval($id));
        }

        if (null !== $userId) {
            $query->andWhere('e.user = :user_id')
                ->setParameter('user_id', intval($userId));
        }

        if (null !== $brandId) {
            $query->andWhere('e.brand = :brand_id')
                ->setParameter('brand_id', intval($brandId));
        }

        if (null !== $typeEquipmentId) {
            $query->andWhere('e.type_equipment = :type_equipment_id')
                ->setParameter('type_equipment_id', intval($typeEquipmentId));
        }

        if (null !== $name) {
            $query->andWhere('LOWER(e.name) LIKE :name')
                ->setParameter('name', '%'.strtolower($name).'%');
        }

        if ('' !== $reference) {
            $query->andWhere('e.reference = :reference')
                ->setParameter('reference', $reference);
        }

        return $query->getQuery()->getResult();
    }
    //    /**
    //     * @return Equipment[] Returns an array of Equipment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Equipment
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
