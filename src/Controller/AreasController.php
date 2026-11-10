<?php

namespace App\Controller;

use Exception;
use DateTimeImmutable;
use App\Entity\Area;
use App\Service\AreasService;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Utils\ErrorBuilder;

class AreasController extends AbstractController
{

    /**
     * @var AreasService
     */
    private $areasService;

    /**
     * @var TagsService
     */
    private $tagsService;

    public function __construct(
        AreasService $areasService
    ) {
        $this->areasService = $areasService;
    }

    /**
     * @Route("/areas", name="app_areas_list", methods={"GET","HEAD"})
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();

            $orderBy = [];
            if($request->query->get('orderBy') != null){
                $orderBy = $request->query->get('orderBy');
                $orderBy = explode(',', $orderBy);
                $orderBy = [$orderBy[0] => $orderBy[1]];
            }

            $filters = [];
            // if($request->query->get('situacao') != null){
            //     $value = $request->query->get('situacao');
            //     $filters['situacao'] = $value;
            // }

            $areas = $this->areasService->listarUseCase($usuario, $filters, $orderBy);

            return new JsonResponse($areas);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => ErrorBuilder::getError($e)], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => ErrorBuilder::getError($e)], Response::HTTP_BAD_REQUEST);
        }
    }

    private function validateCreate($request)
    {
        if(!property_exists($request, 'nome') || $request->nome == null || $request->nome == ''){
            throw new Exception('Nome não pode ser vazio.');
        }
    }

    /**
     * @Route("/areas", name="app_areas_create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $requestContent = $request->getContent();
            $requestObj = json_decode($requestContent);
            $usuario = $this->getUser();

            $this->validateCreate($requestObj);
            
            $area = $this->areasService->factoryArea(
                $requestObj->nome,
            );

            $area = $this->areasService->criarUseCase($area, $usuario);

            return new JsonResponse($area, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => ErrorBuilder::getError($e)], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => ErrorBuilder::getError($e)], Response::HTTP_BAD_REQUEST);
        }
    }

    private function validateUpdate($request)
    {
        if(!property_exists($request, 'nome') || $request->nome == null || $request->nome == ''){
            throw new Exception('Nome não pode ser vazio.');
        }
    }

    private function fillUpdate($request, Area $area)
    {
        $area->setNome($request->nome);
        return $area;
    }

    /**
     * @Route("/areas/{id}", name="app_areas_update", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse
    {
        try {
            $requestContent = $request->getContent();
            $requestObj = json_decode($requestContent);
            $usuario = $this->getUser();

            $this->validateUpdate($requestObj);
            $area = $this->areasService->findOne($usuario, $id);
            $area = $this->fillUpdate($requestObj, $area);

            $dados = $this->areasService->atualizarUseCase($area, $usuario);

            return new JsonResponse($dados, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => ErrorBuilder::getError($e)], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => ErrorBuilder::getError($e)], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/areas/{id}", name="app_areas_delete", methods={"DELETE"})
     */
    public function delete($id, Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $areas = $this->areasService->findOne($usuario, $id);
            $areas = $this->areasService->deletarUseCase($areas, $usuario);
            return new JsonResponse($areas, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => ErrorBuilder::getError($e)], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => ErrorBuilder::getError($e)], Response::HTTP_BAD_REQUEST);
        }
    }

}