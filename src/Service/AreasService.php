<?php

namespace App\Service;

use App\Entity\Area;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Projeto;
use App\Entity\Historico;
use App\Service\HistoricosService;
use App\Service\RecompensasService;
use Doctrine\Persistence\ManagerRegistry;

class AreasService
{
    private ManagerRegistry $doctrine;

    public function __construct(
        ManagerRegistry $doctrine
    ) {
        $this->doctrine = $doctrine;
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array
     */
    public function findAll(User $usuario, array $filter = [], array $orderBy = []): array
    {
        $filter['usuario'] = $usuario;
        return $this->doctrine->getRepository(Area::class)->findBy($filter, $orderBy);
    }

    /**
     * @param User $usuario
     * @param integer $id
     * @return Area
     */
    public function findOne(User $usuario, $id): Area
    {
        $criteria['usuario'] = $usuario;
        $criteria['id'] = $id;
        return $this->doctrine->getRepository(Area::class)->findOneBy($criteria);
    }

    /**
     * @param User $usuario
     * @param array $filters
     * @param array $orderBy
     * @return array<Area>
     */
    public function listarUseCase(User $usuario, array $filters = [], array $orderBy = []) : array
    {
        try {
            $areas = $this->findAll($usuario, $filters, $orderBy);
            return $areas;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function factoryArea(string $nome)
    {
        $area = new Area();
        $area->setNome($nome);
        return $area;
    }

    public function criarUseCase(Area $area, User $usuario)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            
            $this->basePersist($area, $usuario);
            
            $entityManager->getConnection()->commit();
            return $area;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function basePersist(Area $area, User $usuario)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            
            $area->setUpdatedAt(new DateTimeImmutable());
            if($area->getCreatedAt() == null) {
                $area->setCreatedAt(new DateTimeImmutable());
            }
            if($area->getUsuario() == null) {
                $area->setUsuario($usuario);
            }

            $entityManager->persist($area);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $area;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function atualizarUseCase(Area $area, User $usuario)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            
            $this->basePersist($area, $usuario);
            
            $entityManager->getConnection()->commit();
            return compact('area');
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function deletarUseCase(Area $area, User $usuario)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $entityManager->remove($area);
            $entityManager->flush();

            $entityManager->getConnection()->commit();
            return $area;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

}