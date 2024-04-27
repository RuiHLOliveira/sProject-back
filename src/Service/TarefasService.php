<?php

namespace App\Service;

use App\Entity\Projeto;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Tarefa;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TarefasService
{
    
    private $doctrine;
    private $encoder;

    public function __construct(ManagerRegistry $doctrine,  UserPasswordEncoderInterface $encoder)
    {
        $this->doctrine = $doctrine;
        $this->encoder = $encoder;
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array
     */
    public function findAll(User $usuario, array $filters = [], array $orderBy = null): array
    {
        $filters['usuario'] = $usuario;
        return $this->doctrine->getRepository(Tarefa::class)->findBy($filters, $orderBy);
    }

    /**
     * @param string $idTarefa
     * @param User $usuario
     */
    public function find(string $idTarefa, User $usuario): Tarefa
    {
        return $this->doctrine->getRepository(Tarefa::class)->findOneBy([
            'id' => $idTarefa,
            'usuario' => $usuario
        ]);
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array<Tarefa>
     */
    public function listaTarefasUseCase(User $usuario, array $filters = [], array $orderBy = null): array
    {
        try {
            $tarefas = $this->findAll($usuario, $filters, $orderBy);
            return $tarefas;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * @param Tarefa $tarefa
     * @return Tarefa
     */
    public function atualizaTarefasUseCase(Tarefa $tarefa): Tarefa
    {
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();

            $tarefa->setUpdatedAt(new DateTimeImmutable());

            $entityManager->persist($tarefa);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $tarefa;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    /**
     * @param string $descricao
     * @param int $projetoId
     * @param string $hora
     * @param User $usuario
     * @return Tarefa
     */
    public function factoryTarefa($descricao, $projetoId, $hora, $usuario) {

        $projeto = $this->doctrine->getRepository(Projeto::class)->findOneBy([
            'id' => $projetoId,
            'usuario' => $usuario
        ]);
        if($projeto == null) {
            throw new NotFoundHttpException('Projeto não encontrado.');
        }

        $tarefa = new Tarefa();
        $tarefa->setUsuario($usuario);
        $tarefa->setDescricao($descricao);
        $tarefa->setSituacao(0);
        $tarefa->setProjeto($projeto);
        if($hora != ''){
            $tarefa->setHora(new DateTimeImmutable($hora));
        }
        
        
        return $tarefa;
    }

    public function createNewTarefa(Tarefa $tarefa)
    {
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();

            $tarefa->setCreatedAt(new DateTimeImmutable());

            $entityManager->persist($tarefa);
            $entityManager->flush();

            $entityManager->getConnection()->commit();

            return $tarefa;

        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    
    public function editarUseCase(Tarefa $tarefa, User $usuario){
        
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();

            $tarefa->concluir();
            $tarefa->setUpdatedAt(new DateTimeImmutable());
            $entityManager->persist($tarefa);
            $entityManager->flush();

            $entityManager->getConnection()->commit();


            return $tarefa;

        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function concluir(Tarefa $tarefa, User $usuario){
        
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();

            $tarefa->concluir();
            $tarefa->setUpdatedAt(new DateTimeImmutable());
            $entityManager->persist($tarefa);
            $entityManager->flush();

            $entityManager->getConnection()->commit();


            return $tarefa;

        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }
    

    public function falhar(Tarefa $tarefa, User $usuario){
        
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();

            $tarefa->falhar();
            $tarefa->setUpdatedAt(new DateTimeImmutable());
            $entityManager->persist($tarefa);
            $entityManager->flush();

            $entityManager->getConnection()->commit();


            return $tarefa;

        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

}