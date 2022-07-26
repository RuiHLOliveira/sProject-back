<?php

namespace PhpDailyManager\Controller;

use DateTimeImmutable;
use PhpMiniRouter\Core\Validator;
use PhpDailyManager\Entity\Movimento;
use PhpMiniRouter\Database\DbConnectionFactory;

class MovimentosController
{
    
    public function index($request) {

        $pdo = DbConnectionFactory::get();

        $sql = 'select * from movimentos';

        $params = [];

        if(isset($request->conta) && $request->conta != null && $request->conta != ''){
            $sql = $sql . " where conta = :conta";
            $params['conta'] = $request->conta;
        }

        if(isset($request->orderDateDesc) && $request->orderDateDesc != null && $request->orderDateDesc == '1'){
            $sql = $sql . " order by datamovimento desc";
        }

        $statement = $pdo->prepare($sql);

        $statement->execute($params);

        $dados = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $dados;
    }

    public function create($request){

        //validar
        Validator::validate($request, 'descricao', Validator::NOT_EMPTY);
        Validator::validate($request, 'valor', Validator::NOT_EMPTY);
        Validator::validate($request, 'dataMovimento', Validator::NOT_EMPTY);
        Validator::validate($request, 'tipoMovimento', Validator::NOT_EMPTY);
        Validator::validate($request, 'contaMovimento', Validator::NOT_EMPTY);

        $movimento = new Movimento();
        $movimento->descricao = $request->descricao;
        $movimento->valor = $request->valor;
        $movimento->dataMovimento = $request->dataMovimento;
        $movimento->tipoMovimento = $request->tipoMovimento;
        $movimento->conta = $request->contaMovimento;
        $movimento->createdAt = new DateTimeImmutable();

        $pdo = DbConnectionFactory::get();

        $sql = 'insert into movimentos (descricao, valor, dataMovimento, conta, tipomovimento, created_at) values (:descricao, :valor, :dataMovimento, :conta, :tipomovimento ,:created_at)';
        $statement = $pdo->prepare($sql);

        $statement->bindValue(':descricao', $movimento->descricao);
        $statement->bindValue(':valor', $movimento->valor);
        $statement->bindValue(':dataMovimento', $movimento->dataMovimento);
        $statement->bindValue(':conta', $movimento->conta);
        $statement->bindValue(':tipomovimento', $movimento->tipoMovimento);
        $statement->bindValue(':created_at', $movimento->createdAt->format('Y-m-d'));

        $statement->execute();

        $dados = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $dados;
    }

}
