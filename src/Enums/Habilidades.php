<?php

namespace App\Enums;

use JsonSerializable;
use Override;

class Habilidades implements JsonSerializable {

    public $nome;
    public $descricao;
    public $tipo;
    public $atributo;
    public $porcentagem;
    public $recarga;
    public $recargaRestante;
    public $quantidade;
    public $quantidadeMaxima;
    public $duracao;
    public $imagem;

    public function jsonSerialize(): array
    {
        return [
            'nome'              => $this->nome,
            'descricao'         => $this->descricao,
            'tipo'              => $this->tipo,
            'atributo'          => $this->atributo,
            'porcentagem'       => $this->porcentagem,
            'recarga'           => $this->recarga,
            'recargaRestante'   => $this->recargaRestante,
            'duracao'           => $this->duracao,
            'quantidade'        => $this->quantidade,
            'quantidadeMaxima'  => $this->quantidadeMaxima,
            'imagem'            => $this->imagem,
        ];
    }

    public function __construct() {
        $this->recargaRestante = 0;
        $this->quantidade = 0;
        $this->quantidadeMaxima = 1;
    }

    public static function buildHabilidade($nome, $descricao, $tipo, $atributo, $porcentagem, $segundosCooldown, $duracao = null){
        return [
            'nome' => $nome,
            'descricao' => $descricao,
            'tipo' => $tipo,
            'atributo' => $atributo,
            'porcentagem' => $porcentagem,
            'recarga' => $segundosCooldown,
            'recargaRestante' => 0,
            'duracao' => $duracao,
            'quantidade' => 0,
            'quantidadeMaxima' => $tipo == self::TIPO_PASSIVO ? 20 : 1,
        ];
    }

    const TIPO_PASSIVO = 'TIPO_PASSIVO';
    const TIPO_ATIVO = 'TIPO_ATIVO';

    const ATRIBUTO_DANO = 'ATRIBUTO_DANO';
    const ATRIBUTO_DEFESA = 'ATRIBUTO_DEFESA';
    const ATRIBUTO_VIDAMAXIMA = 'ATRIBUTO_VIDAMAXIMA';
    const ATRIBUTO_CURA = 'ATRIBUTO_CURA';
    const ATRIBUTO_CRITCHANCE = 'ATRIBUTO_CRITCHANCE';

    private function normalizaQuantidadeMaxima()
    {
        if($this->tipo == self::TIPO_PASSIVO) $this->quantidadeMaxima = 20;
        if($this->tipo == self::TIPO_ATIVO) $this->quantidadeMaxima = 1;
    }

    public function setQuantidadeMaxima($quantidadeMaxima): self
    {
        $this->quantidadeMaxima = $quantidadeMaxima;
        $this->normalizaQuantidadeMaxima();
        return $this;
    }

    public function setTipo($tipo): self
    {
        $this->tipo = $tipo;
        $this->normalizaQuantidadeMaxima();
        return $this;
    }

    /**
     * Set the value of nome
     */
    public function setNome($nome): self
    {
        $this->nome = $nome;

        return $this;
    }


    /**
     * Set the value of descricao
     */
    public function setDescricao($descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }




    /**
     * Set the value of atributo
     */
    public function setAtributo($atributo): self
    {
        $this->atributo = $atributo;

        return $this;
    }


    /**
     * Set the value of porcentagem
     */
    public function setPorcentagem($porcentagem): self
    {
        $this->porcentagem = $porcentagem;

        return $this;
    }


    /**
     * Set the value of duracao
     */
    public function setDuracao($duracao): self
    {
        $this->duracao = $duracao;

        return $this;
    }


    /**
     * Set the value of recarga
     */
    public function setRecarga($recarga): self
    {
        $this->recarga = $recarga;

        return $this;
    }


    /**
     * Set the value of imagem
     */
    public function setImagem($imagem): self
    {
        $this->imagem = $imagem;

        return $this;
    }
}