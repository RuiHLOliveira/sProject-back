<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\Dia;
use App\Entity\Note;
use App\Entity\Tarefa;
use DateTimeImmutable;
use App\Entity\Projeto;
use App\Entity\Notebook;
use App\Entity\Atividade;
use App\Entity\Historico;
use App\Service\ProjetosService;
use App\Service\HistoricosService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function exportProjetos(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            return new JsonResponse(['hello world'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
