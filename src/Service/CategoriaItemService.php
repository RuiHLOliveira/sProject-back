<?php

namespace App\Service;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Projeto;
use App\Entity\CategoriaItem;
use Facebook\WebDriver\WebDriverBy;
use Doctrine\Persistence\ManagerRegistry;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoriaItemService
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
        return $this->doctrine->getRepository(CategoriaItem::class)->findBy($filters, $orderBy);
    }

    /**
     * @param string $idCategoriaItem
     * @param User $usuario
     */
    public function find(string $idCategoriaItem, User $usuario): CategoriaItem
    {
        return $this->doctrine->getRepository(CategoriaItem::class)->findOneBy([
            'id' => $idCategoriaItem,
            'usuario' => $usuario
        ]);
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array<CategoriaItem>
     */
    public function listaCategoriaItemsUseCase(User $usuario, array $filters = [], array $orderBy = null): array
    {
        try {
            $CategoriaItems = $this->findAll($usuario, $filters, $orderBy);
            return $CategoriaItems;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * @param CategoriaItem $categoriaItem
     * @return CategoriaItem
     */
    public function atualizaCategoriaItemUseCase(CategoriaItem $categoriaItem): CategoriaItem
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            $categoriaItem->setUpdatedAt(new DateTimeImmutable());
            $entityManager->persist($categoriaItem);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $categoriaItem;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    /**
     * @param string $nome
     * @param User $usuario
     * @return CategoriaItem
     */
    public function factoryCategoriaItem($categoria, $usuario)
    {
        $categoriaItem = new CategoriaItem();
        $categoriaItem->setUsuario($usuario);
        $categoriaItem->setCategoria($categoria);
        return $categoriaItem;
    }

    public function createNewCategoriaItem(CategoriaItem $categoriaItem)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            $categoriaItem->setCreatedAt(new DateTimeImmutable());
            $entityManager->persist($categoriaItem);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $categoriaItem;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    
    public function editarUseCase(CategoriaItem $categoriaItem, User $usuario)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            $categoriaItem->setUpdatedAt(new DateTimeImmutable());
            $entityManager->persist($categoriaItem);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $categoriaItem;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

}