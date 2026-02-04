<?php

namespace App\Controller;

use Exception;
use DateTimeImmutable;
use App\Entity\Personagem;
use App\Service\PersonagensService;
use App\Service\TagsService;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PersonagensController extends AbstractController
{

    /**
     * @var PersonagensService
     */
    private $personagensService;


    public function __construct(
        PersonagensService $personagensService
    ) {
        $this->personagensService = $personagensService;
    }

    /**
     * @Route("/personagens", name="app_personagens_list", methods={"GET","HEAD"})
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

            $personagens = $this->personagensService->listaPersonagensUseCase($usuario, $filters, $orderBy);

            $loadHistoricos = $request->query->get('loadHistoricos');
            if(filter_var($loadHistoricos, FILTER_VALIDATE_BOOLEAN)) {
                for ($i=0; $i < count($personagens); $i++) {
                    // $personagens[$i]->serializarHistoricos();
                }
            }

            return new JsonResponse($personagens);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

}