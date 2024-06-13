<?php

namespace App\Service;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Habito;
use App\Entity\HabitoRealizado;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HabitosService
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
        try {
            $entityManager = $this->doctrine->getManager();
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
    public function factoryHabito($descricao, $hora, $usuario) {

        $habito = new Habito();
        $habito->setUsuario($usuario);
        $habito->setDescricao($descricao);
        $habito->setSituacao(0);
        if($hora != ''){
            $habito->setHora(new DateTimeImmutable($hora));
        }
        
        
        return $habito;
    }

    public function createNewHabito(Habito $habito)
    {
        try {
            $entityManager = $this->doctrine->getManager();
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

    
    public function editarUseCase(Habito $habito, User $usuario){
        
        try {
            $entityManager = $this->doctrine->getManager();
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

    public function concluir(Habito $habito, User $usuario){
        
        try {
            /**
             * Concluir é criar um novo registro de que o hábito foi feito neste dia
             */
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();

            $habitoRealizado = new HabitoRealizado();
            $habitoRealizado->setRealizadoEm(new DateTimeImmutable());
            $habitoRealizado->setCreatedAt(new DateTimeImmutable());
            $habitoRealizado->setUsuario($usuario);
            $habitoRealizado->setHabito($habito);

            $entityManager->persist($habitoRealizado);
            $entityManager->flush();

            $entityManager->getConnection()->commit();

            return $habito;

        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }
    

    // public function falhar(Habito $habito, User $usuario){
        
    //     try {
    //         $entityManager = $this->doctrine->getManager();
    //         $entityManager->getConnection()->beginTransaction();

    //         $habito->falhar();
    //         $habito->setUpdatedAt(new DateTimeImmutable());
    //         $entityManager->persist($habito);
    //         $entityManager->flush();

    //         $entityManager->getConnection()->commit();


    //         return $habito;

    //     } catch (\Throwable $th) {
    //         $entityManager->getConnection()->rollback();
    //         throw $th;
    //     }
    // }

}