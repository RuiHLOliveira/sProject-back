<?php

namespace App\Service;

use App\Entity\Dia;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Atividade;
use App\Entity\Configuracao;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ConfiguracoesService extends BaseService
{

    public function __construct(ManagerRegistry $doctrine)
    {
        parent::__construct(Configuracao::class, $doctrine);
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array
     */
    public function findAll(User $usuario, array $orderBy = null): array
    {
        $filter = [];
        $filter['usuario'] = $usuario;
        return $this->getRepository()->findBy($filter, $orderBy);
    }

    /**
     * @param string $idConfiguracao
     * @param User $usuario
     */
    public function find(string $idConfiguracao, User $usuario): Configuracao
    {
        return $this->getRepository()->findOneBy([
            'id' => $idConfiguracao,
            'usuario' => $usuario
        ]);
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array<Configuracao>
     */
    public function listaConfiguracoesUseCase(User $usuario, array $orderBy = null): array
    {
        try {
            $configuracoes = $this->findAll($usuario, $orderBy);
            return $configuracoes;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * @param Configuracao $configuracao
     * @return Configuracao
     */
    public function atualizaConfiguracoesUseCase(Configuracao $configuracao): Configuracao
    {
        try {
            $this->beginTransaction();
            $configuracao->setUpdatedAt(new DateTimeImmutable());
            $this->entityManager->persist($configuracao);
            $this->entityManager->flush();
            $this->commit();
            return $configuracao;
        } catch (\Throwable $th) {
            $this->rollback();
            throw $th;
        }
    }

    public function verificaECriaConfiguracoesPadrao(User $usuario)
    {
        $configuracoesExistentes = $this->findAll($usuario, []);
        $configuracoesPadrao = Configuracao::getConfiguracoesPadrao();
        $configuracoesACriar = [];
        //localiza
        foreach ($configuracoesPadrao as $key => $confPadrao) {
            $array = array_filter($configuracoesExistentes, function($confExistente) use ($confPadrao) {
                return $confExistente->getChave() == $confPadrao->getChave();
            });
            if(count($array) == 0){
                $configuracoesACriar[] = $confPadrao;
            }
        }
        //cria
        foreach ($configuracoesACriar as $key => $configACriar) {
            $configuracao = $this->createNewConfiguracao(
                $this->factoryConfiguracao(
                    $configACriar->getChave(),
                    $configACriar->getValor(),
                    $usuario
                )
            );
        }
    }

    /**
     * @param string $descricao
     * @param string $valor
     * @param User $usuario
     * @return Configuracao
     */
    public function factoryConfiguracao($chave, $valor, $usuario): Configuracao
    {
        $configuracao = new Configuracao();
        $configuracao->setUsuario($usuario);
        $configuracao->setChave($chave);
        $configuracao->setValor($valor);
        $configuracao->setCreatedAt(new DateTimeImmutable());
        return $configuracao;
    }

    public function createNewConfiguracao(Configuracao $configuracao)
    {
        try {
            $this->beginTransaction();
            $this->entityManager->persist($configuracao);
            $this->entityManager->flush();
            $this->commit();
            return $configuracao;
        } catch (\Throwable $th) {
            $this->rollback();
            throw $th;
        }
    }

    
    public function editarUseCase(Configuracao $configuracao, User $usuario){
        
        try {
            $this->beginTransaction();
            $configuracao->setUpdatedAt(new DateTimeImmutable());
            $this->entityManager->persist($configuracao);
            $this->entityManager->flush();
            $this->commit();
            return $configuracao;
        } catch (\Throwable $th) {
            $this->rollback();
            throw $th;
        }
    }

}