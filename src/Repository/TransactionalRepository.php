<?php

namespace App\Repository;

use App\Entity\Transactional;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transactional>
 *
 * @method Transactional|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transactional|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transactional[]    findAll()
 * @method Transactional[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transactional::class);
    }

//    /**
//     * @return Transactional[] Returns an array of Transactional objects
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

//    public function findOneBySomeField($value): ?Transactional
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
