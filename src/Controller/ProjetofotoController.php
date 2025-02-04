<?php

namespace App\Controller;

use Exception;
use DateTimeImmutable;
use App\Entity\Projetofoto;
use PhpParser\JsonDecoder;
use App\Service\ProjetosfotosService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProjetofotoController extends AbstractController
{
    
    private $projetosfotosService;

    public function __construct(ProjetosfotosService $projetosfotosService)
    {
        $this->projetosfotosService = $projetosfotosService;
    }

    private function getFilters(Request $request)
    {
        $filters = [];
        $projeto = $request->query->get('projeto');
        if($projeto != ''){
            $filters['projeto'] = $projeto;
        }
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
     * @Route("/projetosfotos", name="app_projetosfotos_list", methods={"GET", "HEAD"})
     */
    public function index(Request $request): Response
    {
        try {
            $usuario = $this->getUser();

            $filters = $this->getFilters($request);
            $orderBy = $this->getOrderBy($request);

            $entityList = $this->projetosfotosService->listaProjetosfotosUseCase($usuario, $filters, $orderBy);

            $properties = $this->getProperties($request);

            if(isset($properties['projeto']) && filter_var($properties['projeto'], FILTER_VALIDATE_BOOLEAN)) {
                for ($i=0; $i < count($entityList); $i++) {
                    $entityList[$i]->serializarProjeto();
                }
            }

            return new JsonResponse($entityList);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    
    private function validateCreateProjetofotoData($requestData) {
        if( !property_exists($requestData, 'descricao') || $requestData->descricao == ''){
            throw new BadRequestHttpException("Descrição não enviada.");
        }
        if( !property_exists($requestData, 'link') || $requestData->link == ''){
            throw new BadRequestHttpException("Link não enviada.");
        }
        if( !property_exists($requestData, 'projeto') || $requestData->projeto == ''){
            throw new BadRequestHttpException("Projeto não enviado.");
        }
    }

    /**
     * @Route("/projetosfotos", name="app_projetosfotos_create", methods={"POST"})
     */
    public function create(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());
            $this->validateCreateProjetofotoData($requestData);

            $projetofoto = $this->projetosfotosService->factoryProjetofoto($requestData->descricao, $requestData->link, $requestData->projeto, $usuario);
            $projetofoto = $this->projetosfotosService->createNewProjetofoto($projetofoto);
            return new JsonResponse($projetofoto, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function validateUpdateProjetofotoData($requestData) {
        if( !property_exists($requestData, 'descricao') || $requestData->descricao == ''){
            throw new BadRequestHttpException("Descrição não enviada.");
        }
        if( !property_exists($requestData, 'link') || $requestData->link == ''){
            throw new BadRequestHttpException("Link não enviada.");
        }
    }

    /**
     * @Route("/projetosfotos/{id}", name="app_projetosfotos_update", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());
            $this->validateUpdateProjetofotoData($requestData);

            $projetofoto = $this->projetosfotosService->find($id, $usuario);
            if($projetofoto == null) {
                throw new NotFoundHttpException('Projeto foto não encontrado.');
            }

            $projetofoto->setDescricao($requestData->descricao);
            $projetofoto->setLink($requestData->link);
            $this->projetosfotosService->atualizaProjetosfotosUseCase($projetofoto);

            return new JsonResponse();
            
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/projetosfotos/{id}", name="app_projetosfotos_delete", methods={"DELETE"})
     */
    public function delete($id, Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();

            $projetofoto = $this->projetosfotosService->find($id, $usuario);

            $projetofoto = $this->projetosfotosService->deleteProjetofoto($projetofoto, $usuario);

            return new JsonResponse($projetofoto, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

}
