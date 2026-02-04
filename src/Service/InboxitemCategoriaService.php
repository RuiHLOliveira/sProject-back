<?php

namespace App\Service;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Projeto;
use App\Entity\InboxitemCategoria;
use Facebook\WebDriver\WebDriverBy;
use Doctrine\Persistence\ManagerRegistry;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InboxitemCategoriaService
{
    
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array
     */
    public function findAll(User $usuario, array $filters = [], array $orderBy = null): array
    {
        $filters['usuario'] = $usuario;
        return $this->doctrine->getRepository(InboxitemCategoria::class)->findBy($filters, $orderBy);
    }

    /**
     * @param string $idInboxitemCategoria
     * @param User $usuario
     */
    public function find(string $idInboxitemCategoria, User $usuario): InboxitemCategoria
    {
        return $this->doctrine->getRepository(InboxitemCategoria::class)->findOneBy([
            'id' => $idInboxitemCategoria,
            'usuario' => $usuario
        ]);
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array<InboxitemCategoria>
     */
    public function listaInboxitemCategoriasUseCase(User $usuario, array $filters = [], array $orderBy = null): array
    {
        try {
            $InboxitemCategorias = $this->findAll($usuario, $filters, $orderBy);
            return $InboxitemCategorias;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * @param InboxitemCategoria $inboxitemCategoria
     * @return InboxitemCategoria
     */
    public function atualizaInboxitemCategoriaUseCase(InboxitemCategoria $inboxitemCategoria): InboxitemCategoria
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            $inboxitemCategoria->setUpdatedAt(new DateTimeImmutable());
            $entityManager->persist($inboxitemCategoria);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $inboxitemCategoria;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    /**
     * @param string $nome
     * @param User $usuario
     * @return InboxitemCategoria
     */
    public function factoryInboxitemCategoria($categoria, $usuario)
    {
        $inboxitemCategoria = new InboxitemCategoria();
        $inboxitemCategoria->setUsuario($usuario);
        $inboxitemCategoria->setCategoria($categoria);
        return $inboxitemCategoria;
    }

    public function createNewInboxitemCategoria(InboxitemCategoria $inboxitemCategoria)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            $inboxitemCategoria->setCreatedAt(new DateTimeImmutable());
            $entityManager->persist($inboxitemCategoria);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $inboxitemCategoria;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    
    public function editarUseCase(InboxitemCategoria $inboxitemCategoria, User $usuario)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            $inboxitemCategoria->setUpdatedAt(new DateTimeImmutable());
            $entityManager->persist($inboxitemCategoria);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $inboxitemCategoria;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

}