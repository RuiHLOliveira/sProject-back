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

class RecompensasacoesController extends AbstractController
{

    private RecompensasacoesService $recomepnsasacoesService;


    public function __construct(
        RecompensasacoesService $recomepnsasacoesService
    ) {
        $this->recomepnsasacoesService = $recomepnsasacoesService;
    }

    /**
     * @Route("/recompensasacoes", name="app_recompensasacoes_list", methods={"GET","HEAD"})
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

            $recompensasacoes = $this->recomepnsasacoesService->listUseCase($filters, $orderBy);

            return new JsonResponse($recompensasacoes);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/recompensasacoes", name="app_recompensasacoes_create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $requestContent = $request->getContent();
            $requestObj = json_decode($requestContent);
            $usuario = $this->getUser();

            $this->validateCreate($requestObj);
            $recompensa = $this->recomepnsasacoesService->factory($requestObj->quantidade, $requestObj->tipoatividade, $requestObj->recompensa);
            $recompensa = $this->recomepnsasacoesService->createUseCase($recompensa, $usuario);

            return new JsonResponse($recompensa, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function validateCreate($request)
    {
        if(!property_exists($request, 'quantidade') || $request->quantidade == null || $request->quantidade == '' || $request->quantidade == 0){
            throw new Exception('quantidade não pode ser vazio.');
        }
        if(!property_exists($request, 'tipoatividade') || $request->tipoatividade == null || $request->tipoatividade == ''){
            throw new Exception('tipoatividade não pode ser vazio.');
        }
        if(!property_exists($request, 'recompensa') || $request->recompensa == null || $request->recompensa == ''){
            throw new Exception('recompensa não pode ser vazio.');
        }
    }

}