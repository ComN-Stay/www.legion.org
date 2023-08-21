<?php

namespace App\Repository;

use App\Entity\ArticlesMedias;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ArticlesMedias>
 *
 * @method ArticlesMedias|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticlesMedias|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticlesMedias[]    findAll()
 * @method ArticlesMedias[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticlesMediasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticlesMedias::class);
    }

//    /**
//     * @return ArticlesMedias[] Returns an array of ArticlesMedias objects
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

//    public function findOneBySomeField($value): ?ArticlesMedias
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
