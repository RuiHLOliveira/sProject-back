<?php

namespace App\Service;

use DateTime;
use DateInterval;
use LogicException;
use App\Entity\User;
use DateTimeImmutable;
use App\Enums\EntidadeEnum;
use App\Entity\Personagem;
use App\Entity\PersonagemHistorico;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PersonagensHistoricosService
{
    
    private $doctrine;
    private $encoder;
    private PersonagensService $personagensService;

    public function __construct(ManagerRegistry $doctrine,  UserPasswordEncoderInterface $encoder, PersonagensService $personagensService)
    {
        $this->doctrine = $doctrine;
        $this->encoder = $encoder;
        $this->personagensService = $personagensService;
    }

    private function getRepository() : ObjectRepository {
        return $this->doctrine->getRepository(PersonagemHistorico::class);
    }

    public function findAll(User $usuario, Personagem $personagem, array $filters = [], array $orderBy = null): array
    {
        $filters['usuario'] = $usuario;
        $filters['personagem'] = $personagem;
        return $this->getRepository()->findBy($filters, $orderBy);
    }

    public function listUseCase(User $usuario, Personagem $personagem, array $filters = [], array $orderBy = null): array
    {
        try {
            $personagensHistoricos = $this->findAll($usuario, $personagem, $filters, $orderBy);
            return $personagensHistoricos;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function factory(User $usuario, $dadosjson, $tipohistorico, $texto, $idPersonagem)
    {
        if(!isset(PersonagemHistorico::LISTA_TIPOSHISTORICOS[$tipohistorico])){
            throw new NotFoundHttpException('Tipo historico não encontrada.');
        }
        $personagem = $this->personagensService->find($usuario, $idPersonagem);
        if($personagem == null) {
            throw new NotFoundHttpException('Personagem não encontrada.');
        }
        $personagemHistorico = new PersonagemHistorico();
        $personagemHistorico->setDadosjson($dadosjson);
        $personagemHistorico->setTipohistorico($tipohistorico);
        $personagemHistorico->setTexto($texto);
        $personagemHistorico->setPersonagem($personagem);
        return $personagemHistorico;
    }

    public function createUseCase(PersonagemHistorico $personagemHistorico)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $personagemHistorico->setCreatedAt(new DateTimeImmutable());
            $entityManager->persist($personagemHistorico);
            $entityManager->flush();

            $entityManager->getConnection()->commit();
            return $personagemHistorico;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function editUseCase(PersonagemHistorico $personagemHistorico)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $personagemHistorico->setUpdatedat(new DateTimeImmutable());
            $entityManager->persist($personagemHistorico);
            $entityManager->flush();

            $entityManager->getConnection()->commit();
            return $personagemHistorico;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }


}