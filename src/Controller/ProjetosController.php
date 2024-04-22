<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Projeto;
use App\Service\ProjetosService;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProjetosController extends AbstractController
{

    /**
     * @var ProjetosService
     */
    private $projetosService;

    public function __construct(ProjetosService $projetosService)
    {
        $this->projetosService = $projetosService;
    }

    /**
     * @Route("/projetos", name="app_projetos_list", methods={"GET","HEAD"})
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();

            $orderBy = null;
            if($request->query->get('orderBy') != null){
                $orderBy = $request->query->get('orderBy');
                $orderBy = explode(',', $orderBy);
                $orderBy = [$orderBy[0] => $orderBy[1]];
            }

            $filters = [];
            if($request->query->get('situacao') != null){
                $filter = $request->query->get('situacao');
                $filters['situacao'] = $filter;
            }
            if($request->query->get('prioridade') != null){
                $filter = $request->query->get('prioridade');
                $filters['prioridade'] = $filter;
            }

            $projetos = $this->projetosService->listaProjetosUseCase($usuario, $filters, $orderBy);

            $loadTarefas = $request->query->get('loadTarefas');
            if(filter_var($loadTarefas, FILTER_VALIDATE_BOOLEAN)) {
                for ($i=0; $i < count($projetos); $i++) {
                    $projetos[$i]->serializarTarefas();
                }
            }

            return new JsonResponse($projetos);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function validateCreate($request)
    {
        if(!property_exists($request, 'nome') || $request->nome == null || $request->nome == ''){
            throw new Exception('Nome não pode ser vazio.');
        }
        if(!property_exists($request, 'anotacoes') || $request->anotacoes == null || $request->anotacoes == ''){
            throw new Exception('Anotações não pode ser vazio.');
        }
        if(!property_exists($request, 'prioridade') || $request->prioridade == null || $request->prioridade == ''){
            throw new Exception('Prioridade não pode ser vazio.');
        }
        if(!in_array($request->prioridade, Projeto::LISTA_PRIORIDADES)){
            throw new Exception('Prioridade não suportada.');
        }
    }

    /**
     * @Route("/projetos", name="app_projetos_create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $requestContent = $request->getContent();
            $requestObj = json_decode($requestContent);
            $usuario = $this->getUser();

            $this->validateCreate($requestObj);
            
            $projeto = $this->projetosService->factoryCreateProjetoUsecase(
                $requestObj->nome,
                $requestObj->anotacoes,
                $requestObj->dataPrazo,
                $requestObj->prioridade
            );

            $projeto = $this->projetosService->createProjetoUsecase($projeto, $usuario);

            return new JsonResponse($projeto, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function validateUpdate($request)
    {
        if($request->nome == null || $request->nome == ''){
            throw new Exception('Nome não pode ser vazio');
        }
        if($request->anotacoes == null || $request->anotacoes == ''){
            throw new Exception('Anotações não pode ser vazio');
        }
        if($request->situacao == null || $request->situacao == ''){
            throw new Exception('Situação não pode ser vazio');
        }
        if($request->prioridade == null || $request->prioridade == ''){
            throw new Exception('Prioridade não pode ser vazio');
        }
    }

    private function fillUpdateProjeto($request, Projeto $projeto)
    {
        if($request->dataPrazo != null && $request->dataPrazo != '') {
            $dataPrazo = new DateTimeImmutable($request->dataPrazo);
            $projeto->setDataPrazo($dataPrazo);
        }
        $projeto->setNome($request->nome);
        $projeto->setAnotacoes($request->anotacoes);
        $projeto->setPrioridade($request->prioridade);
        $projeto->setSituacao($request->situacao);
        return $projeto;
    }

    /**
     * @Route("/projetos/{id}", name="app_projetos_update", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse
    {
        try {
            $requestContent = $request->getContent();
            $requestObj = json_decode($requestContent);
            $usuario = $this->getUser();

            $this->validateUpdate($requestObj);
            $projeto = $this->projetosService->findOne($usuario, $id);
            // $oldProjeto = clone($projeto);
            $projeto = $this->fillUpdateProjeto($requestObj, $projeto);

            $projeto = $this->projetosService->updateProjeto($projeto, $usuario);

            return new JsonResponse($projeto, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/projetos/{id}", name="app_projetos_delete", methods={"DELETE"})
     */
    public function delete($id, Request $request): JsonResponse
    {
        try {
            // $requestContent = $request->getContent();
            // $requestObj = json_decode($requestContent);
            $usuario = $this->getUser();

            // $this->validateUpdate($requestObj);
            $projeto = $this->projetosService->findOne($usuario, $id);
            // $oldProjeto = clone($projeto);
            // $projeto = $this->fillUpdateProjeto($requestObj, $projeto);

            $projeto = $this->projetosService->deleteProjeto($projeto, $usuario);

            return new JsonResponse($projeto, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}