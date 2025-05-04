<?php

namespace App\Service;

use DateTime;
use DateInterval;
use App\Entity\Tag;
use LogicException;
use App\Entity\User;
use App\Entity\Tarefa;
use DateTimeImmutable;
use App\Entity\Projeto;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TagsService
{
    
    private $doctrine;
    private $encoder;

    public function __construct(ManagerRegistry $doctrine,  UserPasswordEncoderInterface $encoder)
    {
        $this->doctrine = $doctrine;
        $this->encoder = $encoder;
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array
     */
    public function findAll(User $usuario, array $filters = [], array $orderBy = null): array
    {
        $filters['usuario'] = $usuario;
        return $this->doctrine->getRepository(Tag::class)->findBy($filters, $orderBy);
    }

    /**
     * @param string $id
     * @param User $usuario
     */
    public function find(string $id, User $usuario): Tag
    {
        $entity = $this->doctrine->getRepository(Tag::class)->findOneBy([
            'id' => $id,
            'usuario' => $usuario
        ]);
        return $entity;
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array<Tag>
     */
    public function listaTags(User $usuario, array $filters = [], array $orderBy = null): array
    {
        try {
            $tag = $this->findAll($usuario, $filters, $orderBy);
            return $tag;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * @param Tag $entity
     * @return Tag
     */
    public function update(Tag $entity): Tag
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $entity->setUpdatedAt(new DateTimeImmutable());

            $entityManager->persist($entity);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $entity;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    /**
     * @param Tag $entity
     * @param User $usuario
     * @return Tag
     */
    public function delete(Tag $entity, User $usuario)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $entityManager->remove($entity);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $entity;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function create(Tag $entity, $usuario)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $entity->setCreatedAt(new DateTimeImmutable());
            $entity->setUsuario($usuario);

            $entityManager->persist($entity);
            $entityManager->flush();

            $entityManager->getConnection()->commit();

            return $entity;

        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }


}