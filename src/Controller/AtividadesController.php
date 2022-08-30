<?php

namespace App\Controller;

use Exception;
use App\Entity\Hora;
use DateTimeImmutable;
use App\Entity\Atividade;
use App\Service\AtividadesService;
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
    
    private $atividadesService;

    public function __construct(AtividadesService $atividadesService)
    {
        $this->atividadesService = $atividadesService;
    }

    /**
     * @Route("/atividades", name="app_atividades_list", methods={"GET", "HEAD"})
     */
    public function index(): Response
    {
        try {
            $usuario = $this->getUser();
            
            $entityList = $this->atividadesService->index($usuario);

            return new JsonResponse($entityList);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/atividades", name="app_atividades_create", methods={"POST"})
     */
    public function create(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());

            $descricao = $requestData->descricao;
            $horaId = $requestData->hora;

            $atividade = $this->atividadesService->factoryAtividade($descricao, $horaId, $usuario);
            $atividade = $this->atividadesService->createNewAtividade($atividade);

            return new JsonResponse($atividade, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/atividades/{id}", name="app_atividades_update", methods={"PUT"})
     */
    public function update($id, Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());
            $descricao = $requestData->descricao;
            // $horaId = $requestData->hora;

            $atividade = $doctrine->getRepository(Atividade::class)->findOneBy([
                'id' => $id,
                'usuario' => $usuario
            ]);
            if($atividade == null) {
                throw new NotFoundHttpException('Atividade não encontrada.');
            }
            $entityManager = $doctrine->getManager();
            $atividade->setDescricao($descricao);
            $atividade->setUpdatedAt(new DateTimeImmutable());
            $entityManager->persist($atividade);
            $entityManager->flush();
            return new JsonResponse();
            
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/atividades/{id}/concluir", name="app_atividades_concluir", methods={"POST"})
     */
    public function concluiAtividade($id, Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();

            $atividade = $doctrine->getRepository(Atividade::class)->findOneBy([
                'id' => $id,
                'usuario' => $usuario
            ]);
            if($atividade == null) {
                throw new NotFoundHttpException('Atividade não encontrada.');
            }

            $atividade = $this->atividadesService->concluir($atividade, $usuario);
            
            $atividade = $this->atividadesService->find($atividade->getId(), $usuario);

            return new JsonResponse($atividade, Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * @Route("/atividades/{id}/falhar", name="app_atividades_falhar", methods={"POST"})
     */
    public function falharAtividade($id, Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();

            $atividade = $doctrine->getRepository(Atividade::class)->findOneBy([
                'id' => $id,
                'usuario' => $usuario
            ]);
            if($atividade == null) {
                throw new NotFoundHttpException('Atividade não encontrada.');
            }

            $atividade = $this->atividadesService->falhar($atividade, $usuario);
            
            $atividade = $this->atividadesService->find($atividade->getId(), $usuario);

            return new JsonResponse($atividade, Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
