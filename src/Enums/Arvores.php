<?php

namespace App\Enums;

use App\Enums\Habilidades;

abstract class Arvores {
    
    private static function buildEspecializacao($codigo, $nome, $habilidadePadrao, $arvoreHabilidades) {
        return [
            'codigo' => $codigo,
            'nome' => $nome,
            'habilidadePadrao' => $habilidadePadrao,
            'arvoreHabilidades' => $arvoreHabilidades
        ];
    }

    private static function buildLinhaArvore($rankOrdem, $habilidade1) {
        return [
            'rankOrdem' => $rankOrdem,
            'habilidade' => $habilidade1
        ];

    }
    

    // private static function buildGuerreiroArmas()
    // {

    //     /**
    //      * Golpe Mortal / 6s cd / 215%
    //      * 
    //      * Golpe Colossal / 20s cd / 175% + 2k / 6s de 0 armadura + 30s de 4% mais dano fisico
    //      * Batida / 25 raiva / 275% + 1k 
    //      * Executar 7k (*1.25)
    //      * 
    //      * Regeneração Enfurecida - cd 1min - cura 10% + 10% apos 5sec
    //      * Fôlego Renovado - abaixo de 35%, regenera 3% por segundo
    //      * Vigilância - reduz 30% por 12 seg
    //      * 
    //      * Tornado de Aço 120% (180%) por segundo por 6 segundos - 1min rec
    //      * Onda de Choque - 40cd - 75% (*1.2) cd 20s caso atinge 3 ou mais
    //      * Rugido do Dragão - 1m cd - 126pts (151), sempre crítico e sem defesa
    //      * 
    //      * Avatar - dano 20% 24s / cd 3m
    //      * Banho de Sangue dano 30% 12s / cd 60s
    //      * Seta Tempestuosa golpe 500% / cd 30s
    //      */

    //     // golpe comum
    //     $golpeMortal = Habilidades::buildHabilidade(
    //         'Golpe Mortal', //nome
    //         2, //multiplicador
    //         6, //cooldown
    //         Habilidades::TIPO_ATIVO, //tipo
    //         null //duracao
    //     );
    //     // habilidades
    //     $batida = Habilidades::buildHabilidade('Batida', 3, 10, Habilidades::TIPO_ATIVO);
    //     $executar = Habilidades::buildHabilidade('Executar', 7, 30, Habilidades::TIPO_ATIVO);
    //     $golpeColossal = Habilidades::buildHabilidade('Golpe Colossal', 4, 20, Habilidades::TIPO_ATIVO);

    //     $regeneracaoEnfurecida = Habilidades::buildHabilidade('Regeneração Enfurecida', 0, 10, Habilidades::TIPO_ATIVO);
    //     $folegoRenovado = Habilidades::buildHabilidade('Fôlego Renovado', 0, 20, Habilidades::TIPO_ATIVO);
    //     $vigilancia = Habilidades::buildHabilidade('Vigilância', 0, 10, Habilidades::TIPO_ATIVO);

    //     $tornadoDeAco = Habilidades::buildHabilidade('Tornado de Aço', 1.2 * 6, 30, Habilidades::TIPO_ATIVO);
    //     $ondaDeChoque = Habilidades::buildHabilidade('Onda de Choque', 4, 15, Habilidades::TIPO_ATIVO);
    //     $rugidoDoDragao = Habilidades::buildHabilidade('Rugido do Dragão', 16, 60, Habilidades::TIPO_ATIVO);

    //     $avatar = Habilidades::buildHabilidade('Avatar', 1.2, 120, Habilidades::TIPO_EFEITO_AUMENTO_DANO, 24);
    //     $banhoDeSangue = Habilidades::buildHabilidade('Banho de Sangue', 1.3, 60, Habilidades::TIPO_EFEITO_AUMENTO_DANO, 12);
    //     $setaTempestuosa = Habilidades::buildHabilidade('Seta Tempestuosa', 5, 30, Habilidades::TIPO_ATIVO);


    //     $arvoreCompleta = self::buildArvoreHabilidade(
    //         self::buildLinhaArvore(1, $batida, $golpeColossal, $executar),
    //         self::buildLinhaArvore(2, $regeneracaoEnfurecida, $folegoRenovado, $vigilancia),
    //         self::buildLinhaArvore(3, $tornadoDeAco, $ondaDeChoque, $rugidoDoDragao),
    //         self::buildLinhaArvore(4, $avatar, $banhoDeSangue, $setaTempestuosa),
    //     );

    //     // especializacoes
    //     $armas = self::buildEspecializacao('armas', 'Armas', $golpeMortal, $arvoreCompleta);

    //     return $armas;
    // }

    
    // private static function buildGuerreiroFuria()
    // {
    //     /**
    //      * Sede de sangue / 4.5s cd / 90% 1h + 1k / dupla chance critico
    //      * 
    //      * Golpe Furioso / 10 raiva / 190% 1+2h
    //      * Golpe Selvagem / 30 raiva / 230% 2h
    //      * Executar 7k (*1.25)
    //      * 
    //      * Regeneração Enfurecida - cd 1min - cura 10% + 10% apos 5sec
    //      * Fôlego Renovado - abaixo de 35%, regenera 3% por segundo
    //      * Vigilância - reduz 30% por 12 seg
    //      * 
    //      * Tornado de Aço 120% (180%) por segundo por 6 segundos - 1min rec
    //      * Onda de Choque - 40cd - 75% (*1.2) cd 20s caso atinge 3 ou mais
    //      * Rugido do Dragão - 1m cd - 126pts (151), sempre crítico e sem defesa
    //      * 
    //      * Avatar - dano 20% 24s
    //      * Banho de Sangue dano 30% 12s
    //      * Seta Tempestuosa golpe 500%
    //      */

    //     // golpe comum
    //     $sedeDeSangue = Habilidades::buildHabilidade('Sede de sangue', 2, 5, Habilidades::TIPO_ATIVO);
    //     // habilidades
    //     $golpeFurioso = Habilidades::buildHabilidade('Golpe Furioso', 3.8, 3, Habilidades::TIPO_ATIVO);
    //     $golpeSelvagem = Habilidades::buildHabilidade('Golpe Selvagem', 4.6, 9, Habilidades::TIPO_ATIVO);
    //     $executar = Habilidades::buildHabilidade('Executar', 7, 30, Habilidades::TIPO_ATIVO);

    //     $regeneracaoEnfurecida = Habilidades::buildHabilidade('Regeneração Enfurecida', 0, 10, Habilidades::TIPO_ATIVO);
    //     $folegoRenovado = Habilidades::buildHabilidade('Fôlego Renovado', 0, 20, Habilidades::TIPO_ATIVO);
    //     $vigilancia = Habilidades::buildHabilidade('Vigilância', 0, 10, Habilidades::TIPO_ATIVO);

    //     $tornadoDeAco = Habilidades::buildHabilidade('Tornado de Aço', 1.2 * 6, 30, Habilidades::TIPO_ATIVO);
    //     $ondaDeChoque = Habilidades::buildHabilidade('Onda de Choque', 4, 15, Habilidades::TIPO_ATIVO);
    //     $rugidoDoDragao = Habilidades::buildHabilidade('Rugido do Dragão', 16, 60, Habilidades::TIPO_ATIVO);

    //     $avatar = Habilidades::buildHabilidade('Avatar', 1.2, 120, Habilidades::TIPO_EFEITO_AUMENTO_DANO, 24);
    //     $banhoDeSangue = Habilidades::buildHabilidade('Banho de Sangue', 1.3, 60, Habilidades::TIPO_EFEITO_AUMENTO_DANO, 12);
    //     $setaTempestuosa = Habilidades::buildHabilidade('Seta Tempestuosa', 5, 30, Habilidades::TIPO_ATIVO);

    //     $arvoreCompleta = self::buildArvoreHabilidade(
    //         self::buildLinhaArvore(1, $golpeFurioso, $golpeSelvagem, $executar),
    //         self::buildLinhaArvore(2, $regeneracaoEnfurecida, $folegoRenovado, $vigilancia),
    //         self::buildLinhaArvore(3, $tornadoDeAco, $ondaDeChoque, $rugidoDoDragao),
    //         self::buildLinhaArvore(4, $avatar, $banhoDeSangue, $setaTempestuosa),
    //     );

    //     // especializacoes
    //     $spec = self::buildEspecializacao('furia', 'Fúria', $sedeDeSangue, $arvoreCompleta);

    //     return $spec;
    // }

    
    public static function buildArvoreFisica(){
        
        $golpeMortal = (new Habilidades())
        ->setNome('Golpe Mortal')
        ->setDescricao('Golpeia com um ataque direto')
        ->setTipo(Habilidades::TIPO_ATIVO)
        ->setAtributo(null)
        ->setPorcentagem(520)
        ->setRecarga(6)
        ->setDuracao(null)
        ->setImagem('fisico/golpemortal.jpg');
        

        $posturaBerserker = (new Habilidades())
        ->setNome('Postura Berserker')
        ->setDescricao('Aumenta o dano total em 1%.')
        ->setTipo(Habilidades::TIPO_PASSIVO)
        ->setAtributo(Habilidades::ATRIBUTO_DANO)
        ->setPorcentagem(1)
        ->setRecarga(0)
        ->setDuracao(null)
        ->setImagem('fisico/posturaberserker.jpg');

        $trovoada = (new Habilidades())
        ->setNome('Trovoada')
        ->setDescricao('Golpeia com um baque no chão')
        ->setTipo(Habilidades::TIPO_ATIVO)
        ->setAtributo(null)
        ->setPorcentagem(235)
        ->setRecarga(6)
        ->setDuracao(null)
        ->setImagem('fisico/trovoada.jpg');


        $arremessoAvassalador = (new Habilidades())
        ->setNome('Arremesso Avassalador de Arma')
        ->setDescricao('Arremessa a arma causando grande dano')
        ->setTipo(Habilidades::TIPO_ATIVO)
        ->setAtributo(null)
        ->setPorcentagem(600)
        ->setRecarga(45)
        ->setDuracao(null)
        ->setImagem('fisico/arremessoavassalador.jpg');

        $treinamentoBarbaro = (new Habilidades())
        ->setNome('Treinamento Bárbaro')
        ->setDescricao('Aumenta a cance de acerto crítico em 1%.')
        ->setTipo(Habilidades::TIPO_PASSIVO)
        ->setAtributo(Habilidades::ATRIBUTO_CRITCHANCE)
        ->setPorcentagem(1)
        ->setRecarga(6)
        ->setDuracao(null)
        ->setImagem('fisico/treinamentobarbaro.jpg');


        $lancaDoCampeao = (new Habilidades())
        ->setNome('Lança do Campeão')
        ->setDescricao('Golpeia com a arma em estocadas, causando grande dano.')
        ->setTipo(Habilidades::TIPO_ATIVO)
        ->setAtributo(null)
        ->setPorcentagem(239+326)
        ->setRecarga(90)
        ->setDuracao(null)
        ->setImagem('fisico/lancadocampeao.jpg');


        $proficienciaEmPosturaBerserker = (new Habilidades())
        ->setNome('Proficiência em Postura Berserke')
        ->setDescricao('Aumenta o dano em 3%.')
        ->setTipo(Habilidades::TIPO_PASSIVO)
        ->setAtributo(Habilidades::ATRIBUTO_DANO)
        ->setPorcentagem(3)
        ->setRecarga(0)
        ->setDuracao(null)
        ->setImagem('fisico/proficienciaemposutra.jpg');
        
        $arvoreCompleta = [
            self::buildLinhaArvore(1, $golpeMortal),
            self::buildLinhaArvore(1, $posturaBerserker),
            self::buildLinhaArvore(1, $trovoada),
            self::buildLinhaArvore(1, $arremessoAvassalador),
            self::buildLinhaArvore(1, $treinamentoBarbaro),
            self::buildLinhaArvore(1, $lancaDoCampeao),
            self::buildLinhaArvore(1, $proficienciaEmPosturaBerserker),
        ];

        // especializacoes
        $fisica = self::buildEspecializacao(
            'fisica', 'fisica',
            null,
            $arvoreCompleta
        );

        return $fisica;

    }

    public static function buildArvoreDefensiva(){

        $posturaDefensiva = (new Habilidades())
        ->setNome('Postura Defensiva')
        ->setDescricao('Diminui o dano recebido em 1%.')
        ->setTipo(Habilidades::TIPO_PASSIVO)
        ->setAtributo(Habilidades::ATRIBUTO_DEFESA)
        ->setPorcentagem(1)
        ->setRecarga(0)
        ->setDuracao(null)
        ->setImagem('fisico/posturadefensiva.jpg');

    
        $placasReforcadas = (new Habilidades())
        ->setNome('Placas Reforçadas')
        ->setDescricao('Aumenta a armadura em 1%')
        ->setTipo(Habilidades::TIPO_PASSIVO)
        ->setAtributo(Habilidades::ATRIBUTO_DEFESA)
        ->setPorcentagem(1)
        ->setRecarga(0)
        ->setDuracao(null)
        ->setImagem('fisico/placasreforcadas.jpg');

    
        $determinacao = (new Habilidades())
        ->setNome('Determinação')
        ->setDescricao('Aumenta a vida em 1%')
        ->setTipo(Habilidades::TIPO_PASSIVO)
        ->setAtributo(Habilidades::ATRIBUTO_VIDAMAXIMA)
        ->setPorcentagem(1)
        ->setRecarga(0)
        ->setDuracao(null)
        ->setImagem('fisico/determinacao.jpg');

    
        $proficienciaEmPosturaDefensiva = (new Habilidades())
        ->setNome('Proficiência em Postura Defensiva')
        ->setDescricao('Diminui o dano recebido em 3%.')
        ->setTipo(Habilidades::TIPO_PASSIVO)
        ->setAtributo(Habilidades::ATRIBUTO_DEFESA)
        ->setPorcentagem(3)
        ->setRecarga(0)
        ->setDuracao(null)
        ->setImagem('fisico/proficienciaemposutra.jpg');
        
        $arvoreCompleta = [
            self::buildLinhaArvore(1, $posturaDefensiva),
            self::buildLinhaArvore(1, $placasReforcadas),
            self::buildLinhaArvore(1, $determinacao),
            self::buildLinhaArvore(1, $proficienciaEmPosturaDefensiva),
        ];

        // especializacoes
        $fisica = self::buildEspecializacao(
            'defensiva',
            'Defensiva',
            null,
            $arvoreCompleta
        );

        return $fisica;

    }

    public static function getArvores()
    {
        $arvoreFisica = self::buildArvoreFisica();
        $arvoreDefensiva = self::buildArvoreDefensiva();

        $arvoresParalelas = [
            $arvoreFisica,
            $arvoreDefensiva,
        ];

        return $arvoresParalelas;
    }

}