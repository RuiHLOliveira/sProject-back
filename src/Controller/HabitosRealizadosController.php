<?php

namespace App\Controller;

use App\Entity\Habito;
use App\Service\HabitosRealizadosService;
use App\Service\HabitosService;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use PhpParser\JsonDecoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class HabitosRealizadosController extends AbstractController
{
    
    private HabitosRealizadosService $habitosRealizadosService;

    public function __construct(HabitosRealizadosService $habitosRealizadosService)
    {
        $this->habitosRealizadosService = $habitosRealizadosService;
    }

    
    /**
     * @Route("/habitos-realizados/{id}/registra-avaliacao", name="app_habitosrealizados_registra_avaliacao", methods={"POST"})
     */
    public function registraAvaliacao($id, Request $request): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = json_decode($request->getContent());
            $tarefa = $this->validateHabitoRealizadoExiste($id, $usuario);
            $tarefa->setAvaliacaoJson($requestData->avaliacaoJson);
            $this->habitosRealizadosService->registraAvaliacaoUseCase($tarefa);
            return new JsonResponse();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    
    private function validateHabitoRealizadoExiste($id, $usuario)
    {
        $tarefa = $this->habitosRealizadosService->find($id, $usuario);
        if($tarefa == null) {
            throw new NotFoundHttpException('Habito Realizado não encontrada.');
        }
        return $tarefa;
    }

}
