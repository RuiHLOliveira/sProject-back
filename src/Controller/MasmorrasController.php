<?php

namespace App\Controller;

use Exception;
use LogicException;
use DateTimeImmutable;
use App\Entity\Masmorra;
use App\Service\TagsService;
use App\Service\MasmorrasService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MasmorrasController extends AbstractController
{

    /**
     * @var MasmorrasService
     */
    private $masmorrasService;


    public function __construct(
        MasmorrasService $masmorrasService
    ) {
        $this->masmorrasService = $masmorrasService;
    }

    /**
     * @Route("/masmorras", name="app_masmorras_list", methods={"GET","HEAD"})
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

            $masmorras = $this->masmorrasService->listaMasmorrasUseCase($usuario, $filters, $orderBy);

            // $loadHistoricos = $request->query->get('loadHistoricos');
            // if(filter_var($loadHistoricos, FILTER_VALIDATE_BOOLEAN)) {
            //     for ($i=0; $i < count($masmorras); $i++) {
            //         // $masmorras[$i]->serializarHistoricos();
            //     }
            // }

            return new JsonResponse($masmorras);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

}