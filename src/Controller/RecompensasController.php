<?php

namespace App\Controller;

use Exception;
use LogicException;
use DateTimeImmutable;
use App\Entity\Personagem;
use App\Service\RecompensasacoesService;
use App\Service\TagsService;
use App\Service\RecompensasService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RecompensasController extends AbstractController
{
    private RecompensasService $recomepnsasService;
    private RecompensasacoesService $recompensasacoesService;

    public function __construct(
        RecompensasService $recomepnsasService,
        RecompensasacoesService $recompensasacoesService
    ) {
        $this->recomepnsasService = $recomepnsasService;
        $this->recompensasacoesService = $recompensasacoesService;
    }

    /**
     * @Route("/recompensas", name="app_recompensas_list", methods={"GET","HEAD"})
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();

            $filters = [];
            $orderBy = [];
            if($request->query->get('orderBy') != null){
                $orderBy = $request->query->get('orderBy');
                $orderBy = explode(',', $orderBy);
                $orderBy = [$orderBy[0] => $orderBy[1]];
            }

            $relations = explode(',',$request->query->get('relations'));
            $loadRecompensasacoes = in_array('recompensasacoes', $relations);

            $recompensas = $this->recomepnsasService->listUseCase($filters, $orderBy);

            if($loadRecompensasacoes) {
                $recompensas = array_map(function ($recompensa){
                    $acoes = $this->recompensasacoesService->findAll(['recompensa' => $recompensa]);
                    $recompensa->setRecompensaacoes($acoes);
                    $recompensa->serializarRecompensasAcoes();
                    return $recompensa;
                },$recompensas);
            }

            return new JsonResponse($recompensas);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/recompensas", name="app_recompensas_create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $requestContent = $request->getContent();
            $requestObj = json_decode($requestContent);
            $usuario = $this->getUser();

            $this->validateCreate($requestObj);
            
            $recompensa = $this->recomepnsasService->factory($requestObj->nome);
            $recompensa = $this->recomepnsasService->createUseCase($recompensa, $usuario);

            return new JsonResponse($recompensa, Response::HTTP_CREATED);
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
    }

}