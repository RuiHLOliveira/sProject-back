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

    public function __construct(
        RecompensasService $recomepnsasService
    ) {
        $this->recomepnsasService = $recomepnsasService;
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
            // if($request->query->get('orderBy') != null){
            //     $orderBy = $request->query->get('orderBy');
            //     $orderBy = explode(',', $orderBy);
            //     $orderBy = [$orderBy[0] => $orderBy[1]];
            // }

            $recompensas = $this->recomepnsasService->listUseCase($filters, $orderBy);

            return new JsonResponse($recompensas);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

}