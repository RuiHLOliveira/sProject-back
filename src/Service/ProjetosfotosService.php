<?php

namespace App\Service;

use App\Entity\Projeto;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Projetofoto;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProjetosfotosService
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
        return $this->doctrine->getRepository(Projetofoto::class)->findBy($filters, $orderBy);
    }

    /**
     * @param string $idProjetofoto
     * @param User $usuario
     */
    public function find(string $idProjetofoto, User $usuario): Projetofoto
    {
        $projetofoto = $this->doctrine->getRepository(Projetofoto::class)->findOneBy([
            'id' => $idProjetofoto,
            'usuario' => $usuario
        ]);
        return $projetofoto;
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array<Projetofoto>
     */
    public function listaProjetosfotosUseCase(User $usuario, array $filters = [], array $orderBy = null): array
    {
        try {
            $projetofoto = $this->findAll($usuario, $filters, $orderBy);
            return $projetofoto;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * @param Projetofoto $projetofoto
     * @return Projetofoto
     */
    public function atualizaProjetosfotosUseCase(Projetofoto $projetofoto): Projetofoto
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $projetofoto->setUpdatedAt(new DateTimeImmutable());

            $entityManager->persist($projetofoto);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $projetofoto;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    /**
     * @param string $descricao
     * @param string $link
     * @param int $projetoId
     * @param string $hora
     * @param User $usuario
     * @return Projetofoto
     */
    public function factoryProjetofoto($descricao, $link, $projetoId, $usuario) : Projetofoto {

        $projeto = $this->doctrine->getRepository(Projeto::class)->findOneBy([
            'id' => $projetoId,
            'usuario' => $usuario
        ]);
        if($projeto == null) {
            throw new NotFoundHttpException('Projeto não encontrado.');
        }

        $projetofoto = new Projetofoto();
        $projetofoto->setUsuario($usuario);
        $projetofoto->setDescricao($descricao);
        $projetofoto->setLink($link);
        $projetofoto->setProjeto($projeto);
        
        
        return $projetofoto;
    }

    public function createNewProjetofoto(Projetofoto $projetofoto) : Projetofoto
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $projetofoto->setCreatedAt(new DateTimeImmutable());

            $entityManager->persist($projetofoto);
            $entityManager->flush();

            $entityManager->getConnection()->commit();

            return $projetofoto;

        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    
    public function editarUseCase(Projetofoto $projetofoto, User $usuario) : Projetofoto
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $projetofoto->setUpdatedAt(new DateTimeImmutable());
            $entityManager->persist($projetofoto);
            $entityManager->flush();

            $entityManager->getConnection()->commit();

            return $projetofoto;

        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function deleteProjetofoto(Projetofoto $projetofoto, User $usuario)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $entityManager->remove($projetofoto);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $projetofoto;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }
}