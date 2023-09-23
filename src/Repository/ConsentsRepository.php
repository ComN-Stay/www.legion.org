<?php

namespace App\Repository;

use App\Entity\Consents;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Consents>
 *
 * @method Consents|null find($id, $lockMode = null, $lockVersion = null)
 * @method Consents|null findOneBy(array $criteria, array $orderBy = null)
 * @method Consents[]    findAll()
 * @method Consents[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConsentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Consents::class);
    }

//    /**
//     * @return Consents[] Returns an array of Consents objects
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

//    public function findOneBySomeField($value): ?Consents
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
