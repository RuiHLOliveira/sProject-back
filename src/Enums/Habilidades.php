<?php

namespace App\Enums;

abstract class Habilidades {

    public static function buildHabilidade($nome, $multiplicador, $segundosCooldown, $tipo, $duracao = null){
        return [
            'nome' => $nome,
            'dano' => $multiplicador,
            'multiplicador' => $multiplicador,
            'tipo' => $tipo,
            'recarga' => $segundosCooldown,
            'recargaRestante' => 0,
            'duracao' => $duracao,
        ];
    }

    const TIPO_EFEITO_AUMENTO_DANO = 'TIPO_EFEITO_AUMENTO_DANO';

    const TIPO_DANO_FISICO = 'TIPO_DANO_FISICO';
    const TIPO_DANO_SAGRADO = 'TIPO_DANO_SAGRADO';
    const TIPO_DANO_FOGO = 'TIPO_DANO_FOGO';
    const TIPO_DANO_GELO = 'TIPO_DANO_GELO';

}