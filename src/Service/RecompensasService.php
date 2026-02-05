<?php

namespace App\Service;

use DateTime;
use DateInterval;
use LogicException;
use App\Entity\User;
use App\Entity\Tarefa;
use DateTimeImmutable;
use App\Entity\Projeto;
use App\Entity\Personagem;
use App\Entity\Recompensa;
use App\Enums\EntidadeEnum;
use App\Entity\PersonagemHistorico;
use App\Service\RecompensasacoesService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use App\Service\PersonagensHistoricosService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RecompensasService
{
    
    private $doctrine;
    private $encoder;
    private PersonagensService $personagensService;
    private PersonagensHistoricosService $personagensHistoricosService;

    public function __construct(
        ManagerRegistry $doctrine,
        UserPasswordEncoderInterface $encoder,
        PersonagensService $personagensService,
        PersonagensHistoricosService $personagensHistoricosService
    ) {
        $this->doctrine = $doctrine;
        $this->encoder = $encoder;
        $this->personagensService = $personagensService;
        $this->personagensHistoricosService = $personagensHistoricosService;
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array
     */
    public function findAll(array $filters = [], array $orderBy = null): array
    {
        return Recompensa::ACOESRECOMPENSAS;
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

    public function processarRecompensaTarefa (Tarefa $tarefa, User $usuario)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            /**
             * @var Personagem $personagem
             */
            $personagem = $this->personagensService->findAll($usuario)[0];
            // recompensa
            $acoesRecompensas = Recompensa::ACOESRECOMPENSAS;
            $recompensaTarefa = $acoesRecompensas[Recompensa::ACAO_TAREFA];
            
            // processa no personagem
            foreach ($recompensaTarefa['moedas'] as $key => $moedaRecompensa) {
                if($moedaRecompensa['moeda'] == Recompensa::MOEDA_OURO) {
                    $personagem->setOuro($personagem->getOuro() + $moedaRecompensa['quantidade']);
                }
                if($moedaRecompensa['moeda'] == Recompensa::MOEDA_EXPERIENCIA) {
                    $personagem->setExperiencia($personagem->getExperiencia() + $moedaRecompensa['quantidade']);
                }
            }
            $personagemAtualizado = $this->personagensService->updatePersonagem($personagem, $usuario);

            // historico
            $idPersonagem = $personagem->getId();
            $texto = 'Completou Tarefa ['.$tarefa->getDescricao().']';
            $dadosjson = [
                'tarefa' => $tarefa->getDescricao(),
                'recompensas' => []
            ];

            
            foreach ($recompensaTarefa['moedas'] as $key => $moedaRecompensa) {
                $dadosjson['recompensas'][] = [
                    'moeda' => $moedaRecompensa['moeda'],
                    'quantidade' => $moedaRecompensa['quantidade'],
                ];
            }

            $historico = $this->personagensHistoricosService->factory($usuario, json_encode($dadosjson), PersonagemHistorico::TIPOHISTORICO_TAREFA, $texto, $idPersonagem);
            $historico = $this->personagensHistoricosService->createUseCase($historico);

            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $historico;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }


}