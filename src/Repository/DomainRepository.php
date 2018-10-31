<?php

namespace App\Repository;

use App\Entity\Domain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @method Domain|null find($id, $lockMode = null, $lockVersion = null)
 * @method Domain|null findOneBy(array $criteria, array $orderBy = null)
 * @method Domain[]    findAll()
 * @method Domain[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomainRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Domain::class);
    }


    public function add(Domain $domain)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($domain);

        $entityManager->flush();
        return new Response('Saved new domain with id '.$domain->getId());
    }

    /**********************/
    // Recuperation des domaines disponibles en fonction des criteres de la recherche
    /**********************/
    public function findDispoByCritere ($refIp = 0,$tf = 0,$tm = 0): array
    {
        $qb = $this->createQueryBuilder('d')
            ->andWhere('d.RefIP >= :refIp')
            ->andWhere('d.trustMetrics >= :tm')
            ->andWhere('d.trustFlow >= :tf')
            ->andWhere('d.dispo = :dispo')
            ->setParameter('refIp', $refIp)
            ->setParameter('tf', $tf)
            ->setParameter('tm', $tm)
            ->setParameter('dispo', 'Disponible')
            ->orderBy('d.lastCrawledDate', 'DESC')
            ->getQuery();

        return $qb->execute();
    }

//    /**
//     * @return Domain[] Returns an array of Domain objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Domain
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
