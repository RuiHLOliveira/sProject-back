<?php

namespace App\Controller;

use App\Service\DiasService;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DiasController extends AbstractController
{

    /**
     * @var DiasService
     */
    private $diasService;

    public function __construct(DiasService $diasService)
    {
        $this->diasService = $diasService;
    }

    /**
     * @Route("/dias", name="app_dias_list", methods={"GET","HEAD"})
     */
    public function listaDias(Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();

            $orderBy = null;
            if($request->query->get('orderBy') != null){
                $orderBy = $request->query->get('orderBy');
                $orderBy = explode(',', $orderBy);
                $orderBy = [$orderBy[0] => $orderBy[1]];
            }

            $dias = $this->diasService->listaDiasUseCase($usuario, $orderBy);

            return new JsonResponse($dias);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/dias", name="app_dias_create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $requestObj = json_decode($request->getContent());

            $usuario = $this->getUser();

            $dataCompleta = new DateTimeImmutable($requestObj->dataCompleta);

            $dia = $this->diasService->createNewDiaFromDataCompleta($dataCompleta, $usuario);

            return new JsonResponse($dia, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}