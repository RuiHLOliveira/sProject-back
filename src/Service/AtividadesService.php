<?php

namespace App\Service;

use App\Entity\Dia;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Atividade;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AtividadesService
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
    public function findAll(User $usuario, array $orderBy = null): array
    {
        $filter = [];
        $filter['usuario'] = $usuario;
        return $this->doctrine->getRepository(Atividade::class)->findBy($filter, $orderBy);
    }

    /**
     * @param string $idAtividade
     * @param User $usuario
     */
    public function find (string $idAtividade, User $usuario): Atividade
    {
        return $this->doctrine->getRepository(Atividade::class)->findOneBy([
            'id' => $idAtividade,
            'usuario' => $usuario
        ]);
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array<Atividade>
     */
    public function listaAtividadesUseCase(User $usuario, array $orderBy = null): array
    {
        try {
            $atividades = $this->findAll($usuario, $orderBy);
            return $atividades;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * @param Atividade $atividade
     * @return Atividade
     */
    public function atualizaAtividadesUseCase(Atividade $atividade): Atividade
    {
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();

            $atividade->setUpdatedAt(new DateTimeImmutable());

            $entityManager->persist($atividade);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $atividade;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    /**
     * @param string $descricao
     * @param int $diaId
     * @param string $hora
     * @param User $usuario
     * @return Atividade
     */
    public function factoryAtividade($descricao, $diaId, $hora, $usuario) {

        $dia = $this->doctrine->getRepository(Dia::class)->findOneBy([
            'id' => $diaId,
            'usuario' => $usuario
        ]);
        if($dia == null) {
            throw new NotFoundHttpException('Dia não encontrado.');
        }

        $atividade = new Atividade();
        $atividade->setUsuario($usuario);
        $atividade->setDescricao($descricao);
        $atividade->setSituacao(0);
        $atividade->setDia($dia);
        $atividade->setHora(new DateTimeImmutable($hora));
        $atividade->setCreatedAt(new DateTimeImmutable());
        
        return $atividade;
    }

    public function createNewAtividade(Atividade $atividade)
    {
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();

            $entityManager->persist($atividade);

            $entityManager->flush();

            $entityManager->getConnection()->commit();

            return $atividade;

        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    
    public function editarUseCase(Atividade $atividade, User $usuario){
        
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();

            $atividade->concluir();
            $atividade->setUpdatedAt(new DateTimeImmutable());
            $entityManager->persist($atividade);
            $entityManager->flush();

            $entityManager->getConnection()->commit();


            return $atividade;

        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function concluir(Atividade $atividade, User $usuario){
        
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();

            $atividade->concluir();
            $atividade->setUpdatedAt(new DateTimeImmutable());
            $entityManager->persist($atividade);
            $entityManager->flush();

            $entityManager->getConnection()->commit();


            return $atividade;

        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }
    

    public function falhar(Atividade $atividade, User $usuario){
        
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();

            $atividade->falhar();
            $atividade->setUpdatedAt(new DateTimeImmutable());
            $entityManager->persist($atividade);
            $entityManager->flush();

            $entityManager->getConnection()->commit();


            return $atividade;

        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

}