<?php

namespace App\Service;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Historico;
use App\Service\TarefasService;
use App\Service\ProjetosService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class HistoricosService
{
    private ManagerRegistry $doctrine;
    private ProjetosService $projetosService;
    private TarefasService $tarefasService;

    public function __construct(
        ManagerRegistry $doctrine,
        ProjetosService $projetosService,
        TarefasService $tarefasService
    ) {
        $this->doctrine = $doctrine;
        $this->projetosService = $projetosService;
        $this->tarefasService = $tarefasService;
    }

    /**
     * @param User $usuario
     * @param array $filters
     * @param array $orderBy
     * @return array
     */
    public function findAll(User $usuario, array $filters = [], array $orderBy = null): array
    {
        $filters['usuario'] = $usuario;
        return $this->doctrine->getRepository(Historico::class)->findBy($filters, $orderBy);
    }

    /**
     * @param User $usuario
     * @param integer $id
     * @return Historico
     */
    public function findOne(User $usuario, $id): Historico
    {
        $criteria['usuario'] = $usuario;
        $criteria['id'] = $id;
        return $this->doctrine->getRepository(Historico::class)->findOneBy($criteria);
    }
    
    public function create(User $usuario, Historico $historico)
    {
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();
            
            $historico->setCreatedAt(new DateTimeImmutable());
            $historico->setUsuario($usuario);

            $entityManager->persist($historico);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $historico;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }


    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array<Historico>
     */
    public function listaHistoricosUseCase(User $usuario, array $filters = [], array $orderBy = null) : array
    {
        try {
            $historicos = $this->findAll($usuario, $filters, $orderBy);
            return $historicos;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function createProjetoHistoricoUseCase(User $usuario, Historico $historico)
    {
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();
            
            $this->create($usuario, $historico);

            $entityManager->persist($historico);
            $entityManager->flush();

            $entityManager->getConnection()->commit();
            return $historico;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function factoryNovoHistorico(int $moduloId, int $moduloTipo, string $descricao): Historico
    {
        $historico = new Historico();
        $historico->setDescricao($descricao);
        $historico->setModuloId($moduloId);
        $historico->setModuloTipo($moduloTipo);
        return $historico;
    }

    public function buscaEValidaModuloTipoId(User $usuario, int $moduloId, int $moduloTipo)
    {
        $entidadeModulo = null;
        switch ($moduloTipo) {
            case Historico::MODULO_TIPO_PROJETO:
                $entidadeModulo = $this->projetosService->findOne($usuario, $moduloId);
                break;
            case Historico::MODULO_TIPO_PROJETO:
                $entidadeModulo = $this->projetosService->findOne($usuario, $moduloId);
                break;
            default:
                throw new BadRequestException('Modulo não suportado');
                break;
        }
        if($entidadeModulo == null) throw new BadRequestException('Entidade não encontrada');
        return $entidadeModulo;
    }


    // public function update(Historico $historico, User $usuario)
    // {
    //     try {
    //         $entityManager = $this->doctrine->getManager();
    //         $entityManager->getConnection()->beginTransaction();
            
    //         $historico->setUpdatedAt(new DateTimeImmutable());

    //         $entityManager->persist($historico);
    //         $entityManager->flush();

    //         $entityManager->getConnection()->commit();

    //         return $historico;

    //     } catch (\Throwable $th) {
    //         $entityManager->getConnection()->rollback();
    //         throw $th;
    //     }
    // }

    // public function delete(Historico $historico, User $usuario)
    // {
    //     try {
    //         $entityManager = $this->doctrine->getManager();
    //         $entityManager->getConnection()->beginTransaction();

    //         $entityManager->remove($historico);
    //         $entityManager->flush();

    //         $entityManager->getConnection()->commit();

    //         return $historico;

    //     } catch (\Throwable $th) {
    //         $entityManager->getConnection()->rollback();
    //         throw $th;
    //     }
    // }

}