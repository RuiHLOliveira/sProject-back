<?php

namespace App\Service;

use App\Entity\Personagem;
use App\Entity\User;
use App\Enums\ClassesEspecializacoes;
use App\Enums\Habilidades;
use DateTimeImmutable;
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

            for ($i=0; $i < count($personagens); $i++) { 
                $atributos = json_decode($personagens[$i]->getAtributosjson(), true);
                $personagens[$i]->setAtributosjson(json_encode($atributos));
            }

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
            ['nome' => 'default_vidaMaxima', 'valor' => 10],
            ['nome' => 'default_vidaAtual', 'valor' => 10],
            ['nome' => 'default_ataque', 'valor' => 1],
            ['nome' => 'default_defesa', 'valor' => 1],
            ['nome' => 'default_critChance', 'valor' => 1],
            ['nome' => 'default_cura', 'valor' => 1],
            
            ['nome' => 'vidaMaxima', 'valor' => 10],
            ['nome' => 'vidaAtual', 'valor' => 10],
            ['nome' => 'ataque', 'valor' => 1],
            ['nome' => 'defesa', 'valor' => 1],
            ['nome' => 'critChance', 'valor' => 1],
            ['nome' => 'cura', 'valor' => 1],
        ];
        if($personagem->getAtributosjson() == null || $personagem->getAtributosjson() == '' || $personagem->getAtributosjson() == '{}') {
            $personagem->setAtributosjson(json_encode([]));
        }
        $dados = json_decode($personagem->getAtributosjson(), true);
        foreach ($defaults as $key => $atributoDefault) {
            $dados[$atributoDefault['nome']] = $atributoDefault['valor'];
        }

        $dados['modificadores'] = [
            Habilidades::ATRIBUTO_DANO => 0,
            Habilidades::ATRIBUTO_CRITCHANCE => 0,
            Habilidades::ATRIBUTO_VIDAMAXIMA => 0,
            Habilidades::ATRIBUTO_DEFESA => 0,
            Habilidades::ATRIBUTO_CURA => 0,
        ];
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
            $personagem = $this->processaArvores($personagem);

            $entityManager->persist($personagem);
            
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $personagem;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    public function processaArvores(Personagem $personagem) {
        $atributos = json_decode($personagem->getAtributosjson(), true);
        $modificadores = [
            Habilidades::ATRIBUTO_DANO => 0,
            Habilidades::ATRIBUTO_CRITCHANCE => 0,
            Habilidades::ATRIBUTO_VIDAMAXIMA => 0,
            Habilidades::ATRIBUTO_DEFESA => 0,
            Habilidades::ATRIBUTO_CURA => 0,
        ];

        foreach ($atributos['habilidadesPassivas'] as $key => $habilidade) {
            $modificadores[$habilidade['atributo']] += $habilidade['quantidade'] * $habilidade['porcentagem'];
        }

        $atributos['modificadores'] = $modificadores;
        $atributos['vidaMaxima'] = ( ( $modificadores[Habilidades::ATRIBUTO_VIDAMAXIMA] * $atributos['vidaMaxima'] ) / 100 ) + $atributos['vidaMaxima'];
        $atributos['ataque'] = ( ( $modificadores[Habilidades::ATRIBUTO_DANO] * $atributos['ataque'] ) / 100 ) + $atributos['ataque'];
        $atributos['defesa'] = ( ( $modificadores[Habilidades::ATRIBUTO_DEFESA] * $atributos['defesa'] ) / 100 ) + $atributos['defesa'];
        $atributos['critChance'] = ( ( $modificadores[Habilidades::ATRIBUTO_CRITCHANCE] * $atributos['critChance'] ) / 100 ) + $atributos['critChance'];
        $atributos['cura'] = ( ( $modificadores[Habilidades::ATRIBUTO_CURA] * $atributos['cura'] ) / 100 ) + $atributos['cura'];
        $atributos['vidaAtual'] = $atributos['vidaMaxima'];

        $personagem->setAtributosjson(json_encode($atributos));
        return $personagem;
    }

}