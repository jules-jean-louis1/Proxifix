<?php

namespace App\Repository;

use App\Entity\Booking;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function getFreeSlots(\DateTime $date, ?int $companyId = null, ?int $interval_min = null): array
    {
        $conn = $this->getEntityManager()->getConnection();
    
        $query = <<<SQL
            SELECT * 
            FROM get_free_slots(:date, :company_id, :interval)
        SQL;
    
        $result = $conn->executeQuery($query, [
            'date' => $date->format('Y-m-d'),
            'company_id' => $companyId ?? 0,
            'interval' => $interval_min ?? 60,
        ]);
    
        return $result->fetchAllAssociative();
    }

    public function isSlotsAvailable(int $companyId, string|DateTimeImmutable $start_date, string|DateTimeImmutable $end_date = null): bool
    {
        $start_date = $start_date instanceof DateTimeImmutable ? $start_date : new DateTimeImmutable($start_date);
        $end_date = $end_date instanceof DateTimeImmutable ? $end_date : $start_date->add(new \DateInterval('PT1H'));
    
        $qb = $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.company = :companyId')
            ->andWhere('b.start_date < :end_date')
            ->andWhere('b.end_date > :start_date')
            ->setParameter('companyId', $companyId)
            ->setParameter('start_date', $start_date)
            ->setParameter('end_date', $end_date);
    
        $count = $qb->getQuery()->getSingleScalarResult();
    
        // Si aucun booking ne chevauche le créneau demandé, le créneau est disponible
        return $count == 0;
    }
    //    /**
    //     * @return Booking[] Returns an array of Booking objects
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

    //    public function findOneBySomeField($value): ?Booking
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
