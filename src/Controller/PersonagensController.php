<?php

namespace App\Controller;

use App\Entity\Personagem;
use App\Service\PersonagensService;
use App\Service\TagsService;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

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

    /**
     * @Route("/personagens/{id}/atualizarAtributos", name="app_personagens_atualizar_atributos", methods={"PUT"})
     */
    public function atualizarAtributos(Request $request, $id): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());

            $this->validateAtualizarAtributos($requestData);

            $personagem = $this->personagensService->find($usuario, $id);

            $atributosjson = $requestData->atributosjson;

            $personagem->setAtributosjson($atributosjson);

            $personagem = $this->personagensService->updatePersonagem($personagem, $usuario);

            return new JsonResponse($personagem);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    
    private function validateAtualizarAtributos($requestData) {
        if( !property_exists($requestData, 'atributosjson') || $requestData->atributosjson == ''){
            throw new BadRequestHttpException("atributosjson não enviada.");
        }
    }


}