<?php

namespace App\Enums;

use App\Enums\Habilidades;

abstract class ClassesEspecializacoes {

    public static function getQuantidadePontos($nivel){
        if($nivel >= 60) return 6;
        if($nivel >= 50) return 5;
        if($nivel >= 40) return 4;
        if($nivel >= 30) return 3;
        if($nivel >= 20) return 2;
        if($nivel >= 10) return 1;
        if($nivel >= 0) return 0;
    }

    const TIPO_ARMADURA_TECIDO = ['codigo' => 'tecido', 'nome' => 'Tecido' ];
    const TIPO_ARMADURA_COURO = ['codigo' => 'couro', 'nome' => 'Couro' ];
    const TIPO_ARMADURA_MALHA = ['codigo' => 'malha', 'nome' => 'Malha' ];
    const TIPO_ARMADURA_PLACA = ['codigo' => 'placa', 'nome' => 'Placa' ];

    const LISTA_TIPOS_ARMADURAS = [
        self::TIPO_ARMADURA_TECIDO['codigo'] => self::TIPO_ARMADURA_TECIDO,
        self::TIPO_ARMADURA_COURO['codigo'] => self::TIPO_ARMADURA_COURO,
        self::TIPO_ARMADURA_MALHA['codigo'] => self::TIPO_ARMADURA_MALHA,
        self::TIPO_ARMADURA_PLACA['codigo'] => self::TIPO_ARMADURA_PLACA,
    ];

    
    private static function buildClasse($codigo, $nome, $tipoArmadura, $listaEspecializacoes) {
        return [
            'codigo' => $codigo,
            'nome' => $nome,
            'tipoArmadura' => $tipoArmadura,
            'listaEspecializacoes' => $listaEspecializacoes
        ];
    }
    private static function buildEspecializacao($codigo, $nome, $habilidadePadrao, $arvoreHabilidades) {
        return [
            'codigo' => $codigo,
            'nome' => $nome,
            'habilidadePadrao' => $habilidadePadrao,
            'arvoreHabilidades' => $arvoreHabilidades
        ];
    }
    private static function buildLinhaArvore($rankOrdem, $habilidade1, $habilidade2, $habilidade3) {
        return [
            'rankOrdem' => $rankOrdem,
            'habilidades' => [
                $habilidade1, $habilidade2, $habilidade3
            ]
        ];

    }
    private static function buildArvoreHabilidade($linha1, $linha2, $linha3, $linha4) {
        return [
            $linha1, $linha2, $linha3, $linha4
        ];
    }
    

    private static function buildGuerreiroArmas()
    {
        /**
         * Golpe Mortal / 6s cd / 215%
         * 
         * Golpe Colossal / 20s cd / 175% + 2k / 6s de 0 armadura + 30s de 4% mais dano fisico
         * Batida / 25 raiva / 275% + 1k 
         * Executar 7k (*1.25)
         * 
         * Regeneração Enfurecida - cd 1min - cura 10% + 10% apos 5sec
         * Fôlego Renovado - abaixo de 35%, regenera 3% por segundo
         * Vigilância - reduz 30% por 12 seg
         * 
         * Tornado de Aço 120% (180%) por segundo por 6 segundos - 1min rec
         * Onda de Choque - 40cd - 75% (*1.2) cd 20s caso atinge 3 ou mais
         * Rugido do Dragão - 1m cd - 126pts (151), sempre crítico e sem defesa
         * 
         * Avatar - dano 20% 24s / cd 3m
         * Banho de Sangue dano 30% 12s / cd 60s
         * Seta Tempestuosa golpe 500% / cd 30s
         */

        // golpe comum
        $golpeMortal = Habilidades::buildHabilidade('Golpe Mortal', 2, 6, Habilidades::TIPO_DANO_FISICO);
        // habilidades
        $batida = Habilidades::buildHabilidade('Batida', 3, 10, Habilidades::TIPO_DANO_FISICO);
        $executar = Habilidades::buildHabilidade('Executar', 7, 30, Habilidades::TIPO_DANO_FISICO);
        $golpeColossal = Habilidades::buildHabilidade('Golpe Colossal', 4, 20, Habilidades::TIPO_DANO_FISICO);

        $regeneracaoEnfurecida = Habilidades::buildHabilidade('Regeneração Enfurecida', 0, 10, Habilidades::TIPO_DANO_FISICO);
        $folegoRenovado = Habilidades::buildHabilidade('Fôlego Renovado', 0, 20, Habilidades::TIPO_DANO_FISICO);
        $vigilancia = Habilidades::buildHabilidade('Vigilância', 0, 10, Habilidades::TIPO_DANO_FISICO);

        $tornadoDeAco = Habilidades::buildHabilidade('Tornado de Aço', 1.2 * 6, 30, Habilidades::TIPO_DANO_FISICO);
        $ondaDeChoque = Habilidades::buildHabilidade('Onda de Choque', 4, 15, Habilidades::TIPO_DANO_FISICO);
        $rugidoDoDragao = Habilidades::buildHabilidade('Rugido do Dragão', 16, 60, Habilidades::TIPO_DANO_FISICO);

        $avatar = Habilidades::buildHabilidade('Avatar', 1.2, 120, Habilidades::TIPO_EFEITO_AUMENTO_DANO, 24);
        $banhoDeSangue = Habilidades::buildHabilidade('Banho de Sangue', 1.3, 60, Habilidades::TIPO_EFEITO_AUMENTO_DANO, 12);
        $setaTempestuosa = Habilidades::buildHabilidade('Seta Tempestuosa', 5, 30, Habilidades::TIPO_DANO_FISICO);


        $arvoreCompleta = self::buildArvoreHabilidade(
            self::buildLinhaArvore(1, $batida, $golpeColossal, $executar),
            self::buildLinhaArvore(2, $regeneracaoEnfurecida, $folegoRenovado, $vigilancia),
            self::buildLinhaArvore(3, $tornadoDeAco, $ondaDeChoque, $rugidoDoDragao),
            self::buildLinhaArvore(4, $avatar, $banhoDeSangue, $setaTempestuosa),
        );

        // especializacoes
        $armas = self::buildEspecializacao('armas', 'Armas', $golpeMortal, $arvoreCompleta);

        return $armas;
    }

    
    private static function buildGuerreiroFuria()
    {
        /**
         * Sede de sangue / 4.5s cd / 90% 1h + 1k / dupla chance critico
         * 
         * Golpe Furioso / 10 raiva / 190% 1+2h
         * Golpe Selvagem / 30 raiva / 230% 2h
         * Executar 7k (*1.25)
         * 
         * Regeneração Enfurecida - cd 1min - cura 10% + 10% apos 5sec
         * Fôlego Renovado - abaixo de 35%, regenera 3% por segundo
         * Vigilância - reduz 30% por 12 seg
         * 
         * Tornado de Aço 120% (180%) por segundo por 6 segundos - 1min rec
         * Onda de Choque - 40cd - 75% (*1.2) cd 20s caso atinge 3 ou mais
         * Rugido do Dragão - 1m cd - 126pts (151), sempre crítico e sem defesa
         * 
         * Avatar - dano 20% 24s
         * Banho de Sangue dano 30% 12s
         * Seta Tempestuosa golpe 500%
         */

        // golpe comum
        $sedeDeSangue = Habilidades::buildHabilidade('Sede de sangue', 2, 5, Habilidades::TIPO_DANO_FISICO);
        // habilidades
        $golpeFurioso = Habilidades::buildHabilidade('Golpe Furioso', 3.8, 3, Habilidades::TIPO_DANO_FISICO);
        $golpeSelvagem = Habilidades::buildHabilidade('Golpe Selvagem', 4.6, 9, Habilidades::TIPO_DANO_FISICO);
        $executar = Habilidades::buildHabilidade('Executar', 7, 30, Habilidades::TIPO_DANO_FISICO);

        $regeneracaoEnfurecida = Habilidades::buildHabilidade('Regeneração Enfurecida', 0, 10, Habilidades::TIPO_DANO_FISICO);
        $folegoRenovado = Habilidades::buildHabilidade('Fôlego Renovado', 0, 20, Habilidades::TIPO_DANO_FISICO);
        $vigilancia = Habilidades::buildHabilidade('Vigilância', 0, 10, Habilidades::TIPO_DANO_FISICO);

        $tornadoDeAco = Habilidades::buildHabilidade('Tornado de Aço', 1.2 * 6, 30, Habilidades::TIPO_DANO_FISICO);
        $ondaDeChoque = Habilidades::buildHabilidade('Onda de Choque', 4, 15, Habilidades::TIPO_DANO_FISICO);
        $rugidoDoDragao = Habilidades::buildHabilidade('Rugido do Dragão', 16, 60, Habilidades::TIPO_DANO_FISICO);

        $avatar = Habilidades::buildHabilidade('Avatar', 1.2, 120, Habilidades::TIPO_EFEITO_AUMENTO_DANO, 24);
        $banhoDeSangue = Habilidades::buildHabilidade('Banho de Sangue', 1.3, 60, Habilidades::TIPO_EFEITO_AUMENTO_DANO, 12);
        $setaTempestuosa = Habilidades::buildHabilidade('Seta Tempestuosa', 5, 30, Habilidades::TIPO_DANO_FISICO);

        $arvoreCompleta = self::buildArvoreHabilidade(
            self::buildLinhaArvore(1, $golpeFurioso, $golpeSelvagem, $executar),
            self::buildLinhaArvore(2, $regeneracaoEnfurecida, $folegoRenovado, $vigilancia),
            self::buildLinhaArvore(3, $tornadoDeAco, $ondaDeChoque, $rugidoDoDragao),
            self::buildLinhaArvore(4, $avatar, $banhoDeSangue, $setaTempestuosa),
        );

        // especializacoes
        $spec = self::buildEspecializacao('furia', 'Fúria', $sedeDeSangue, $arvoreCompleta);

        return $spec;
    }

    private static function buildGuerreiro()
    {

        $listaEspecializacoes = [
            self::buildGuerreiroArmas(),
            self::buildGuerreiroFuria(),
        ];

        return self::buildClasse('guerreiro', 'Guerreiro', self::TIPO_ARMADURA_PLACA, $listaEspecializacoes);
    }

    // private static function buildPaladino()
    // {
    //     return self::buildClasse('paladino', 'Paladino', self::TIPO_ARMADURA_PLACA);
    // }

    public static function getClasses()
    {
        $classes = [
            self::buildGuerreiro()
        ];
        return $classes;
    }

}