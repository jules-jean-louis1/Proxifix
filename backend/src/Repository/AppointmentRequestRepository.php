<?php

namespace App\Repository;

use App\Entity\AppointmentRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AppointmentRequest>
 */
class AppointmentRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppointmentRequest::class);
    }

    /**
     * @return array<AppointmentRequest>
     */
    public function getAppointementByStatus(string $status, ?int $page, ?int $size, ?int $companyId = null): array
    {
        $offset = ($page - 1) * $size;
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.status = :status')
            ->setParameter('status', $status)
            ->orderBy('a.date', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($size);

        if (null !== $companyId) {
            $qb->andWhere('a.company = :company_id')
                ->setParameter('company_id', $companyId);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<AppointmentRequest>
     */
    public function getAppointements(
        int $page,
        int $size,
        ?int $userId = null,
        ?int $appointementId = null,
        ?string $status = null,
        ?\DateTime $date = null,
        ?string $order = null,
        ?int $companyId = null,
        ?int $id = null
    ): array {
        $offset = ($page - 1) * $size;
        $query = $this->createQueryBuilder('a');

        if (null !== $order) {
            $query->orderBy('a.created_at', $order);
        }

        $query->setFirstResult($offset)
            ->setMaxResults($size);

        if (null !== $id) {
            $query->andWhere('a.id = :id')
                ->setParameter('id', $id);
        }

        if (null !== $appointementId) {
            $query->andWhere('a.id = :appointment_id')
                ->setParameter('appointment_id', $appointementId);
        }
        if (null !== $status) {
            $query->andWhere('a.status = :status')
                ->setParameter('status', $status);
        }
        if (null !== $date) {
            $query->andWhere('a.date = :date')
                ->setParameter('date', $date);
        }
        if (null !== $userId) {
            $query->andWhere('a.customer = :user_id')
                ->setParameter('user_id', $userId);
        }
        if (null !== $companyId) {
            $query->andWhere('a.company = :company_id')
                ->setParameter('company_id', $companyId);
        }

        return $query->getQuery()->getResult();
    }

    //    /**
    //     * @return AppointmentRequest[] Returns an array of AppointmentRequest objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?AppointmentRequest
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
