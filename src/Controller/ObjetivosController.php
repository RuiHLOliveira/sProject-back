<?php

namespace App\Controller;

use App\Entity\Objetivo;
use App\Entity\Projeto;
use App\Service\AreasService;
use App\Service\ObjetivosService;
use App\Service\ProjetosService;
use App\Service\TagsService;
use App\Utils\ErrorBuilder;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ObjetivosController extends AbstractController
{

    /**
     * @var ObjetivosService
     */
    private $objetivosService;

    /**
     * @var AreasService
     */
    private $areasService;

    public function __construct(
        ObjetivosService $objetivosService,
        AreasService $areasService
    ) {
        $this->objetivosService = $objetivosService;
        $this->areasService = $areasService;
    }

    /**
     * @Route("/objetivos", name="app_objetivos_list", methods={"GET","HEAD"})
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

            $objetivos = $this->objetivosService->listarUseCase($usuario, $filters, $orderBy);



            return new JsonResponse($objetivos);
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
        if(!property_exists($request, 'anotacoes') || $request->anotacoes == null || $request->anotacoes == ''){
            throw new Exception('Anotações não pode ser vazio.');
        }
        if(!property_exists($request, 'respostas') || $request->respostas == null || $request->respostas == ''){
            throw new Exception('respostas não pode ser vazio.');
        }
        // if(!property_exists($request, 'horizonte') || $request->horizonte == null || $request->horizonte == ''){
        //     throw new Exception('horizonte não pode ser vazio.');
        // }
        // if(!in_array($request->prioridade, Objetivo::LISTA_HORIZONTES)){
        //     throw new Exception('Horizonte não suportada.');
        // }
    }

    /**
     * @Route("/objetivos", name="app_objetivos_create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $requestContent = $request->getContent();
            $requestObj = json_decode($requestContent);
            $usuario = $this->getUser();

            $this->validateCreate($requestObj);

            $area = $this->areasService->findOne($usuario, $requestObj->area);
            
            $objetivo = $this->objetivosService->factoryObjetivo(
                $requestObj->nome,
                $requestObj->anotacoes,
                $requestObj->respostas,
                $requestObj->horizonte,
                $requestObj->fotos ?? null,
                $area,
            );

            $objetivo = $this->objetivosService->criarUseCase($objetivo, $usuario);

            return new JsonResponse($objetivo, Response::HTTP_CREATED);
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
        if(!property_exists($request, 'anotacoes') || $request->anotacoes == null || $request->anotacoes == ''){
            throw new Exception('Anotações não pode ser vazio.');
        }
        if(!property_exists($request, 'respostas') || $request->respostas == null || $request->respostas == ''){
            throw new Exception('respostas não pode ser vazio.');
        }
        if(!property_exists($request, 'horizonte') || $request->horizonte == null || $request->horizonte == ''){
            throw new Exception('horizonte não pode ser vazio.');
        }
        if(!in_array($request->horizonte, Objetivo::LISTA_HORIZONTES)){
            throw new Exception('Horizonte não suportada.');
        }
    }

    private function fillUpdate($request, Objetivo $objetivo)
    {
        $objetivo->setNome($request->nome);
        $objetivo->setAnotacoes($request->anotacoes);
        $objetivo->setRespostas($request->respostas);
        $objetivo->setHorizonte($request->horizonte);
        $objetivo->setFotos($request->fotos);
        $objetivo->setSituacao($request->situacao);
        $objetivo->setArea($request->area);
        return $objetivo;
    }

    /**
     * @Route("/objetivos/{id}", name="app_objetivos_update", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse
    {
        try {
            $requestContent = $request->getContent();
            $requestObj = json_decode($requestContent);
            $usuario = $this->getUser();

            $this->validateUpdate($requestObj);
            
            $area = $this->areasService->findOne($usuario, $requestObj->area);
            $requestObj->area = $area;

            $objetivo = $this->objetivosService->findOne($usuario, $id);
            $objetivo = $this->fillUpdate($requestObj, $objetivo);

            $dados = $this->objetivosService->atualizarUseCase($objetivo, $usuario);

            return new JsonResponse($dados, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => ErrorBuilder::getError($e)], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => ErrorBuilder::getError($e)], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/objetivos/{id}", name="app_objetivos_delete", methods={"DELETE"})
     */
    public function delete($id, Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $objetivos = $this->objetivosService->findOne($usuario, $id);
            $objetivos = $this->objetivosService->deletarUseCase($objetivos, $usuario);
            return new JsonResponse($objetivos, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => ErrorBuilder::getError($e)], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => ErrorBuilder::getError($e)], Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     * @Route("/perguntas", name="app_perguntas_list", methods={"GET","HEAD"})
     */
    public function listaPerguntas(Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();

            // $orderBy = [];
            // if($request->query->get('orderBy') != null){
            //     $orderBy = $request->query->get('orderBy');
            //     $orderBy = explode(',', $orderBy);
            //     $orderBy = [$orderBy[0] => $orderBy[1]];
            // }

            // $filters = [];
            // if($request->query->get('situacao') != null){
            //     $value = $request->query->get('situacao');
            //     $filters['situacao'] = $value;
            // }

            // $perguntas = $this->objetivosService->listarUseCase($usuario, $filters, $orderBy);
            $perguntas = [
                [
                    'id' => 1,
                    'pergunta' => 'quando?',
                ],
                [
                    'id' => 2,
                    'pergunta' => 'como você se sente?',
                ],
                [
                    'id' => 3,
                    'pergunta' => 'qual ação você precisou fazer?',
                ],
                [
                    'id' => 4,
                    'pergunta' => 'o que você precisou deixar de ser?',
                ],
            ];

            return new JsonResponse($perguntas);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => ErrorBuilder::getError($e)], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => ErrorBuilder::getError($e)], Response::HTTP_BAD_REQUEST);
        }
    }

}