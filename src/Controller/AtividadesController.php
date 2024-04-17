<?php

namespace App\Controller;

use Exception;
use DateTimeImmutable;
use App\Entity\Atividade;
use PhpParser\JsonDecoder;
use App\Service\AtividadesService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
    public function index(Request $request): Response
    {
        try {
            $usuario = $this->getUser();

            $orderBy = null;
            if($request->query->get('orderBy') != null){
                $orderBy = $request->query->get('orderBy');
                $orderBy = explode(',', $orderBy);
                $orderBy = [$orderBy[0] => $orderBy[1]];
            }
            
            $entityList = $this->atividadesService->listaAtividadesUseCase($usuario, $orderBy);

            return new JsonResponse($entityList);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    private function validateCreateAtividadeData($requestData) {
        if( !property_exists($requestData, 'descricao') || $requestData->descricao == ''){
            throw new BadRequestHttpException("Descrição não enviada.");
        }
        if( !property_exists($requestData, 'dia') || $requestData->dia == ''){
            throw new BadRequestHttpException("Dia não enviado.");
        }
        if( !property_exists($requestData, 'hora') || $requestData->hora == ''){
            throw new BadRequestHttpException("Hora não enviada.");
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
            $this->validateCreateAtividadeData($requestData);

            $atividade = $this->atividadesService->factoryAtividade($requestData->descricao, $requestData->dia, $requestData->hora, $usuario);
            $atividade = $this->atividadesService->createNewAtividade($atividade);
            return new JsonResponse($atividade, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function validateUpdateAtividadeData($requestData) {
        if( !property_exists($requestData, 'descricao') || $requestData->descricao == ''){
            throw new BadRequestHttpException("Descrição não enviada.");
        }
        if( !property_exists($requestData, 'hora') || $requestData->hora == ''){
            throw new BadRequestHttpException("Hora não enviada.");
        }
    }

    /**
     * @Route("/atividades/{id}", name="app_atividades_update", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());
            $this->validateUpdateAtividadeData($requestData);

            $atividade = $this->atividadesService->find($id, $usuario);
            if($atividade == null) {
                throw new NotFoundHttpException('Atividade não encontrada.');
            }

            $atividade->setDescricao($requestData->descricao);
            $atividade->setHora(new DateTimeImmutable($requestData->hora));
            $this->atividadesService->atualizaAtividadesUseCase($atividade);

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
            $atividade = $this->atividadesService->find($id, $usuario);
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
            $atividade = $this->atividadesService->find($id, $usuario);
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
