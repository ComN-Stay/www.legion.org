<?php

namespace App\Repository;

use App\Entity\Articles;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Articles>
 *
 * @method Articles|null find($id, $lockMode = null, $lockVersion = null)
 * @method Articles|null findOneBy(array $criteria, array $orderBy = null)
 * @method Articles[]    findAll()
 * @method Articles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticlesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articles::class);
    }

    public function createOrderedByDateQueryBuilder($status, $tag, $order_by, $order_way): QueryBuilder
    {
        $queryBuilder = $this->addOrderByDateQueryBuilder();
        if ($status) {
            $queryBuilder->andWhere('a.fk_status = :fk_status')
                ->setParameter('fk_status', $status);
        }
        if ($tag) {
            $queryBuilder->andWhere(':fk_tag MEMBER OF a.tags')
                ->setParameter('fk_tag', $tag);
        }

        $queryBuilder->orderBy('a.' . $order_by, $order_way)->getQuery()->getResult();
        
        return $queryBuilder;
    }

    private function addOrderByDateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        $queryBuilder = $queryBuilder ?? $this->createQueryBuilder('a');
        $queryBuilder
            ->leftJoin('a.fk_user', 'u')
            ->leftJoin('a.fk_team', 't');
        return $queryBuilder;
    }
}
