<?php

namespace App\Controller;

use Exception;
use DateTimeImmutable;
use App\Entity\Projeto;
use App\Service\ProjetosService;
use App\Service\TagsService;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjetosController extends AbstractController
{

    /**
     * @var ProjetosService
     */
    private $projetosService;

    /**
     * @var TagsService
     */
    private $tagsService;

    public function __construct(
        ProjetosService $projetosService,
        TagsService $tagsService
    ) {
        $this->projetosService = $projetosService;
        $this->tagsService = $tagsService;
    }

    /**
     * @Route("/projetos", name="app_projetos_list", methods={"GET","HEAD"})
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
            if($request->query->get('situacao') != null){
                $value = $request->query->get('situacao');
                $filters['situacao'] = $value;
            }
            if($request->query->get('prioridade') != null){
                $value = $request->query->get('prioridade');
                $filters['prioridade'] = $value;
            }
            if($request->query->get('fixado') != null){
                $value = $request->query->get('fixado');
                if($value == '1') $value = true;
                if($value == '0') $value = false;
                $filters['fixado'] = $value;
            }


            $projetos = $this->projetosService->listaProjetosUseCase($usuario, $filters, $orderBy);

            $loadTarefas = $request->query->get('loadTarefas');
            if(filter_var($loadTarefas, FILTER_VALIDATE_BOOLEAN)) {
                for ($i=0; $i < count($projetos); $i++) {
                    $projetos[$i]->serializarTarefas();
                }
            }
            $loadProjetosfotos = $request->query->get('loadProjetosfotos');
            if(filter_var($loadProjetosfotos, FILTER_VALIDATE_BOOLEAN)) {
                for ($i=0; $i < count($projetos); $i++) {
                    $projetos[$i]->serializarProjetosfotos();
                }
            }

            return new JsonResponse($projetos);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
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
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
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

            $dados = $this->projetosService->updateProjeto($projeto, $usuario);

            return new JsonResponse($dados, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/projetos/{id}/tags", name="app_projetos_update_tags", methods={"PUT"})
     */
    public function updateTags($id, Request $request): JsonResponse
    {
        try {
            $requestContent = $request->getContent();
            $requestObj = json_decode($requestContent);
            $usuario = $this->getUser();

            //$this->validateUpdate($requestObj);
            $projeto = $this->projetosService->findOne($usuario, $id);
            //$projeto = $this->fillUpdateProjeto($requestObj, $projeto);

            // TODO validar as tags todas
            $tags = $requestObj->tags;
            $tags = $this->filtraTagsDados($tags);
            $this->validaTags($projeto, $tags);

            $projeto->setTags($tags);
            $projeto = $this->projetosService->updateProjeto($projeto, $usuario);

            return new JsonResponse($projeto, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function filtraTagsDados($tags)
    {
        foreach ($tags as $key => $tag) {
            unset($tags[$key]);
            $tags[$key] = [
                'id' => $tag->id,
                'descricao' => $tag->descricao,
                'cor' => $tag->cor,
            ];
        }
        return $tags;
    }

    private function validaTags(Projeto $projeto, array $tags)
    {
        // tags são as mesmas?
        // tags passadas existem?
        $oldTags = $projeto->getTags();
        if(json_encode($tags) == json_encode($oldTags)) return;
        foreach ($tags as $key => $tag) {
            $tagDb = $this->tagsService->find($tag['id'], $projeto->getUsuario());
            if($tagDb == null) throw new LogicException("Tag inexistente!");
            if($tagDb->getDescricao() != $tag['descricao']) throw new LogicException("Tag incorreta!");
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
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/projetos/{id}/fixar-desafixar", name="app_projetos_fixardesafixar", methods={"POST"})
     */
    public function fixarDesafixar($id, Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $projeto = $this->projetosService->findOne($usuario, $id);
            if($projeto == null) {
                throw new NotFoundHttpException('Projeto não encontrado.');
            }
            $response = $this->projetosService->fixarDesafixar($projeto, $usuario);
            $projeto = $this->projetosService->findOne($usuario, $projeto->getId());
            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}