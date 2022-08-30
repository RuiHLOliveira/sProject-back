<?php

namespace App\Service;

use App\Entity\Dia;
use App\Entity\Dias;
use App\Entity\Hora;
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

    public function index(User $usuario) {

        try {
            $entityList = $this->doctrine->getRepository(Atividade::class)->findBy([
                'usuario' => $usuario
            ]);

            return $entityList;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param string $descricao
     * @param int $hora
     * @return Atividade
     */
    public function factoryAtividade($descricao, $horaId, $usuario) {

        $hora = $this->doctrine->getRepository(Hora::class)->findOneBy([
            'id' => $horaId,
            'usuario' => $usuario
        ]);
        if($hora == null) {
            throw new NotFoundHttpException('Hora não encontrada.');
        }

        $atividade = new Atividade();
        $atividade->setUsuario($usuario);
        $atividade->setDescricao($descricao);
        $atividade->setSituacao(0);
        $atividade->setHora($hora);
        $atividade->setCreatedAt(new DateTimeImmutable());
        
        return $atividade;
    }

    public function createNewAtividade(Atividade $atividade) {
        
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

    public function find ($idAtividade, $usuario) {
        
        $atividade = $this->doctrine->getRepository(Atividade::class)->findOneBy([
            'id' => $idAtividade,
            'usuario' => $usuario
        ]);

        return $atividade;
    }

}