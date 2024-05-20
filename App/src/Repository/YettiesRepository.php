<?php

namespace App\Repository;

use App\Entity\Yetties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Yetties>
 */
class YettiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Yetties::class);
    }

    public function findTopTenYetis(): array
    {
        $yetis = $this->findAll();
        usort($yetis, function (Yetties $a, Yetties $b) {
            return $b->getTotalRating() <=> $a->getTotalRating();
        });

        return array_slice($yetis, 0, 10);
    }

    public function findRelevant(User $user): ?Yetties
    {
        $yetis = $this->findAll();

        $filteredYetis = array_filter($yetis, function (Yetties $yeti) use ($user) {
            $userRating = $yeti->getRating()[$user->getUsername()] ?? null;
            return $userRating === null;
        });

        if (empty($filteredYetis)) {
            return null;
        }

        $randomKey = array_rand($filteredYetis);
        return $filteredYetis[$randomKey];
    }


//    /**
//     * @return Yetties[] Returns an array of Yetties objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('y')
//            ->andWhere('y.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('y.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Yetties
//    {
//        return $this->createQueryBuilder('y')
//            ->andWhere('y.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
