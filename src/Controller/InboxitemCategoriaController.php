<?php

namespace App\Controller;

use Exception;
use DateTimeImmutable;
use App\Entity\InboxitemCategoria;
use PhpParser\JsonDecoder;
use App\Service\InboxitemCategoriaService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InboxitemCategoriaController extends AbstractController
{
    
    private $inboxitemCategoriaService;

    public function __construct(InboxitemCategoriaService $inboxitemCategoriaService)
    {
        $this->inboxitemCategoriaService = $inboxitemCategoriaService;
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
     * @Route("/inboxitemCategorias", name="app_inboxitemCategorias_list", methods={"GET", "HEAD"})
     */
    public function index(Request $request): Response
    {
        try {
            $usuario = $this->getUser();

            $filters = [];
            $orderBy = $this->getOrderBy($request);

            $entityList = $this->inboxitemCategoriaService->listaInboxitemCategoriasUseCase($usuario, $filters, $orderBy);

            $properties = $this->getProperties($request);

            return new JsonResponse($entityList);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    
    private function validateCreateInboxitemCategoriaData($requestData) {
        if( !property_exists($requestData, 'categoria') || $requestData->categoria == ''){
            throw new BadRequestHttpException("Categoria não enviada.");
        }
    }

    /**
     * @Route("/inboxitemCategorias", name="app_inboxitemCategorias_create", methods={"POST"})
     */
    public function create(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());
            $this->validateCreateInboxitemCategoriaData($requestData);
            $inboxitemCategoria = $this->inboxitemCategoriaService->factoryInboxitemCategoria(
                $requestData->categoria,
                $usuario
            );
            $inboxitemCategoria = $this->inboxitemCategoriaService->createNewInboxitemCategoria($inboxitemCategoria);
            return new JsonResponse($inboxitemCategoria, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function validateUpdateInboxitemCategoriaData($requestData) {
        $this->validateCreateInboxitemCategoriaData($requestData);
    }

    /**
     * @Route("/inboxitemCategorias/{id}", name="app_inboxitemCategorias_update", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());
            $this->validateUpdateInboxitemCategoriaData($requestData);
            $inboxitemCategoria = $this->inboxitemCategoriaService->find($id, $usuario);
            if($inboxitemCategoria == null) {
                throw new NotFoundHttpException('inboxitemCategoria não encontrada.');
            }
            $inboxitemCategoria->setCategoria($requestData->categoria);
            $this->inboxitemCategoriaService->atualizainboxitemCategoriaUseCase($inboxitemCategoria);
            return new JsonResponse();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

}
