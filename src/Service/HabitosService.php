<?php

namespace App\Service;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Habito;
use App\Entity\HabitoRealizado;
use App\Entity\Historico;
use Doctrine\Persistence\ManagerRegistry;

class HabitosService
{
    private ManagerRegistry $doctrine;
    protected HistoricosService $historicosService;

    public function __construct(
        ManagerRegistry $doctrine,
        HistoricosService $historicosService
    ) {
        $this->doctrine = $doctrine;
        $this->historicosService = $historicosService;
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array
     */
    public function findAll(User $usuario, array $filters = [], array $orderBy = null): array
    {
        $filters['usuario'] = $usuario;
        return $this->doctrine->getRepository(Habito::class)->findBy($filters, $orderBy);
    }

    /**
     * @param string $idHabito
     * @param User $usuario
     */
    public function find(string $idHabito, User $usuario): Habito
    {
        return $this->doctrine->getRepository(Habito::class)->findOneBy([
            'id' => $idHabito,
            'usuario' => $usuario
        ]);
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array<Habito>
     */
    public function listaHabitosUseCase(User $usuario, array $filters = [], array $orderBy = null, $relations = []): array
    {
        try {
            $habitos = $this->findAll($usuario, $filters, $orderBy);
            foreach ($relations as $key => $relation) {
                for ($i=0; $i < count($habitos); $i++) {
                    if($relation == 'habitoRealizados'){
                        $habitos[$i]->serializarHabitoRealizados();
                    }
                }
            }
            return $habitos;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * @param Habito $habito
     * @return Habito
     */
    public function atualizaHabitosUseCase(Habito $habito): Habito
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $habito->setUpdatedAt(new DateTimeImmutable());

            $entityManager->persist($habito);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $habito;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    /**
     * @param string $descricao
     * @param string $hora
     * @param User $usuario
     * @return Habito
     */
    public function factoryHabito($descricao, $motivo, $hora, $usuario) {

        $habito = new Habito();
        $habito->setUsuario($usuario);
        $habito->setDescricao($descricao);
        $habito->setMotivo($motivo);
        $habito->setSituacao(0);
        if($hora != ''){
            $habito->setHora(new DateTimeImmutable($hora));
        }
        
        
        return $habito;
    }

    public function createNewHabito(Habito $habito)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $habito->setCreatedAt(new DateTimeImmutable());

            $entityManager->persist($habito);
            $entityManager->flush();

            $entityManager->getConnection()->commit();

            return $habito;

        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    
    public function editarUseCase(Habito $habito, User $usuario)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $habito->concluir();
            $habito->setUpdatedAt(new DateTimeImmutable());
            $entityManager->persist($habito);
            $entityManager->flush();

            $entityManager->getConnection()->commit();


            return $habito;

        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function concluir(string $textoObservacao, Habito $habito, User $usuario)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            /**
             * Concluir é criar um novo registro de que o hábito foi feito neste dia
             */
            $habitoRealizado = new HabitoRealizado();
            $habitoRealizado->setRealizadoEm(new DateTimeImmutable());
            $habitoRealizado->setCreatedAt(new DateTimeImmutable());
            $habitoRealizado->setUsuario($usuario);
            $habitoRealizado->setHabito($habito);

            $entityManager->persist($habitoRealizado);

            $historicoCriado = $this->criaHistoricoHabitoConcluido($textoObservacao, $habito, $usuario);

            $entityManager->flush();

            $entityManager->getConnection()->commit();

            return $habito;

        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    protected function criaHistoricoHabitoConcluido ($textoObservacao, $habito, $usuario) : Historico
    {
        $descricaoHistorico = 'Hábito concluído';
        $this->historicosService->buscaEValidaModuloTipoId($usuario, $habito->getId(), Historico::MODULO_TIPO_HABITO);
        $historico = $this->historicosService->factoryNovoHistorico(
            $habito->getId(),
            Historico::MODULO_TIPO_HABITO,
            $descricaoHistorico
        );
        $historico->setTexto($textoObservacao);
        $historicoCriado = $this->historicosService->create($usuario, $historico);
        return $historicoCriado;
    }
}