<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\InboxItem;
use PhpParser\JsonDecoder;
use App\Service\InboxItemService;
use App\Service\CategoriaItemService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InboxItemController extends AbstractController
{
    
    private $inboxItemService;
    private $categoriaItemService;

    public function __construct(
        InboxItemService $inboxItemService,
        CategoriaItemService $categoriaItemService
    ) {
        $this->inboxItemService = $inboxItemService;
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

    private function getFilters(Request $request, User $usuario): array
    {
        $filters = [];
        $categoriaItem = $request->query->get('categoriaItem');
        if($categoriaItem != null){
            if($categoriaItem > 0){
                $filters['categoriaItem'] = $this->categoriaItemService->find($categoriaItem, $usuario);
            } elseif ($categoriaItem == 0){
                $filters['categoriaItem'] = null;
            }
        }
        return $filters;
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
     * @Route("/inboxItems", name="app_inboxItems_list", methods={"GET", "HEAD"})
     */
    public function index(Request $request): Response
    {
        try {
            $usuario = $this->getUser();

            $filters = $this->getFilters($request, $usuario);
            $orderBy = $this->getOrderBy($request);

            $entityList = $this->inboxItemService->listaInboxItemsUseCase($usuario, $filters, $orderBy);

            $properties = $this->getProperties($request);

            // if(isset($properties['projeto']) && filter_var($properties['projeto'], FILTER_VALIDATE_BOOLEAN)) {
            //     $bp='';
            //     for ($i=0; $i < count($entityList); $i++) {
            //         $entityList[$i]->serializarProjeto();
            //     }
            // }

            return new JsonResponse($entityList);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    
    private function validateCreateInboxItemData($requestData) {
        if( !property_exists($requestData, 'link') || $requestData->link == ''){
            throw new BadRequestHttpException("Link não enviada.");
        }
    }

    /**
     * @Route("/inboxItems", name="app_inboxItems_create", methods={"POST"})
     */
    public function create(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());
            $this->validateCreateInboxItemData($requestData);
            $inboxItem = $this->inboxItemService->factoryInboxItem(
                $requestData->link,
                $requestData->nome,
                $usuario
            );
            $inboxItem = $this->inboxItemService->createNewInboxItem($inboxItem);
            return new JsonResponse($inboxItem, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function validateUpdateInboxItemData($requestData) {
        $this->validateCreateInboxItemData($requestData);
        if( !property_exists($requestData, 'nome') || $requestData->nome == ''){
            throw new BadRequestHttpException("Nome não enviada.");
        }
        if( !property_exists($requestData, 'categoriaItem') || $requestData->categoriaItem == ''){
            throw new BadRequestHttpException("Categoria não enviada.");
        }
        // if( !property_exists($requestData, 'origem') || $requestData->origem == ''){
        //     throw new BadRequestHttpException("Origem não enviada.");
        // }
    }

    /**
     * @Route("/inboxItems/{id}", name="app_inboxItems_update", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());
            $this->validateUpdateInboxItemData($requestData);

            $inboxItem = $this->inboxItemService->find($id, $usuario);
            if($inboxItem == null) {
                throw new NotFoundHttpException('inboxItem não encontrada.');
            }

            $inboxItem->setNome($requestData->nome);
            $inboxItem->setLink($requestData->link);
            $categoriaItem = $this->categoriaItemService->find($requestData->categoriaItem->id, $usuario);
            $inboxItem->setCategoriaItem($categoriaItem);
            $inboxItem->setAcao($requestData->acao);
            $this->inboxItemService->atualizainboxItemUseCase($inboxItem);

            return new JsonResponse();
            
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    
    /**
     * @Route("/inboxItems/{id}", name="app_inboxItems_delete", methods={"DELETE"})
     */
    public function delete($id, Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();

            $inboxItem = $this->inboxItemService->find($id, $usuario);
            if($inboxItem == null) {
                throw new NotFoundHttpException('inboxItem não encontrada.');
            }

            $inboxItem = $this->inboxItemService->delete($inboxItem, $usuario);

            return new JsonResponse($inboxItem, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
