<?php

namespace App\Repository;

use App\Entity\Statistics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Statistics>
 *
 * @method Statistics|null find($id, $lockMode = null, $lockVersion = null)
 * @method Statistics|null findOneBy(array $criteria, array $orderBy = null)
 * @method Statistics[]    findAll()
 * @method Statistics[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatisticsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Statistics::class);
    }

    public function findVisitsBetween($start, $end)
    {
        return $this->createQueryBuilder('s')
              ->andWhere('s.day between :start and :end')
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->orderBy('s.day', 'ASC')
                ->getQuery()
                ->getResult()
            ;
    }

//    /**
//     * @return Statistics[] Returns an array of Statistics objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Statistics
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
