<?php

namespace App\Controller;

use Exception;
use LogicException;
use DateTimeImmutable;
use App\Service\ClassesService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClassesController extends AbstractController
{

    /**
     * @var ClassesService
     */
    private $classesService;


    public function __construct(
        ClassesService $classesService
    ) {
        $this->classesService = $classesService;
    }

    /**
     * @Route("/classes", name="app_classes_list", methods={"GET","HEAD"})
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

            $classes = $this->classesService->listaClassesUseCase($usuario, $filters, $orderBy);

            return new JsonResponse($classes);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Error $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

}