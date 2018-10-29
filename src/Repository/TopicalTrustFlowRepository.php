<?php

namespace App\Repository;

use App\Entity\TopicalTrustFlow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TopicalTrustFlow|null find($id, $lockMode = null, $lockVersion = null)
 * @method TopicalTrustFlow|null findOneBy(array $criteria, array $orderBy = null)
 * @method TopicalTrustFlow[]    findAll()
 * @method TopicalTrustFlow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicalTrustFlowRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TopicalTrustFlow::class);
    }

//    /**
//     * @return TopicalTrustFlow[] Returns an array of TopicalTrustFlow objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TopicalTrustFlow
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
