<?php

namespace App\Repository;

use App\Entity\PersonagemHistorico;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PersonagemHistorico>
 *
 * @method PersonagemHistorico|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonagemHistorico|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonagemHistorico[]    findAll()
 * @method PersonagemHistorico[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonagemHistoricoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonagemHistorico::class);
    }

    public function add(PersonagemHistorico $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PersonagemHistorico $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PersonagemHistorico[] Returns an array of PersonagemHistorico objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PersonagemHistorico
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
