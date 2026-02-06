<?php

namespace App\Controller;

use Exception;
use DateTimeImmutable;
use App\Entity\Habito;
use PhpParser\JsonDecoder;
use App\Service\HabitosService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class HabitosController extends AbstractController
{
    
    private HabitosService $habitosService;

    public function __construct(HabitosService $habitosService)
    {
        $this->habitosService = $habitosService;
    }

    private function getFilters(Request $request)
    {
        $filters = [];
        return $filters;
    }
    
    private function getOrderBy(Request $request)
    {
        $orderBy = null;
        if($request->query->get('orderBy') != null){
            $orderBy = $request->query->get('orderBy');
            $orderBy = explode(',', $orderBy);
            $orderBy = [$orderBy[0] => $orderBy[1]];
        }
        return $orderBy;
    }

    private function getRelations(Request $request)
    {
        $relations = [];
        if($request->query->get('relations') != null){
            $relations = $request->query->get('relations');
            $relations = explode(',', $relations);
        }
        return $relations;
    }

    /**
     * @Route("/habitos", name="app_habitos_list", methods={"GET", "HEAD"})
     */
    public function index(Request $request): Response
    {
        try {
            $usuario = $this->getUser();

            $filters = $this->getFilters($request);
            $orderBy = $this->getOrderBy($request);
            $relations = $this->getRelations($request);

            $entityList = $this->habitosService->listaHabitosUseCase($usuario, $filters, $orderBy, $relations);

            return new JsonResponse($entityList);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    
    private function validateCreateHabitoData($requestData) {
        if( !property_exists($requestData, 'descricao') || $requestData->descricao == ''){
            throw new BadRequestHttpException("Descrição não enviada.");
        }
        if( !property_exists($requestData, 'hora') || $requestData->hora == ''){
            throw new BadRequestHttpException("Hora não enviada.");
        }
    }

    /**
     * @Route("/habitos", name="app_habitos_create", methods={"POST"})
     */
    public function create(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());
            $this->validateCreateHabitoData($requestData);

            $habito = $this->habitosService->factoryHabito($requestData->descricao, $requestData->motivo, $requestData->hora, $usuario);
            $habito = $this->habitosService->createNewHabito($habito);
            return new JsonResponse($habito, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function validateUpdateHabitoData($requestData) {
        if( !property_exists($requestData, 'descricao') || $requestData->descricao == ''){
            throw new BadRequestHttpException("Descrição não enviada.");
        }
        if( !property_exists($requestData, 'hora') || $requestData->hora == ''){
            throw new BadRequestHttpException("Hora não enviada.");
        }
    }

    /**
     * @Route("/habitos/{id}", name="app_habitos_update", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());
            $this->validateUpdateHabitoData($requestData);

            $habito = $this->habitosService->find($id, $usuario);
            if($habito == null) {
                throw new NotFoundHttpException('Habito não encontrada.');
            }

            $habito->setDescricao($requestData->descricao);
            $habito->setMotivo($requestData->motivo);
            if($requestData->hora != '') {
                $habito->setHora(new DateTimeImmutable($requestData->hora));
            }
            $this->habitosService->atualizaHabitosUseCase($habito);

            return new JsonResponse();
            
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/habitos/{id}/concluir", name="app_habitos_concluir", methods={"POST"})
     */
    public function concluiHabito($id, Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $requestData = json_decode($request->getContent());
            $usuario = $this->getUser();
            $textoObservacao = $requestData->textoObservacao;
            $habito = $this->habitosService->find($id, $usuario);
            if($habito == null) {
                throw new NotFoundHttpException('Habito não encontrado.');
            }
            $dados = $this->habitosService->concluir($textoObservacao, $habito, $usuario);
            $dados['habito'] = $this->habitosService->find($dados['habito']->getId(), $usuario);
            return new JsonResponse($dados, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    
    // /**
    //  * @Route("/habitos/{id}/falhar", name="app_habitos_falhar", methods={"POST"})
    //  */
    // public function falharHabito($id, Request $request, ManagerRegistry $doctrine): JsonResponse
    // {
    //     try {
    //         $usuario = $this->getUser();
    //         $habito = $this->habitosService->find($id, $usuario);
    //         if($habito == null) {
    //             throw new NotFoundHttpException('Habito não encontrada.');
    //         }
    //         $habito = $this->habitosService->falhar($habito, $usuario);
    //         $habito = $this->habitosService->find($habito->getId(), $usuario);
    //         return new JsonResponse($habito, Response::HTTP_OK);
    //     } catch (\Exception $e) {
    //         return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
    //     }
    // }

}
