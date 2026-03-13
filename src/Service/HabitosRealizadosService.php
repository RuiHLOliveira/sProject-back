<?php

namespace App\Service;

use App\Entity\HabitoRealizado;
use DateTime;
use DateInterval;
use LogicException;
use App\Entity\User;
use App\Entity\Tarefa;
use DateTimeImmutable;
use App\Entity\Projeto;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HabitosRealizadosService
{
    
    private $doctrine;
    private $encoder;

    public function __construct(
        ManagerRegistry $doctrine,
        UserPasswordEncoderInterface $encoder
    )
    {
        $this->doctrine = $doctrine;
        $this->encoder = $encoder;
    }

    private function getRepository() : ObjectRepository {
        return $this->doctrine->getRepository(HabitoRealizado::class);
    }


    public function findAll(User $usuario, array $filters = [], array $orderBy = null): array
    {
        $filters['usuario'] = $usuario;
        return $this->getRepository()->findBy($filters, $orderBy);
    }

    public function find(string $id, User $usuario): HabitoRealizado
    {
        $habitoRealizado = $this->getRepository()->findOneBy([
            'id' => $id,
            'usuario' => $usuario
        ]);
        return $habitoRealizado;
    }

    public function listarUseCase(User $usuario, array $filters = [], array $orderBy = null): array
    {
        try {
            $habitosRealizados = $this->findAll($usuario, $filters, $orderBy);
            return $habitosRealizados;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function registraAvaliacaoUseCase(HabitoRealizado $habitoRealizado): HabitoRealizado
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            $entityManager->persist($habitoRealizado);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $habitoRealizado;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

}