<?php

namespace App\Controller;

use Exception;
use App\Entity\Hora;
use DateTimeImmutable;
use App\Entity\Atividade;
use PhpParser\JsonDecoder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AtividadesController extends AbstractController
{
    /**
     * @Route("/atividades", name="app_atividades_list", methods={"GET", "HEAD"})
     */
    public function index(): Response
    {
        throw new Exception('implementar');
    }

    /**
     * @Route("/atividades", name="app_atividades_create", methods={"POST"})
     */
    public function create(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $requestData = json_decode($request->getContent());
        $descricao = $requestData->descricao;
        $horaId = $requestData->hora;

        $hora = $doctrine->getRepository(Hora::class)->find($horaId);
        if($hora == null) {
            throw new NotFoundHttpException('Hora não encontrada.');
        }
        $entityManager = $doctrine->getManager();
        $atividade = new Atividade();
        $atividade->setDescricao($descricao);
        $atividade->setHora($hora);
        $atividade->setCreatedAt(new DateTimeImmutable());
        $entityManager->persist($atividade);
        $entityManager->flush();
        return new JsonResponse();
    }

    /**
     * @Route("/atividades/{id}", name="app_atividades_create", methods={"PUT"})
     */
    public function update($id, Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $requestData = json_decode($request->getContent());
        $descricao = $requestData->descricao;
        // $horaId = $requestData->hora;

        $atividade = $doctrine->getRepository(Atividade::class)->find($id);
        if($atividade == null) {
            throw new NotFoundHttpException('Atividade não encontrada.');
        }
        $entityManager = $doctrine->getManager();
        $atividade->setDescricao($descricao);
        $atividade->setUpdatedAt(new DateTimeImmutable());
        $entityManager->persist($atividade);
        $entityManager->flush();
        return new JsonResponse();
    }
}
