<?php

namespace App\Repository;

use App\Entity\PagesTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PagesType>
 *
 * @method PagesType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PagesType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PagesType[]    findAll()
 * @method PagesType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagesTypesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PagesTypes::class);
    }

//    /**
//     * @return PagesType[] Returns an array of PagesType objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PagesType
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
