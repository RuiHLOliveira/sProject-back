<?php

namespace App\Entity;

abstract class Recompensa
{
    const MOEDA_OURO = 'ouro';
    const MOEDA_EXPERIENCIA = 'experiencia';
    const LISTA_MOEDAS = [
        self::MOEDA_OURO,
        self::MOEDA_EXPERIENCIA,
    ];

    const ACAO_HABITO = 'habito';
    const ACAO_INBOXITEM = 'inboxitem';
    const ACAO_TAREFA = 'tarefa';
    const ACAO_PROJETO = 'projeto';
    const LISTA_ENTIDADES = [
        self::ACAO_HABITO,
        self::ACAO_INBOXITEM,
        self::ACAO_TAREFA,
        self::ACAO_PROJETO,
    ];

    const ACOESRECOMPENSAS = [
        self::ACAO_HABITO => [
            'acao' => self::ACAO_HABITO,
            'moedas' => [
                ['moeda' => self::MOEDA_OURO, 'quantidade'=> 10],
                ['moeda' => self::MOEDA_EXPERIENCIA, 'quantidade'=> 10]
            ]
        ],
        self::ACAO_INBOXITEM => [
            'acao' => self::ACAO_INBOXITEM,
            'moedas' => [
                ['moeda' => self::MOEDA_OURO, 'quantidade'=> 10],
                ['moeda' => self::MOEDA_EXPERIENCIA, 'quantidade'=> 10]
            ]
        ],
        self::ACAO_TAREFA => [
            'acao' => self::ACAO_TAREFA,
            'moedas' => [
                ['moeda' => self::MOEDA_OURO, 'quantidade'=> 50],
                ['moeda' => self::MOEDA_EXPERIENCIA, 'quantidade'=> 50]
            ]
        ],
        self::ACAO_PROJETO => [
            'acao' => self::ACAO_PROJETO,
            'moedas' => [
                ['moeda' => self::MOEDA_OURO, 'quantidade'=> 500],
                ['moeda' => self::MOEDA_EXPERIENCIA, 'quantidade'=> 500]
            ]
        ],
    ];

    public static function getTabelaNiveis(){
        $nivel1 = 1;
        $vidaMaxima = 10;
        $expProxNivel = 100;
        $nivelMax = 1000;
        $arrayNiveis = [];
        $arrayNiveis[$nivel1] = [
            'nivel' => $nivel1,
            'vidaMaxima' => $vidaMaxima,
            'expProxNivel' => $expProxNivel,
        ];

        $fatorDeVida = 10;
        $fatorDeExp = 150;

        for($i = $nivel1+1; $i < $nivelMax; $i++){
            $vidaMaxima += $fatorDeVida;
            $expProxNivel += $fatorDeExp;
            $arrayNiveis[$i] = [
                'nivel' => $i,
                'vidaMaxima' => $vidaMaxima,
                'expProxNivel' => $expProxNivel,
            ];
        }

        $vidaMaxima += $fatorDeVida;
        $expProxNivel = null;
        $arrayNiveis[$nivelMax] = [
            'nivel' => $nivelMax,
            'vidaMaxima' => $vidaMaxima,
            'expProxNivel' => $expProxNivel,
        ];
        return $arrayNiveis;
    }
}
