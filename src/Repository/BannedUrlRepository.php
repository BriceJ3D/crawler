<?php

namespace App\Repository;

use App\Entity\BannedUrl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BannedUrl|null find($id, $lockMode = null, $lockVersion = null)
 * @method BannedUrl|null findOneBy(array $criteria, array $orderBy = null)
 * @method BannedUrl[]    findAll()
 * @method BannedUrl[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BannedUrlRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BannedUrl::class);
    }

//    /**
//     * @return BannedUrl[] Returns an array of BannedUrl objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BannedUrl
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
