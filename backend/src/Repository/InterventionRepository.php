<?php
namespace App\Repository;

use App\Entity\Intervention;
use DateTimeImmutable;
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

    public function getFreeSlots(\DateTime $date, ?int $companyId = null, ?int $interval_min = null, ?string $startTime, ?string $endTime, ?string $role): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $query = <<<SQL
            SELECT *
            FROM get_free_slots(:date, :company_id, :interval)
        SQL;

        $result = $conn->executeQuery($query, [
            'p_date'       => $date->format('Y-m-d'),
            'p_company_id' => $companyId ?? 0,
            'p_interval_minutes'   => $interval_min ?? 60,
        ]);

        return $result->fetchAllAssociative();
    }

    public function isSlotsAvailable(int $companyId, string | DateTimeImmutable $start_date, string | DateTimeImmutable $end_date = null): bool
    {

        $start_date = $start_date instanceof DateTimeImmutable ? $start_date : new DateTimeImmutable($start_date);
        $end_date   = $end_date instanceof DateTimeImmutable ? $end_date : $start_date->add(new \DateInterval('PT1H')); // Par défaut, 1 heure

        $qb = $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.company = :companyId')
            ->andWhere('i.start_date < :end_date')
            ->andWhere('i.end_date > :start_date')
            ->setParameter('companyId', $companyId)
            ->setParameter('start_date', $start_date)
            ->setParameter('end_date', $end_date);

        $count = $qb->getQuery()->getSingleScalarResult();

        return $count == 0;
    }

    public function getInterventions(
        ?int $id = null,
        ?int $userId = null,
        ?int $companyId = null,
        ?string $status = null,
        ?int $page = 1,
        ?string $order = 'ASC',
        ?int $typeInterventionId = null,
        ?int $size = 10
    ): array {
        $offset = ($page - 1) * $size;
        $query  = $this->createQueryBuilder('i');

        if ($order !== null) {
            $query->orderBy('i.created_at', $order);
        }
        $query->setFirstResult($offset)
            ->setMaxResults($size);

        if ($id !== null) {
            $query->andWhere('i.id = :id')
                ->setParameter('id', intval($id));
        }

        if ($userId !== null) {
            $query->andWhere('i.user = :user_id')
                ->setParameter('user_id', intval($userId));
        }

        if ($companyId !== null) {
            $query->andWhere('i.company = :company_id')
                ->setParameter('company_id', intval($companyId));
        }

        if ($status !== null && $status !== "all") {
            $query->leftJoin('i.status', 's')
                ->andWhere('s.name = :status')
                ->setParameter('status', $status);
        }

        if ($typeInterventionId !== null) {
            $query->andWhere('i.typeIntervention = :type_intervention_id')
                ->setParameter('type_intervention_id', intval($typeInterventionId));
        }

        return $query->getQuery()->getResult();
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
