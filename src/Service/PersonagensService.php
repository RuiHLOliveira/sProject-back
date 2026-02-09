<?php

namespace App\Service;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Personagem;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;

class PersonagensService
{
    private ManagerRegistry $doctrine;

    public function __construct(
        ManagerRegistry $doctrine
    ) {
        $this->doctrine = $doctrine;
    }

    private function getRepository() : ObjectRepository {
        return $this->doctrine->getRepository(Personagem::class);
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array
     */
    public function findAll(User $usuario, array $filter = [], array $orderBy = []): array
    {
        $filter['usuario'] = $usuario;
        return $this->getRepository()->findBy($filter, $orderBy);
    }

    /**
     * @param User $usuario
     * @param integer $id
     * @return Personagem
     */
    public function find(User $usuario, $id): Personagem
    {
        $criteria['usuario'] = $usuario;
        $criteria['id'] = $id;
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * @param User $usuario
     * @param array $filters
     * @param array $orderBy
     * @return array<Personagem>
     */
    public function listaPersonagensUseCase(User $usuario, array $filters = [], array $orderBy = []) : array
    {
        try {
            $personagens = $this->findAll($usuario, $filters, $orderBy);
            return $personagens;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function factoryCreatePersonagemUsecase(User $usuario) : Personagem
    {
        $email = $usuario->getEmail();
        $nome = explode('.com',$email)[0];
        $personagem = new Personagem();
        $personagem->setNome($nome);
        $personagem->setNivel(1);
        $personagem->setExperiencia(0);
        $personagem->setOuro(0);
        $personagem = $this->setDefaultStatusJson($personagem);
        return $personagem;
    }

    private function setDefaultStatusJson(Personagem $personagem)
    {
        $defaults = [
            ['nome' => 'vidaMaxima', 'valor' => 10],
            ['nome' => 'vidaAtual', 'valor' => 10],
            ['nome' => 'ataque', 'valor' => 1],
            ['nome' => 'defesa', 'valor' => 1]
        ];
        if($personagem->getAtributosjson() == null || $personagem->getAtributosjson() == '' || $personagem->getAtributosjson() == '{}') {
            $personagem->setAtributosjson(json_encode([]));
        }
        $dados = json_decode($personagem->getAtributosjson(), true);
        foreach ($defaults as $key => $atributoDefault) {
            if(!isset($dados[$atributoDefault['nome']])) {
                $dados[$atributoDefault['nome']] = $atributoDefault['valor'];
            }
        }
        $personagem->setAtributosjson(json_encode($dados));
        return $personagem;
    }

    public function createPersonagemUseCase(User $usuario) : Personagem
    {
        //busca para ver se existe
        $personagens = $this->findAll($usuario);
        if(count($personagens) > 0) return $personagens[0];

        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $personagem = $this->factoryCreatePersonagemUsecase($usuario);
            $this->baseCreate($personagem, $usuario);

            $entityManager->getConnection()->commit();
            return $personagem;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function baseCreate(Personagem $personagem, User $usuario) : Personagem
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            
            $personagem->setCreatedAt(new DateTimeImmutable());
            $personagem->setUsuario($usuario);

            $entityManager->persist($personagem);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $personagem;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function updatePersonagem(Personagem $personagem, User $usuario) : Personagem
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();
            
            $personagem->setUpdatedAt(new DateTimeImmutable());
            $personagem = $this->setDefaultStatusJson($personagem);

            $entityManager->persist($personagem);
            
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $personagem;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

}