<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

//    /**
//     * @return Category[] Returns an array of Category objects
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

//    public function findOneBySomeField($value): ?Category
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findTopSellingCategories(int $limit): array
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'COUNT(op.id) as total_sales')
            ->leftJoin('App\Entity\Product', 'p', 'WITH', 'p.category = c')
            ->leftJoin('App\Entity\OrderProduct', 'op', 'WITH', 'op.product = p')
            ->groupBy('c.id')
            ->orderBy('total_sales', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
