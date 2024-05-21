<?php

namespace App\Repository;

use App\Entity\RatingHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RatingHistory>
 */
class RatingHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RatingHistory::class);
    }

    public function findOneByDate(): ?RatingHistory
    {
       $allRecords = $this->findAll();

        foreach ($allRecords as $result) {
            if (isset($result->getRating()[date('Y-m-d')])) {
                return $result;
            }
        }

        return null;
    }

    public function findAllToArray(): ?array
    {
        $allRecords = $this->findAll();
        $array = [];
        foreach ($allRecords as $result) {
            array_push($array, $result->getRating());
        }
        if ($array) {
            return $array;;
        } else {
            return null;
        }
    }

//    /**
//     * @return RatingHistory[] Returns an array of RatingHistory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RatingHistory
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
