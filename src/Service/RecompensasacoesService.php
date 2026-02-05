<?php

namespace App\Service;

use DateTime;
use DateInterval;
use LogicException;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Projeto;
use App\Entity\Recompensa;
use App\Entity\Recompensaacao;
use App\Enums\EntidadeEnum;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RecompensasacoesService
{
    
    private $doctrine;
    private $encoder;
    private RecompensasService $recompensasService;

    public function __construct(ManagerRegistry $doctrine,  UserPasswordEncoderInterface $encoder, RecompensasService $recompensasService)
    {
        $this->doctrine = $doctrine;
        $this->encoder = $encoder;
        $this->recompensasService = $recompensasService;
    }

    private function getRepository() : ObjectRepository {
        return $this->doctrine->getRepository(Recompensaacao::class);
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
     * @param User $usuario
     * @param array $orderBy
     * @return array<Recompensaacao>
     */
    public function listUseCase(array $filters = [], array $orderBy = null): array
    {
        try {
            $recompensasacoes = $this->findAll($filters, $orderBy);
            return $recompensasacoes;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param string $descricao
     * @param string $motivo
     * @param int $projetoId
     * @param string $datahora
     * @param User $usuario
     * @return Recompensaacao
     */
    public function factory($quantidade, $tipoatividade, $idRecompensa)
    {
        if(!in_array($tipoatividade, EntidadeEnum::LISTA_ENTIDADES)){
            throw new NotFoundHttpException('Entidade não encontrada.');
        }
        $recompensa = $this->recompensasService->find($idRecompensa);
        if($recompensa == null) {
            throw new NotFoundHttpException('Recompensa não encontrada.');
        }
        $recompensaacao = new Recompensaacao();
        $recompensaacao->setQuantidade($quantidade);
        $recompensaacao->setTipoatividade($tipoatividade);
        $recompensaacao->setRecompensa($recompensa);
        return $recompensaacao;
    }

    public function createUseCase(Recompensaacao $recompensaacao)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $recompensaacao->setCreatedAt(new DateTimeImmutable());
            $entityManager->persist($recompensaacao);
            $entityManager->flush();

            $entityManager->getConnection()->commit();
            return $recompensaacao;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function editUseCase(Recompensaacao $recompensaacao)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $recompensaacao->setUpdatedat(new DateTimeImmutable());
            $entityManager->persist($recompensaacao);
            $entityManager->flush();

            $entityManager->getConnection()->commit();
            return $recompensaacao;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }


}