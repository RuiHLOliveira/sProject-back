<?php

namespace App\Service;

use DateTime;
use DateInterval;
use LogicException;
use App\Entity\User;
use App\Entity\Recompensa;
use DateTimeImmutable;
use App\Entity\Projeto;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RecompensasService
{
    
    private $doctrine;
    private $encoder;

    public function __construct(ManagerRegistry $doctrine,  UserPasswordEncoderInterface $encoder)
    {
        $this->doctrine = $doctrine;
        $this->encoder = $encoder;
    }

    private function getRepository() : ObjectRepository {
        return $this->doctrine->getRepository(Recompensa::class);
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array
     */
    public function findAll(array $filters = [], array $orderBy = null): array
    {
        return $this->getRepository()->findBy($filters, $orderBy);
    }

    /**
     * @param integer $id
     * @return Recompensa
     */
    public function find($id): Recompensa
    {
        $criteria['id'] = $id;
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * @param array $orderBy
     * @return array<Recompensa>
     */
    public function listUseCase(array $filters = [], array $orderBy = null): array
    {
        try {
            $recompensas = $this->findAll($filters, $orderBy);
            return $recompensas;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param string $nome
     * @return Recompensa
     */
    public function factory($nome) {

        $recompensa = new Recompensa();
        $recompensa->setNome($nome);
        return $recompensa;
    }

    public function createUseCase(Recompensa $recompensa)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $recompensa->setCreatedAt(new DateTimeImmutable());
            $entityManager->persist($recompensa);
            $entityManager->flush();

            $entityManager->getConnection()->commit();
            return $recompensa;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }


}