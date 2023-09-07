<?php

namespace App\Repository;

use App\Entity\TransactionalVars;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<TransactionalVars>
 *
 * @method TransactionalVars|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransactionalVars|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransactionalVars[]    findAll()
 * @method TransactionalVars[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionalVarsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransactionalVars::class);
    }

    public function deleteAll()
    {
        $query = $this->createQueryBuilder('t')
                 ->delete()
                 ->getQuery()
                 ->execute();
        return $query;
    }

//    /**
//     * @return TransactionalVars[] Returns an array of TransactionalVars objects
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

//    public function findOneBySomeField($value): ?TransactionalVars
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
