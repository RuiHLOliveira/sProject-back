<?php

namespace App\Repository;

use App\Entity\HabitoRealizado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HabitoRealizado>
 *
 * @method HabitoRealizado|null find($id, $lockMode = null, $lockVersion = null)
 * @method HabitoRealizado|null findOneBy(array $criteria, array $orderBy = null)
 * @method HabitoRealizado[]    findAll()
 * @method HabitoRealizado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HabitoRealizadoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HabitoRealizado::class);
    }

    public function add(HabitoRealizado $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(HabitoRealizado $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return HabitoRealizado[] Returns an array of HabitoRealizado objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HabitoRealizado
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
