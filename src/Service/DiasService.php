<?php

namespace App\Service;

use App\Entity\Dia;
use App\Entity\Dias;
use App\Entity\Hora;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DiasService
{
    
    private $doctrine;
    private $encoder;

    public function __construct(ManagerRegistry $doctrine,  UserPasswordEncoderInterface $encoder)
    {
        $this->doctrine = $doctrine;
        $this->encoder = $encoder;
    }

    public function index(User $usuario, array $orderBy = null) {

        try {
            $dias = $this->doctrine->getRepository(Dia::class)->findBy(['usuario' => $usuario], $orderBy);

            foreach($dias as $key => $dia) {
                $dias[$key]->serializarHoras();
                $dias[$key]->serializarAtividades();
            }

            return $dias;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function createNewDiaFromDataCompleta(DateTimeImmutable $dataCompleta, User $usuario) {
        
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();

            $dia = new Dia();
            $dia->setDataCompleta($dataCompleta);
            $dia->setCreatedAt(new DateTimeImmutable());
            $dia->setUsuario($usuario);
            
            $entityManager->persist($dia);
            // $entityManager->flush();

            for ($i=6; $i < 23; $i++) { 
                $hora = new Hora();
                $hora->setHora($i);
                $hora->setDia($dia);
                $hora->setUsuario($dia->getUsuario());
                $hora->setCreatedAt(new DateTimeImmutable());
                $entityManager->persist($hora);
            }

            $entityManager->flush();

            $entityManager->getConnection()->commit();

            return $dia;

        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

}