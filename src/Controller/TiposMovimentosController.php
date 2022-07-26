<?php

namespace PhpDailyManager\Controller;

use DateTimeImmutable;
use PhpDailyManager\Entity\TipoMovimento;
use PhpMiniRouter\Database\DbConnectionFactory;

class TiposMovimentosController
{
    public function index() {
        
        $pdo = DbConnectionFactory::get();

        $sql = 'select * from tiposmovimentos';
        $statement = $pdo->prepare($sql);

        $statement->execute();

        $dados = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $dados;
    }
    
    public function create($request){

        //validar
        if($request->nomeTipoMovimento == null || $request->nomeTipoMovimento == ''){
            throw new \Exception("Favor preencher Nome do Tipo Movimento", 1);
        }

        $tipoMovimento = new TipoMovimento();
        $tipoMovimento->nome = $request->nomeTipoMovimento;
        $tipoMovimento->createdAt = new DateTimeImmutable();

        $pdo = DbConnectionFactory::get();

        $sql = 'insert into tiposmovimentos (nome,created_at) values (:nome,:created_at)';
        $statement = $pdo->prepare($sql);

        $statement->bindValue(':nome', $tipoMovimento->nome);
        $statement->bindValue(':created_at', $tipoMovimento->createdAt->format('Y-m-d'));

        $statement->execute();

        $dados = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $dados;
    }
}
