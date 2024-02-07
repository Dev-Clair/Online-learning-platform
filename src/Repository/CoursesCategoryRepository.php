<?php

namespace App\Repository;

use App\Entity\CoursesCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CoursesCategory>
 *
 * @method CoursesCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoursesCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoursesCategory[]    findAll()
 * @method CoursesCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoursesCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoursesCategory::class);
    }

//    /**
//     * @return CoursesCategory[] Returns an array of CoursesCategory objects
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

//    public function findOneBySomeField($value): ?CoursesCategory
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
