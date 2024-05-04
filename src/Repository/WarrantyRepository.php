<?php

namespace App\Repository;

use App\Entity\Warranty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Warranty>
 *
 * @method Warranty|null find($id, $lockMode = null, $lockVersion = null)
 * @method Warranty|null findOneBy(array $criteria, array $orderBy = null)
 * @method Warranty[]    findAll()
 * @method Warranty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WarrantyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Warranty::class);
    }

//    /**
//     * @return Warranty[] Returns an array of Warranty objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Warranty
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * Znajduje gwarancje wraz z powiązanymi tagami dla danego użytkownika.
     *
     * @param int $userId
     * @return Warranty[] Zwraca tablicę gwarancji wraz z tagami.
     */
    public function findWarrantiesWithTags(int $userId): array
    {
        return $this->createQueryBuilder('w')
            ->select('w', 'tags') // Wybierz zarówno gwarancję, jak i jej tagi
            ->leftJoin('w.tags', 'tags') // Left join, aby uwzględnić gwarancje bez tagów
            ->where('w.idUser = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
}
