<?php
namespace App\Repository;

use App\Entity\AppointmentEquipment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AppointmentEquipment>
 */
class AppointmentEquipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppointmentEquipment::class);
    }
    public function findEquipmentsByAppointmentId(int $appointmentId): array
    {
        return $this->createQueryBuilder('ae')
            ->select('e.id')
            ->innerJoin('ae.equipment', 'e')
            ->innerJoin('ae.appointment', 'a')
            ->where('a.id = :appointmentId')
            ->setParameter('appointmentId', $appointmentId)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return AppointmentEquipment[] Returns an array of AppointmentEquipment objects
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

    //    public function findOneBySomeField($value): ?AppointmentEquipment
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
