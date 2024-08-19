<?php

namespace App\Controller;

use Exception;
use DateTimeImmutable;
use App\Entity\Historico;
use PhpParser\JsonDecoder;
use App\Service\HistoricosService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class HistoricosController extends AbstractController
{
    
    private $historicosService;

    public function __construct(HistoricosService $historicosService)
    {
        $this->historicosService = $historicosService;
    }

    private function getFilters(Request $request)
    {
        $filters = [];
        $projeto = $request->query->get('projeto');

        //filtros unicos
        if($projeto != ''){
            $filters['moduloTipo'] = Historico::MODULO_TIPO_PROJETO;
            $filters['moduloId'] = $projeto;
            return $filters;
        } else {
            
        }
        return $filters;
    }
    
    private function getOrderBy(Request $request)
    {
        $orderBy = null;
        if($request->query->get('orderBy') != null){
            $orderBy = $request->query->get('orderBy');
            $orderBy = explode(',', $orderBy);
            $orderBy = [$orderBy[0] => $orderBy[1]];
        }
        return $orderBy;
    }

    /**
     * @Route("/historicos", name="app_historicos_list", methods={"GET", "HEAD"})
     */
    public function index(Request $request): Response
    {
        try {
            $usuario = $this->getUser();

            $filters = $this->getFilters($request);
            $orderBy = $this->getOrderBy($request);

            $entityList = $this->historicosService->listaHistoricosUseCase($usuario, $filters, $orderBy);

            return new JsonResponse($entityList);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    
    // private function validateCreateHistoricoData($requestData) {
    //     if( !property_exists($requestData, 'descricao') || $requestData->descricao == ''){
    //         throw new BadRequestHttpException("Descrição não enviada.");
    //     }
    //     if( !property_exists($requestData, 'projeto') || $requestData->projeto == ''){
    //         throw new BadRequestHttpException("Projeto não enviado.");
    //     }
    //     if( !property_exists($requestData, 'hora') || $requestData->hora == ''){
    //         throw new BadRequestHttpException("Hora não enviada.");
    //     }
    // }

    // /**
    //  * @Route("/historicos", name="app_historicos_create", methods={"POST"})
    //  */
    // public function create(Request $request, ManagerRegistry $doctrine): JsonResponse
    // {
    //     try {
    //         $usuario = $this->getUser();
    //         $requestData = json_decode($request->getContent());
    //         $this->validateCreateHistoricoData($requestData);

    //         $historico = $this->historicosService->factoryHistorico($requestData->descricao, $requestData->projeto, $requestData->hora, $usuario);
    //         $historico = $this->historicosService->createNewHistorico($historico);
    //         return new JsonResponse($historico, Response::HTTP_CREATED);
    //     } catch (\Exception $e) {
    //         return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }
    // }

    // private function validateUpdateHistoricoData($requestData) {
    //     if( !property_exists($requestData, 'descricao') || $requestData->descricao == ''){
    //         throw new BadRequestHttpException("Descrição não enviada.");
    //     }
    //     if( !property_exists($requestData, 'hora') || $requestData->hora == ''){
    //         throw new BadRequestHttpException("Hora não enviada.");
    //     }
    // }

    // /**
    //  * @Route("/historicos/{id}", name="app_historicos_update", methods={"PUT"})
    //  */
    // public function update($id, Request $request): JsonResponse
    // {
    //     try {
    //         $usuario = $this->getUser();
    //         $requestData = json_decode($request->getContent());
    //         $this->validateUpdateHistoricoData($requestData);

    //         $historico = $this->historicosService->find($id, $usuario);
    //         if($historico == null) {
    //             throw new NotFoundHttpException('Historico não encontrada.');
    //         }

    //         $historico->setDescricao($requestData->descricao);
    //         $historico->setHora(new DateTimeImmutable($requestData->hora));
    //         $this->historicosService->atualizaHistoricosUseCase($historico);

    //         return new JsonResponse();
            
    //     } catch (\Exception $e) {
    //         return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }
    // }

}
