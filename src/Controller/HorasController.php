<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HorasController extends AbstractController
{
    /**
     * @Route("/horas", name="app_horas")
     */
    public function index(): Response
    {
        return $this->render('horas/index.html.twig', [
            'controller_name' => 'HorasController',
        ]);
    }
}
