<?php

namespace App\Repository;

use App\Entity\PetsType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PetsType>
 *
 * @method PetsType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PetsType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PetsType[]    findAll()
 * @method PetsType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PetsTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PetsType::class);
    }

//    /**
//     * @return PetsType[] Returns an array of PetsType objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PetsType
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
