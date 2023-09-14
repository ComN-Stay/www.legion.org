<?php

namespace App\Repository;

use App\Entity\Adverts;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Adverts>
 *
 * @method Adverts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Adverts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Adverts[]    findAll()
 * @method Adverts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Adverts::class);
    }

    public function createOrderedByDateQueryBuilder($status, $localization, $types, $min_price, $max_price, $order_by, $order_way): QueryBuilder
    {
        $queryBuilder = $this->addOrderByDateQueryBuilder();
        if ($status) {
            $queryBuilder->andWhere('a.fk_status = :fk_status')
                ->setParameter('fk_status', $status);
        }
        if ($types) {
            $queryBuilder->andWhere('a.fk_pets_type in (:fk_pets_type)')
                ->setParameter('fk_pets_type', $types);
        }
        if ($min_price && $max_price === null) {
            $queryBuilder->andWhere('a.price > :price')
                ->setParameter('price', $min_price);
        } elseif($min_price && $max_price) {
            $queryBuilder->andWhere('a.price between :min_price and :max_price')
                ->setParameter('min_price', $min_price)
                ->setParameter('max_price', $max_price);
        }

        if($localization && intval($localization) != 0 && strlen($localization) == 2) {
            $queryBuilder->andWhere('c.zip_code like :localization')
                ->setParameter('localization', intval($localization).'%');
        } elseif($localization && intval($localization) != 0 && strlen($localization) > 2) {
            $queryBuilder->andWhere('c.zip_code = :localization')
                ->setParameter('localization', intval($localization));
        } elseif($localization && intval($localization) == 0) {
            $queryBuilder->andWhere('c.town = :localization')
                ->setParameter('localization', $localization);
        }

        $queryBuilder->orderBy('a.' . $order_by, $order_way);
        
        return $queryBuilder;
    }

    private function addOrderByDateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        $queryBuilder = $queryBuilder ?? $this->createQueryBuilder('a');
        $queryBuilder
            ->join('a.fk_company', 'c');
        return $queryBuilder;
    }
}
