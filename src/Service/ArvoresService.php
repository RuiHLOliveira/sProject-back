<?php

namespace App\Service;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Historico;
use App\Enums\Arvores;
use App\Enums\Habilidades;
use App\Service\HabitosService;
use App\Service\TarefasService;
use App\Service\ProjetosService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ArvoresService
{
    private array $listaArvores;

    public function __construct() {
        $this->buildList();
    }

    public function findAll(User $usuario, array $filters = [], array $orderBy = null): array
    {
        return $this->listaArvores;
    }

    public function listaArvoresUseCase(User $usuario, array $filters = [], array $orderBy = null) : array
    {
        try {
            $arvores = $this->findAll($usuario, $filters, $orderBy);
            return $arvores;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function buildList ()
    {
        $this->listaArvores = Arvores::getArvores();
    }
}