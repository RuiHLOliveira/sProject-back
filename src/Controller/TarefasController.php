<?php

namespace App\Controller;

use Exception;
use DateTimeImmutable;
use App\Entity\Tarefa;
use PhpParser\JsonDecoder;
use App\Service\TarefasService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TarefasController extends AbstractController
{
    
    private $tarefasService;

    public function __construct(TarefasService $tarefasService)
    {
        $this->tarefasService = $tarefasService;
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
     * @Route("/tarefas", name="app_tarefas_list", methods={"GET", "HEAD"})
     */
    public function index(Request $request): Response
    {
        try {
            $usuario = $this->getUser();

            $filters = $this->getFilters($request);
            $orderBy = $this->getOrderBy($request);

            $entityList = $this->tarefasService->listaTarefasUseCase($usuario, $filters, $orderBy);

            $properties = $this->getProperties($request);

            if(isset($properties['projeto']) && filter_var($properties['projeto'], FILTER_VALIDATE_BOOLEAN)) {
                $bp='';
                for ($i=0; $i < count($entityList); $i++) {
                    $entityList[$i]->serializarProjeto();
                }
            }

            return new JsonResponse($entityList);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    
    private function validateCreateTarefaData($requestData) {
        if( !property_exists($requestData, 'descricao') || $requestData->descricao == ''){
            throw new BadRequestHttpException("Descrição não enviada.");
        }
        if( !property_exists($requestData, 'projeto') || $requestData->projeto == ''){
            throw new BadRequestHttpException("Projeto não enviado.");
        }
        // if( !property_exists($requestData, 'hora') || $requestData->hora == ''){
        //     throw new BadRequestHttpException("Hora não enviada.");
        // }
    }

    /**
     * @Route("/tarefas", name="app_tarefas_create", methods={"POST"})
     */
    public function create(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());
            $this->validateCreateTarefaData($requestData);

            $tarefa = $this->tarefasService->factoryTarefa($requestData->descricao, $requestData->motivo, $requestData->projeto, $requestData->datahora, $usuario);
            $tarefa = $this->tarefasService->createNewTarefa($tarefa);
            return new JsonResponse($tarefa, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function validateUpdateTarefaData($requestData) {
        if( !property_exists($requestData, 'descricao') || $requestData->descricao == ''){
            throw new BadRequestHttpException("Descrição não enviada.");
        }
        // if( !property_exists($requestData, 'hora') || $requestData->hora == ''){
        //     throw new BadRequestHttpException("Hora não enviada.");
        // }
    }

    /**
     * @Route("/tarefas/{id}", name="app_tarefas_update", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());
            $this->validateUpdateTarefaData($requestData);

            $tarefa = $this->validateTarefaExiste($id, $usuario);

            $tarefa->setDescricao($requestData->descricao);
            if($requestData->motivo != null) $tarefa->setMotivo($requestData->motivo);
            if($requestData->datahora != '') {
                $tarefa->setDatahora(new DateTimeImmutable($requestData->datahora));
            } else {
                $tarefa->setDatahora(null);
            }
            $this->tarefasService->atualizaTarefasUseCase($tarefa);

            return new JsonResponse();
            
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    
    /**
     * @Route("/tarefas/{id}", name="app_tarefas_delete", methods={"DELETE"})
     */
    public function delete($id, Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());

            $tarefa = $this->validateTarefaExiste($id, $usuario);

            $this->tarefasService->deleteTarefaUseCase($tarefa, $usuario);

            return new JsonResponse();
            
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/tarefas/{id}/prioridade", name="app_tarefas_update_prioridade", methods={"PUT"})
     */
    public function updatePrioridade($id, Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());
            $tarefa = $this->validateTarefaExiste($id, $usuario);
            $tarefa->setPrioridade($requestData->prioridade);
            $this->tarefasService->atualizaTarefasUseCase($tarefa);
            return new JsonResponse();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/tarefas/{id}/concluir", name="app_tarefas_concluir", methods={"POST"})
     */
    public function concluiTarefa($id, Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $tarefa = $this->validateTarefaExiste($id, $usuario);
            $tarefa = $this->tarefasService->concluir($tarefa, $usuario);
            $tarefa = $this->tarefasService->find($tarefa->getId(), $usuario);
            return new JsonResponse($tarefa, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    //  /**
    //  * @Route("/tarefas/{id}/meu-dia", name="app_tarefas_adicionar_meu_dia", methods={"POST"})
    //  */
    // public function adicionarAoMeuDiaTarefa($id, Request $request, ManagerRegistry $doctrine): JsonResponse
    // {
    //     try {
    //         $usuario = $this->getUser();
    //         $tarefa = $this->validateTarefaExiste($id, $usuario);
    //         $tarefa = $this->tarefasService->adicionarAoMeuDia($tarefa, $usuario);
    //         $tarefa = $this->tarefasService->find($tarefa->getId(), $usuario);
    //         return new JsonResponse($tarefa, Response::HTTP_OK);
    //     } catch (\Exception $e) {
    //         return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
    //     }
    // }
    
    //  /**
    //  * @Route("/tarefas/{id}/remover-meu-dia", name="app_tarefas_remover_meu_dia", methods={"POST"})
    //  */
    // public function removerMeuDiaTarefa($id, Request $request, ManagerRegistry $doctrine): JsonResponse
    // {
    //     try {
    //         $usuario = $this->getUser();
    //         $tarefa = $this->validateTarefaExiste($id, $usuario);
    //         $tarefa = $this->tarefasService->removerMeuDia($tarefa, $usuario);
    //         $tarefa = $this->tarefasService->find($tarefa->getId(), $usuario);
    //         return new JsonResponse($tarefa, Response::HTTP_OK);
    //     } catch (\Exception $e) {
    //         return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
    //     }
    // }
    
    /**
     * @Route("/tarefas/{id}/reagendar-dia-seguinte", name="app_tarefas_falhar", methods={"POST"})
     */
    public function reagendarDiaSeguinte($id, Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $tarefa = $this->validateTarefaExiste($id, $usuario);
            $tarefa = $this->tarefasService->reagendarDiaSeguinte($tarefa, $usuario);
            $tarefa = $this->tarefasService->find($tarefa->getId(), $usuario);
            return new JsonResponse($tarefa, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function validateTarefaExiste($id, $usuario)
    {
        $tarefa = $this->tarefasService->find($id, $usuario);
        if($tarefa == null) {
            throw new NotFoundHttpException('Tarefa não encontrada.');
        }
        return $tarefa;
    }

}
