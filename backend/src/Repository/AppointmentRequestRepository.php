<?php
namespace App\Repository;

use App\Entity\AppointmentRequest;
use DateTime;
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

    public function getAppointementByStatus(string $status, ?int $companyId = null, ?int $page, ?int $size): array
    {
        $offset = ($page - 1) * $size;
        $qb     = $this->createQueryBuilder('a')
            ->andWhere('a.status = :status')
            ->setParameter('status', $status)
            ->orderBy('a.date', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($size);

        if ($companyId !== null) {
            $qb->andWhere('a.company = :company_id')
                ->setParameter('company_id', $companyId);
        }

        return $qb->getQuery()->getResult();
    }

    public function getAppointements(
        int $companyId,
        int $page,
        int $size,
        ?int $userId = null,
        ?int $appointementId = null,
        ?string $status = null,
        ?DateTime $date = null,
        ?string $order = null
    ): array {
        $offset = ($page - 1) * $size;
        $query  = $this->createQueryBuilder('a')
            ->andWhere('a.company = :company_id')
            ->setParameter('company_id', $companyId);

        if ($order !== null) {
            $query->orderBy('a.created_at', $order);
        }

        $query->setFirstResult($offset)
            ->setMaxResults($size);

        if ($appointementId !== null) {
            $query->andWhere('a.id = :appointment_id')
                ->setParameter('appointment_id', $appointementId);
        }
        if ($status !== null) {
            $query->andWhere('a.status = :status')
                ->setParameter('status', $status);
        }
        if ($date !== null) {
            $query->andWhere('a.date = :date')
                ->setParameter('date', $date);
        }
        if ($userId !== null) {
            $query->andWhere('a.user = :user_id')
                ->setParameter('user_id', $userId);
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
