<?php

namespace App\Controller;

use Exception;
use DateTimeImmutable;
use PhpParser\JsonDecoder;
use App\Service\ConfiguracoesService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ConfiguracoesController extends AbstractController
{
    
    /**
     * @var ConfiguracoesService
     */
    private $configuracoesService;

    public function __construct(ConfiguracoesService $configuracoesService)
    {
        $this->configuracoesService = $configuracoesService;
    }

    /**
     * @Route("/configuracoes", name="app_configuracoes_list", methods={"GET", "HEAD"})
     */
    public function index(Request $request): Response
    {
        try {
            $usuario = $this->getUser();

            $orderBy = null;
            if($request->query->get('orderBy') != null){
                $orderBy = $request->query->get('orderBy');
                $orderBy = explode(',', $orderBy);
                $orderBy = [$orderBy[0] => $orderBy[1]];
            }
            
            $entityList = $this->configuracoesService->listaConfiguracoesUseCase($usuario, $orderBy);

            return new JsonResponse($entityList);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    
    private function validateCreateConfiguracaoData($requestData) {
        if( !property_exists($requestData, 'chave') || $requestData->chave == ''){
            throw new BadRequestHttpException("chave não enviada.");
        }
        if( !property_exists($requestData, 'valor') || $requestData->valor == ''){
            throw new BadRequestHttpException("valor não enviado.");
        }
    }

    /**
     * @Route("/configuracoes", name="app_configuracoes_create", methods={"POST"})
     */
    public function create(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());
            $this->validateCreateConfiguracaoData($requestData);

            $configuracao = $this->configuracoesService->factoryConfiguracao(
                $requestData->chave,
                $requestData->valor,
                $usuario
            );
            $configuracao = $this->configuracoesService->createNewConfiguracao($configuracao);
            return new JsonResponse($configuracao, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function validateUpdateConfiguracaoData($requestData) {
        if( !property_exists($requestData, 'chave') || $requestData->chave == ''){
            throw new BadRequestHttpException("chave não enviada.");
        }
        if( !property_exists($requestData, 'valor') || $requestData->valor == ''){
            throw new BadRequestHttpException("valor não enviado.");
        }
    }

    /**
     * @Route("/configuracoes/{id}", name="app_configuracoes_update", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());
            $this->validateUpdateConfiguracaoData($requestData);

            $configuracao = $this->configuracoesService->find($id, $usuario);
            if($configuracao == null) {
                throw new NotFoundHttpException('Configuracao não encontrada.');
            }

            $configuracao->setValor($requestData->valor);
            $this->configuracoesService->atualizaConfiguracoesUseCase($configuracao);

            return new JsonResponse();
            
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    
    /**
     * @Route("/configuracoes/criarPadrao", name="app_configuracoes_criar_padrao", methods={"POST"})
     */
    public function criarPadrao(): JsonResponse
    {
        try {
            $usuario = $this->getUser();

            $this->configuracoesService->verificaECriaConfiguracoesPadrao($usuario);

            return new JsonResponse();
            
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }


}
