<?php

namespace App\Repository;

use App\Entity\Brand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Brand>
 */
class BrandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Brand::class);
    }

    /**
     * @return array<Brand>
     */
    public function getBrands(?int $id, int $page, int $size, ?string $name, string $order): array
    {
        $offset = ($page - 1) * $size;
        $query = $this->createQueryBuilder('b');

        if (null !== $id) {
            $query->andWhere('b.id = :id')
                ->setParameter('id', $id);
        }

        if (null !== $name && '' !== $name) {
            $query->andWhere('LOWER(b.name) LIKE :name')
                ->setParameter('name', '%'.strtolower($name).'%');
        }

        $query->orderBy('b.name', $order);

        $query->setFirstResult($offset)
            ->setMaxResults($size);

        return $query->getQuery()->getResult();
    }

    //    /**
    //     * @return Brand[] Returns an array of Brand objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Brand
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function checkBrandName(string $name): bool
    {
        $brand = $this->findOneBy(['name' => $name]);

        return null !== $brand;
    }
}
