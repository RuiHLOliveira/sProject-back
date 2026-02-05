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

    const ACAO_TAREFA = 'tarefa';
    const ACAO_PROJETO = 'projeto';
    const ACAO_HABITO = 'habito';
    const LISTA_ENTIDADES = [
        self::ACAO_TAREFA,
        self::ACAO_PROJETO,
        self::ACAO_HABITO
    ];

    const ACOESRECOMPENSAS = [
        self::ACAO_TAREFA => [
            'acao' => self::ACAO_TAREFA,
            'moedas' => [
                ['moeda' => self::MOEDA_OURO, 'quantidade'=> 50],
                ['moeda' => self::MOEDA_EXPERIENCIA, 'quantidade'=> 50]
            ]
        ],
        self::ACAO_HABITO => [
            'acao' => self::ACAO_HABITO,
            'moedas' => [
                ['moeda' => self::MOEDA_OURO, 'quantidade'=> 10],
                ['moeda' => self::MOEDA_EXPERIENCIA, 'quantidade'=> 10]
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
}
