<?php

namespace App\Service;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Projeto;
use App\Entity\Historico;
use App\Service\HistoricosService;
use App\Service\RecompensasService;
use Doctrine\Persistence\ManagerRegistry;

class ProjetosService
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
        return $this->doctrine->getRepository(Projeto::class)->findBy($filter, $orderBy);
    }

    /**
     * @param User $usuario
     * @param integer $id
     * @return Projeto
     */
    public function findOne(User $usuario, $id): Projeto
    {
        $criteria['usuario'] = $usuario;
        $criteria['id'] = $id;
        return $this->doctrine->getRepository(Projeto::class)->findOneBy($criteria);
    }

    /**
     * @param User $usuario
     * @param array $filters
     * @param array $orderBy
     * @return array<Projeto>
     */
    public function listaProjetosUseCase(User $usuario, array $filters = [], array $orderBy = []) : array
    {
        try {
            $projetos = $this->findAll($usuario, $filters, $orderBy);
            return $projetos;
        } catch (\Exception $e) {
            throw $e;
        }
    }


    public function factoryCreateProjetoUsecase($nome, $anotacoes, $dataPrazo, $prioridade = Projeto::PRIORIDADE_BAIXISSIMA)
    {
        $projeto = new Projeto();
        if($dataPrazo != null && $dataPrazo != '') {
            $dataPrazo = new DateTimeImmutable($dataPrazo);
            $projeto->setDataPrazo($dataPrazo);
        }
        $projeto->setNome($nome);
        $projeto->setAnotacoes($anotacoes);
        $projeto->setSituacao(Projeto::SITUACAO_PENDENTE);
        $projeto->setPrioridade($prioridade);
        $projeto->setFixado(false);
        return $projeto;
    }

    public function createProjetoUseCase(Projeto $projeto, User $usuario)
    {
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();
            
            $this->baseCreate($projeto, $usuario);
            
            $descricaoHistorico = 'Criado novo projeto';
            $historicoCriado = $this->criaHistorico($usuario, $projeto, $descricaoHistorico);

            $entityManager->getConnection()->commit();
            return $projeto;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    private function criaHistorico(User $usuario, Projeto $projeto, string $descricaoHistorico)
    {
        $entidadeModulo = $this->historicosService->buscaEValidaModuloTipoId($usuario, $projeto->getId(), Historico::MODULO_TIPO_PROJETO);
        $historicoProjeto = $this->historicosService->factoryNovoHistorico($projeto->getId(), Historico::MODULO_TIPO_PROJETO, $descricaoHistorico);
        $historicoCriado = $this->historicosService->create($usuario, $historicoProjeto);
        return $historicoCriado;
    }

    public function baseCreate(Projeto $projeto, User $usuario)
    {
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();
            
            $projeto->setCreatedAt(new DateTimeImmutable());
            $projeto->setUsuario($usuario);

            $entityManager->persist($projeto);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $projeto;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function updateProjeto(Projeto $projeto, User $usuario)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            
            $projeto->setUpdatedAt(new DateTimeImmutable());

            $entityManager->persist($projeto);
            
            $descricaoHistorico = 'Editado projeto';
            $historicoCriado = $this->criaHistorico($usuario, $projeto, $descricaoHistorico);

            $historico = [];
            $historicoSubiuNivel = [];
            if($projeto->getSituacao() == Projeto::SITUACAO_CONCLUIDO) {
                $dados = $this->recompensasService->processarRecompensaProjeto($projeto, $usuario);
                $historico = $dados['historico'];
                $historicoSubiuNivel = $dados['historicoSubiuNivel'];
            }

            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return compact('projeto','historico', 'historicoSubiuNivel');
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function deleteProjeto(Projeto $projeto, User $usuario)
    {
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();

            $entityManager->remove($projeto);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $projeto;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function fixarDesafixar(Projeto $projeto, User $usuario)
    {
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();
            
            $projeto->setFixado(!$projeto->getFixado());
            $projeto->setUpdatedAt(new DateTimeImmutable());

            $entityManager->persist($projeto);
            
            $descricaoHistorico = $projeto->getFixado() ? 'Projeto fixado' : 'Projeto desafixado';
            $historicoCriado = $this->criaHistorico($usuario, $projeto, $descricaoHistorico);

            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $descricaoHistorico;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

}