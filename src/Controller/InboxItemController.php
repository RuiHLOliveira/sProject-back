<?php

namespace App\Controller;

use Exception;
use DateTimeImmutable;
use App\Entity\InboxItem;
use PhpParser\JsonDecoder;
use App\Service\InboxItemService;
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

    public function __construct(InboxItemService $inboxItemService)
    {
        $this->inboxItemService = $inboxItemService;
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
     * @Route("/inboxItems", name="app_inboxItems_list", methods={"GET", "HEAD"})
     */
    public function index(Request $request): Response
    {
        try {
            $usuario = $this->getUser();

            $filters = [];
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
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
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
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function validateUpdateInboxItemData($requestData) {
        $this->validateCreateInboxItemData($requestData);
        if( !property_exists($requestData, 'nome') || $requestData->nome == ''){
            throw new BadRequestHttpException("Nome não enviada.");
        }
        if( !property_exists($requestData, 'categoria') || $requestData->categoria == ''){
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
            // $inboxItem->setOrigem($requestData->origem);
            $inboxItem->setCategoria($requestData->categoria);
            $inboxItem->setAcao($requestData->acao);
            $this->inboxItemService->atualizainboxItemUseCase($inboxItem);

            return new JsonResponse();
            
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
