<?php

namespace App\Service;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Historico;
use App\Enums\Habilidades;
use App\Service\HabitosService;
use App\Service\TarefasService;
use App\Service\ProjetosService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class MasmorrasService
{
    private ManagerRegistry $doctrine;
    private array $listaMasmorras;

    public function __construct(
        ManagerRegistry $doctrine
    ) {
        $this->doctrine = $doctrine;
        $this->buildList();
    }

    public function findAll(User $usuario, array $filters = [], array $orderBy = null): array
    {
        return $this->listaMasmorras;
    }

    public function listaMasmorrasUseCase(User $usuario, array $filters = [], array $orderBy = null) : array
    {
        try {
            $masmorras = $this->findAll($usuario, $filters, $orderBy);
            return $masmorras;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function buildMasmorra($imagem, $nome, $descricao, $ordem, $chefoes) {
        return [
            'imagem' => $imagem,
            'nome' => $nome,
            'descricao' => $descricao,
            'ordem' => $ordem,
            'chefoes' => $chefoes
        ];
    }

    private function buildChefao($ordem, $imagem, $nome, $descricao, $pontosVida, $ataque, $defesa, $habilidades) {
        return [
            'imagem' => $imagem,
            'nome' => $nome,
            'descricao' => $descricao,
            'pontosVida' => $pontosVida,
            'ordem' => $ordem,
            'ataque' => $ataque,
            'defesa' => $defesa,
            'habilidades' => $habilidades,
        ];
    }

    private function get1MinasMortas1Falagrum() {
        // a ordem importa
        $habilidades = [

            (new Habilidades())
            ->setNome('Soco')
            ->setDescricao('-')
            ->setTipo(Habilidades::TIPO_ATIVO)
            ->setAtributo(null)
            ->setPorcentagem(200)
            ->setRecarga(3),

            
            (new Habilidades())
            ->setNome('Punhos de Fogo')
            ->setDescricao('-')
            ->setTipo(Habilidades::TIPO_ATIVO)
            ->setAtributo(null)
            ->setPorcentagem(600)
            ->setRecarga(10),

            
            (new Habilidades())
            ->setNome('Punhos de Gelo')
            ->setDescricao('-')
            ->setTipo(Habilidades::TIPO_ATIVO)
            ->setAtributo(null)
            ->setPorcentagem(600)
            ->setRecarga(10),

            // $this->buildHabilidade('Lampejo'), // implementar efeitos adicionais
            // implementar icones
        ];

        $imagem = '1minasmortas1falagrum.png';
        $falagrumNome = 'Falagrum, O Capataz';
        $falagrumDescricao = 'Em um acesso de raiva, Falagrum libertou as próprias habilidades mágicas e reduziu a cinzas a própria gruta. Ao tomar conhecimento de tamanho talento destrutivo, os Défias contrataram o gigantesco ogro-mago como capataz das Minas Mortas.';
        $pontosVida = 50;
        $ataque = 1;
        $defesa = 1;
        $ordemNaMasmorra = 1;
        $falagrum = $this->buildChefao($ordemNaMasmorra, $imagem, $falagrumNome, $falagrumDescricao, $pontosVida, $ataque, $defesa, $habilidades);
        return $falagrum;
    }

    private function get1MinasMortas2HelixQuebracranio() {
        // a ordem importa
        $habilidades = [
            
            (new Habilidades())
            ->setNome('Lançar Helix')
            ->setDescricao('-')
            ->setTipo(Habilidades::TIPO_ATIVO)
            ->setAtributo(null)
            ->setPorcentagem(200)
            ->setRecarga(5),

            
            (new Habilidades())
            ->setNome('Bomba Grudenta')
            ->setDescricao('-')
            ->setTipo(Habilidades::TIPO_ATIVO)
            ->setAtributo(null)
            ->setPorcentagem(500)
            ->setRecarga(6),

            
            (new Habilidades())
            ->setNome('Parvo Esmaga')
            ->setDescricao('-')
            ->setTipo(Habilidades::TIPO_ATIVO)
            ->setAtributo(null)
            ->setPorcentagem(1000)
            ->setRecarga(20),
            // implementar icones
        ];
        $imagem = '1minasmortas2helixquebracranio.png';
        $falagrumNome = 'Helix Quebracâmbio';
        $falagrumDescricao = 'Ex-engenheiro do Cartel Borraquilha, Helix recebeu da Irmandade Défias uma proposta de trabalho envolvendo uma quantia muito maior do que jamais poderia receber como um anônimo artífice da Horda. Helix aceitou a oferta sem pestanejar, renunciando às antigas alianças... como qualquer goblin talentoso faria.';
        $pontosVida = 55;
        $ataque = 2;
        $defesa = 2;
        $ordemNaMasmorra = 2;
        $falagrum = $this->buildChefao($ordemNaMasmorra, $imagem, $falagrumNome, $falagrumDescricao, $pontosVida, $ataque, $defesa, $habilidades);
        return $falagrum;
    }
    
    private function get1MinasMortas3CeifadorDeInimigos5000 () {
        // a ordem importa
        $habilidades = [
            
            (new Habilidades())
            ->setNome('Golpe do Ceifador')
            ->setDescricao('-')
            ->setTipo(Habilidades::TIPO_ATIVO)
            ->setAtributo(null)
            ->setPorcentagem(200)
            ->setRecarga(3),
            
            (new Habilidades())
            ->setNome('Colher')
            ->setDescricao('-')
            ->setTipo(Habilidades::TIPO_ATIVO)
            ->setAtributo(null)
            ->setPorcentagem(400)
            ->setRecarga(10),
            
            (new Habilidades())
            ->setNome('Colheita Descontrolada')
            ->setDescricao('-')
            ->setTipo(Habilidades::TIPO_ATIVO)
            ->setAtributo(null)
            ->setPorcentagem(600)
            ->setRecarga(15),
            // implementar icones
        ];
        $imagem = '1minasmortas3ceifadordeinimigos5000.png';
        $falagrumNome = 'Ceifador de Inimigos 5000';
        $falagrumDescricao = 'Os engenheiros Défias dedicam-se há muitos e longos anos ao aperfeiçoamento de um protótipo de ceifador baseado no Ceifador de Inimigos 4000. Quando o novo modelo estiver pronto, a irmandade acredita que este terror mecanizado passará sobre os soldados de Ventobravo como uma foice ceifando o trigo.';
        $pontosVida = 60;
        $ataque = 3;
        $defesa = 3;
        $ordemNaMasmorra = 3;
        $falagrum = $this->buildChefao($ordemNaMasmorra, $imagem, $falagrumNome, $falagrumDescricao, $pontosVida, $ataque, $defesa, $habilidades);
        return $falagrum;
    }

    private function get1MinasMortas()
    {
        $imagemMasmorra = '1minasmortas.png';
        $nome = 'Minas Mortas';
        $descricao = 'Deep beneath the mines of Moonbrook in southwestern Westfall lie the Deadmines (VC). Despite the demise of the Defias Brotherhood\'s leader Edwin VanCleef at the hands of Alliance militiamen, the Deadmines is still the Brotherhood\'s most secure hideout since Cataclysm. Here the survivors of Edwin\'s crew toil alongside new recruits, so that the Defias juggernaut ship can be complete and the kingdom of Stormwind can be brought to its knees. All this is happening under the vigilant eyes of "Captain" Cookie... and Vanessa VanCleef.';
        $chefoes = [
            $this->get1MinasMortas1Falagrum(),
            $this->get1MinasMortas2HelixQuebracranio(),
            $this->get1MinasMortas3CeifadorDeInimigos5000(),
        ];
        $minasMortas = $this->buildMasmorra($imagemMasmorra, $nome, $descricao, 1, $chefoes);
        return $minasMortas;
    }
    
    private function buildList ()
    {
        // masmorras // chefoes (ordem, next, hp, habilidades(dano, tipoDano)) // loot
        $this->listaMasmorras[] = $this->get1MinasMortas();
    }
}