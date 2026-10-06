<?php

namespace App\Controller;

use Exception;
use LogicException;
use DateTimeImmutable;
use App\Service\ArvoresService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArvoresController extends AbstractController
{

    /**
     * @var ArvoresService
     */
    private $arvoresService;


    public function __construct(
        ArvoresService $arvoresService
    ) {
        $this->arvoresService = $arvoresService;
    }

    /**
     * @Route("/arvores", name="app_arvores_list", methods={"GET","HEAD"})
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

            $arvores = $this->arvoresService->listaArvoresUseCase($usuario, $filters, $orderBy);

            return new JsonResponse($arvores);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

}