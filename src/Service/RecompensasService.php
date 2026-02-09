<?php

namespace App\Service;

use DateTime;
use DateInterval;
use LogicException;
use App\Entity\User;
use App\Entity\Habito;
use App\Entity\Tarefa;
use DateTimeImmutable;
use App\Entity\Projeto;
use App\Entity\InboxItem;
use App\Entity\Personagem;
use App\Entity\Recompensa;
use App\Enums\EntidadeEnum;
use App\Entity\PersonagemHistorico;
use App\Service\RecompensasacoesService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use App\Service\PersonagensHistoricosService;
use Exception;
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
        $constRecompensa = Recompensa::ACAO_TAREFA;
        $constTipoHistorico = PersonagemHistorico::TIPOHISTORICO_TAREFA;
        $descricaoAtividade = $tarefa->getDescricao();
        $texto = 'Concluiu tarefa ['.$descricaoAtividade.']';
        return $this->processarRecompensaGeral($usuario, $constRecompensa, $constTipoHistorico, $descricaoAtividade, $texto);
    }

    public function processarRecompensaHabito (Habito $habito, User $usuario)
    {
        $constRecompensa = Recompensa::ACAO_HABITO;
        $constTipoHistorico = PersonagemHistorico::TIPOHISTORICO_HABITO;
        $descricaoAtividade = $habito->getDescricao();
        $texto = 'Realizou hábito ['.$descricaoAtividade.']';
        return $this->processarRecompensaGeral($usuario, $constRecompensa, $constTipoHistorico, $descricaoAtividade, $texto);
    }
    
    public function processarRecompensaProjeto (Projeto $projeto, User $usuario)
    {
        $constRecompensa = Recompensa::ACAO_PROJETO;
        $constTipoHistorico = PersonagemHistorico::TIPOHISTORICO_PROJETO;
        $descricaoAtividade = $projeto->getNome();
        $texto = 'Completou projeto ['.$descricaoAtividade.']';
        return $this->processarRecompensaGeral($usuario, $constRecompensa, $constTipoHistorico, $descricaoAtividade, $texto);
    }

    public function processarRecompensaInboxItem (InboxItem $inboxItem, User $usuario)
    {
        $constRecompensa = Recompensa::ACAO_INBOXITEM;
        $constTipoHistorico = PersonagemHistorico::TIPOHISTORICO_INBOXITEM;
        $descricaoAtividade = $inboxItem->getNome();
        $texto = 'Revisou Inbox Item ['.$descricaoAtividade.']';
        return $this->processarRecompensaGeral($usuario, $constRecompensa, $constTipoHistorico, $descricaoAtividade, $texto);
    }

    private function processarRecompensaGeral(User $usuario, $constRecompensa, $constTipoHistorico, $descricaoAtividade, $texto)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            $recompensa = Recompensa::ACOESRECOMPENSAS[$constRecompensa];
            $personagem = $this->processaRecompensaPersonagem($usuario, $recompensa);

            $historico = $this->processaRecompensaHistoricoPersonagem(
                $usuario, $personagem, $constTipoHistorico,
                $descricaoAtividade, $texto, $recompensa
            );

            $historicoSubiuNivel = $this->processaLevelUpPersonagem($usuario, $personagem);

            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return compact('historico','historicoSubiuNivel');
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    private function processaLevelUpPersonagem(User $usuario, Personagem $personagem)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $qtdExpTotal = $personagem->getExperiencia();
            $nivelAtual = $personagem->getNivel();

            $historico = null;
            $tabelaNiveis = Recompensa::getTabelaNiveis();
            $nivelAtual = $tabelaNiveis[$nivelAtual];
            $expProxNivel = $nivelAtual['expProxNivel'];
            if($expProxNivel == null){
                $entityManager->flush();
                $entityManager->getConnection()->commit();
                return $historico;
            }

            if($qtdExpTotal >=  $expProxNivel){
                $personagem->setNivel($personagem->getNivel() + 1);
                $nivelAtual = $tabelaNiveis[$personagem->getNivel()];

                $personagemAtribudos = json_decode($personagem->getAtributosjson());
                $personagemAtribudos->vidaMaxima = $nivelAtual['vidaMaxima'];
                $personagemAtribudos->vidaAtual = $personagemAtribudos->vidaMaxima;
                $personagem->setAtributosjson(json_encode($personagemAtribudos));

                $personagemAtualizado = $this->personagensService->updatePersonagem($personagem, $usuario);
                $texto = sprintf('Subiu de Nível! %s => %s', $personagem->getNivel()-1, $personagem->getNivel());
                $historico = $this->personagensHistoricosService->factory($usuario, json_encode([]), PersonagemHistorico::TIPOHISTORICO_SUBIUNIVEL, $texto, $personagem->getId());
                $historico = $this->personagensHistoricosService->createUseCase($historico);
            }

            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $historico;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    private function processaRecompensaPersonagem(User $usuario, $recompensa)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            /**
             * @var Personagem $personagem
             */
            $personagem = $this->personagensService->findAll($usuario)[0];
            // processa no personagem
            foreach ($recompensa['moedas'] as $key => $moedaRecompensa) {
                if($moedaRecompensa['moeda'] == Recompensa::MOEDA_OURO) {
                    $personagem->setOuro($personagem->getOuro() + $moedaRecompensa['quantidade']);
                }
                if($moedaRecompensa['moeda'] == Recompensa::MOEDA_EXPERIENCIA) {
                    $personagem->setExperiencia($personagem->getExperiencia() + $moedaRecompensa['quantidade']);
                }
            }
            $personagemAtualizado = $this->personagensService->updatePersonagem($personagem, $usuario);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $personagemAtualizado;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    private function processaRecompensaHistoricoPersonagem(User $usuario, Personagem $personagem, $constTipoHistorico, $descricaoAtividade, $texto, $recompensa)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            $idPersonagem = $personagem->getId();
            $dadosjson = [
                'descricaoatividade' => $descricaoAtividade,
                'recompensas' => []
            ];
            foreach ($recompensa['moedas'] as $key => $moedaRecompensa) {
                $dadosjson['recompensas'][] = [
                    'moeda' => $moedaRecompensa['moeda'],
                    'quantidade' => $moedaRecompensa['quantidade'],
                ];
            }
            $historico = $this->personagensHistoricosService->factory($usuario, json_encode($dadosjson), $constTipoHistorico, $texto, $idPersonagem);
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