<?php

namespace App\Controller;

use Exception;
use DateTimeImmutable;
use App\Entity\CategoriaItem;
use PhpParser\JsonDecoder;
use App\Service\CategoriaItemService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CategoriaItemController extends AbstractController
{
    
    private $categoriaItemService;

    public function __construct(CategoriaItemService $categoriaItemService)
    {
        $this->categoriaItemService = $categoriaItemService;
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

    private function getProperties(Request $request)
    {
        $properties = explode(',',$request->query->get('properties'));
        foreach($properties as $key => $value) {
            $properties[$value] = true;
            unset($properties[$key]);
        }
        return $properties;
    }

    /**
     * @Route("/categoriaItems", name="app_categoriaItems_list", methods={"GET", "HEAD"})
     */
    public function index(Request $request): Response
    {
        try {
            $usuario = $this->getUser();

            $filters = [];
            $orderBy = $this->getOrderBy($request);

            $entityList = $this->categoriaItemService->listaCategoriaItemsUseCase($usuario, $filters, $orderBy);

            $properties = $this->getProperties($request);

            return new JsonResponse($entityList);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    
    private function validateCreateCategoriaItemData($requestData) {
        if( !property_exists($requestData, 'categoria') || $requestData->categoria == ''){
            throw new BadRequestHttpException("Categoria não enviada.");
        }
    }

    /**
     * @Route("/categoriaItems", name="app_categoriaItems_create", methods={"POST"})
     */
    public function create(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());
            $this->validateCreateCategoriaItemData($requestData);
            $categoriaItem = $this->categoriaItemService->factoryCategoriaItem(
                $requestData->categoria,
                $usuario
            );
            $categoriaItem = $this->categoriaItemService->createNewCategoriaItem($categoriaItem);
            return new JsonResponse($categoriaItem, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function validateUpdateCategoriaItemData($requestData) {
        $this->validateCreateCategoriaItemData($requestData);
    }

    /**
     * @Route("/categoriaItems/{id}", name="app_categoriaItems_update", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());
            $this->validateUpdateCategoriaItemData($requestData);
            $categoriaItem = $this->categoriaItemService->find($id, $usuario);
            if($categoriaItem == null) {
                throw new NotFoundHttpException('categoriaItem não encontrada.');
            }
            $categoriaItem->setCategoria($requestData->categoria);
            $this->categoriaItemService->atualizacategoriaItemUseCase($categoriaItem);
            return new JsonResponse();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

}
