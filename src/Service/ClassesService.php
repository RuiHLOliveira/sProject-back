<?php

namespace App\Service;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Historico;
use App\Enums\ClassesEspecializacoes;
use App\Enums\Habilidades;
use App\Service\HabitosService;
use App\Service\TarefasService;
use App\Service\ProjetosService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ClassesService
{
    private array $listaClasses;

    public function __construct() {
        $this->buildList();
    }

    public function findAll(User $usuario, array $filters = [], array $orderBy = null): array
    {
        return $this->listaClasses;
    }

    public function listaClassesUseCase(User $usuario, array $filters = [], array $orderBy = null) : array
    {
        try {
            $classes = $this->findAll($usuario, $filters, $orderBy);
            return $classes;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function buildList ()
    {
        $this->listaClasses = ClassesEspecializacoes::getClasses();
    }
}