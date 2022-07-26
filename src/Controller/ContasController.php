<?php

namespace PhpDailyManager\Controller;

use DateTimeImmutable;
use PhpDailyManager\Entity\Conta;
use PhpMiniRouter\Database\DbConnectionFactory;

class ContasController
{
    private $dbConnection;

    public function __construct($dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function index() {        
        $sql = 'select * from contas';
        $statement = $this->dbConnection->prepare($sql);

        $statement->execute();

        $dados = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $dados;
    }

    public function create($request){

        //validar
        if($request->nomeConta == null || $request->nomeConta == ''){
            throw new \Exception("Favor preencher Nome da Conta", 1);
        }

        $conta = new Conta();
        $conta->nome = $request->nomeConta;
        $conta->saldo = 0;
        $conta->createdAt = new DateTimeImmutable();

        $sql = 'insert into contas (nome,saldo,created_at) values (:nome,:saldo,:created_at)';
        $statement = $this->dbConnection->prepare($sql);

        $statement->bindValue(':nome',$conta->nome);
        $statement->bindValue(':saldo',$conta->saldo);
        $statement->bindValue(':created_at',$conta->createdAt->format('Y-m-d'));

        $statement->execute();

        $dados = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $dados;
    }
}
