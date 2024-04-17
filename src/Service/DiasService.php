<?php

namespace App\Service;

use App\Entity\Dia;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;

class DiasService
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array
     */
    public function findAll(User $usuario, array $orderBy = null): array
    {
        $filter = [];
        $filter['usuario'] = $usuario;
        return $this->doctrine->getRepository(Dia::class)->findBy($filter, $orderBy);
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array<Dia>
     */
    public function listaDiasUseCase(User $usuario, array $orderBy = null) : array
    {
        try {
            $dias = $this->findAll($usuario, $orderBy);
            return $dias;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function createNewDiaFromDataCompleta(DateTimeImmutable $dataCompleta, User $usuario) {
        
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();

            $dia = new Dia();
            $dia->setDataCompleta($dataCompleta);
            $dia->setCreatedAt(new DateTimeImmutable());
            $dia->setUsuario($usuario);
            
            $entityManager->persist($dia);
            $entityManager->flush();

            $entityManager->getConnection()->commit();

            return $dia;

        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

}