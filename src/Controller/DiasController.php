<?php

namespace App\Controller;

use App\Entity\Dia;
use App\Entity\Hora;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DiasController extends AbstractController
{
    /**
     * @Route("/dias", name="app_dias_list", methods={"GET","HEAD"})
     */
    public function index(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $dias = $doctrine->getRepository(Dia::class)->findBy([
                'usuario' => $usuario
            ]);

            foreach($dias as $key => $dia) {
                $dias[$key]->serializarHoras();
                $dias[$key]->serializarAtividades();
            }

            return new JsonResponse($dias);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/dias", name="app_dias_create", methods={"POST"})
     */
    public function create(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $requestObj = json_decode($request->getContent());

            $entityManager = $doctrine->getManager();

            $usuario = $this->getUser();

            $dataCompleta = new DateTimeImmutable($requestObj->dataCompleta);
            $dia = new Dia();
            $dia->setDataCompleta($dataCompleta);
            $dia->setCreatedAt(new DateTimeImmutable());
            $dia->setUsuario($usuario);
            $entityManager->persist($dia);
            $entityManager->flush();

            for ($i=6; $i < 23; $i++) { 
                $hora = new Hora();
                $hora->setHora($i);
                $hora->setDia($dia);
                $hora->setUsuario($usuario);
                $hora->setCreatedAt(new DateTimeImmutable());
                $entityManager->persist($hora);
            }
            $entityManager->flush();

            return new JsonResponse($dia, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}