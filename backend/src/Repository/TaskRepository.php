<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @return array<Task>
     */
    /**
     * @return array<Task>
     */
    public function getTasks(?int $id, ?int $companyId, ?string $name, ?int $page, ?int $size, ?string $order): array
    {
        $offset = ($page - 1) * $size;
        $query = $this->createQueryBuilder('t');

        if (null !== $id) {
            $query->andWhere('t.id = :id')
                ->setParameter('id', intval($id));
        }
        if (null !== $name && '' !== $name) {
            $query->andWhere('LOWER(t.name) LIKE :name')
                ->setParameter('name', '%'.strtolower($name).'%');
        }
        if (null !== $order) {
            $query->orderBy('t.name', $order);
        }
        // if ($companyId !== null) {
        //     $query->andWhere('t.company = :company_id')
        //         ->setParameter('company_id', $companyId);
        // }

        $query->setFirstResult($offset)
            ->setMaxResults($size);

        return $query->getQuery()->getResult();
    }

    //    /**
    //     * @return Task[] Returns an array of Task objects
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

    //    public function findOneBySomeField($value): ?Task
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
