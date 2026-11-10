<?php

namespace App\Service;

use App\Entity\Area;
use App\Entity\Historico;
use App\Entity\Objetivo;
use App\Entity\Projeto;
use App\Entity\User;
use App\Service\HistoricosService;
use App\Service\RecompensasService;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;

class ObjetivosService
{
    private ManagerRegistry $doctrine;
    private HistoricosService $historicosService;
    private RecompensasService $recompensasService;

    public function __construct(
        ManagerRegistry $doctrine,
        HistoricosService $historicosService,
        RecompensasService $recompensasService
    ) {
        $this->doctrine = $doctrine;
        $this->historicosService = $historicosService;
        $this->recompensasService = $recompensasService;
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array
     */
    public function findAll(User $usuario, array $filter = [], array $orderBy = []): array
    {
        $filter['usuario'] = $usuario;
        return $this->doctrine->getRepository(Objetivo::class)->findBy($filter, $orderBy);
    }

    /**
     * @param User $usuario
     * @param integer $id
     * @return Objetivo
     */
    public function findOne(User $usuario, $id): Objetivo
    {
        $criteria['usuario'] = $usuario;
        $criteria['id'] = $id;
        return $this->doctrine->getRepository(Objetivo::class)->findOneBy($criteria);
    }

    /**
     * @param User $usuario
     * @param array $filters
     * @param array $orderBy
     * @return array<Objetivo>
     */
    public function listarUseCase(User $usuario, array $filters = [], array $orderBy = []) : array
    {
        try {
            $objetivos = $this->findAll($usuario, $filters, $orderBy);
            return $objetivos;
        } catch (\Exception $e) {
            throw $e;
        }
    }


    // REVISAR
    public function factoryObjetivo($nome, $anotacoes, $respostas, $horizonte, $fotos, Area $area)
    {
        $objetivo = new Objetivo();
        $objetivo->setNome($nome);
        $objetivo->setAnotacoes($anotacoes);
        $objetivo->setSituacao(Objetivo::SITUACAO_PENDENTE);
        $objetivo->setRespostas($respostas);
        $objetivo->setHorizonte($horizonte);
        $objetivo->setFotos($fotos);
        $objetivo->setArea($area);
        return $objetivo;
    }

    public function criarUseCase(Objetivo $objetivo, User $usuario)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            
            $this->basePersist($objetivo, $usuario);
            
            $entityManager->getConnection()->commit();
            return $objetivo;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function basePersist(Objetivo $objetivo, User $usuario)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            
            $objetivo->setUpdatedAt(new DateTimeImmutable());
            if($objetivo->getCreatedAt() == null) {
                $objetivo->setCreatedAt(new DateTimeImmutable());
            }
            if($objetivo->getUsuario() == null) {
                $objetivo->setUsuario($usuario);
            }

            $entityManager->persist($objetivo);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $objetivo;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function atualizarUseCase(Objetivo $objetivo, User $usuario)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            
            $this->basePersist($objetivo, $usuario);
            
            $entityManager->getConnection()->commit();
            return compact('objetivo');
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function deletarUseCase(Objetivo $objetivo, User $usuario)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $entityManager->remove($objetivo);
            $entityManager->flush();

            $entityManager->getConnection()->commit();
            return $objetivo;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

}