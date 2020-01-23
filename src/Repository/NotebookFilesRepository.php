<?php

namespace App\Repository;

use App\Entity\NotebookFiles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method NotebookFiles|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotebookFiles|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotebookFiles[]    findAll()
 * @method NotebookFiles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotebookFilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotebookFiles::class);
    }

    // /**
    //  * @return NotebookFiles[] Returns an array of NotebookFiles objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NotebookFiles
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
