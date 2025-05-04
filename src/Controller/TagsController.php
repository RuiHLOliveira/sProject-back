<?php

namespace App\Controller;

use Exception;
use App\Entity\Tag;
use App\Entity\Tarefa;
use DateTimeImmutable;
use PhpParser\JsonDecoder;
use App\Service\TagsService;
use App\Service\TarefasService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TagsController extends AbstractController
{
    
    private $tagsService;

    public function __construct(TagsService $tagsService)
    {
        $this->tagsService = $tagsService;
    }

    /**
     * @Route("/tags", name="app_tags_list", methods={"GET", "HEAD"})
     */
    public function index(Request $request): Response
    {
        try {
            $usuario = $this->getUser();

            $entityList = $this->tagsService->listaTags($usuario);

            return new JsonResponse($entityList);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    
    private function validateCreateTagData($requestData) {
        if( !property_exists($requestData, 'cor') || $requestData->cor == ''){
            throw new BadRequestHttpException("Cor não enviada.");
        }
        if( !property_exists($requestData, 'descricao') || $requestData->descricao == ''){
            throw new BadRequestHttpException("Descrição não enviado.");
        }
    }

    /**
     * @Route("/tags", name="app_tags_create", methods={"POST"})
     */
    public function create(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());
            $this->validateCreateTagData($requestData);

            $tag = (new Tag())
            ->setDescricao($requestData->descricao)
            ->setCor($requestData->cor);

            $tag = $this->tagsService->create($tag, $usuario);

            return new JsonResponse($tag, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    // private function validateUpdateTarefaData($requestData) {
    //     if( !property_exists($requestData, 'descricao') || $requestData->descricao == ''){
    //         throw new BadRequestHttpException("Descrição não enviada.");
    //     }
    //     // if( !property_exists($requestData, 'hora') || $requestData->hora == ''){
    //     //     throw new BadRequestHttpException("Hora não enviada.");
    //     // }
    // }

}
