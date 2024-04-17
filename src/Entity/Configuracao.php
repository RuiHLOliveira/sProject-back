<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\JsonSerializableEntity;
use App\Repository\ConfiguracaoRepository;
use DomainException;

/**
 * @ORM\Entity(repositoryClass=ConfiguracaoRepository::class)
 */
class Configuracao extends JsonSerializableEntity
{
    
    public const CHAVE_EXIBIR_DIA_SEMANA_HABIT_TRACKER = 'exibir_dia_semana_habit_tracker';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $chave;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $valor;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    protected $updated_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    protected $deleted_at;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="configuracoes")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $usuario;

    public function jsonSerialize(): array
    {
        $array = parent::jsonSerialize();
        $array['chave'] = $this->getChave();
        $array['valor'] = $this->getValor();
        $array['usuario'] = $this->getUsuario()->getId();
        return $array;
    }

    public static function getChavesConfiguracoesConhecidas(): array
    {
        return [
            Configuracao::CHAVE_EXIBIR_DIA_SEMANA_HABIT_TRACKER
        ];
    }

    public static function getConfiguracoesPadrao(): array
    {
        return [
            (new Configuracao())->setChave(Configuracao::CHAVE_EXIBIR_DIA_SEMANA_HABIT_TRACKER)->setValor('0')
        ];
    }

    protected function validaChave(string $chave)
    {
        $configuracoes = self::getChavesConfiguracoesConhecidas();
        foreach ($configuracoes as $key => $conf) {
            if($conf == $chave) return true;
        }
        throw new DomainException('Chave inválida.');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChave(): ?string
    {
        return $this->chave;
    }

    public function setChave(string $chave): self
    {
        $this->validaChave($chave);
        $this->chave = $chave;

        return $this;
    }

    public function getValor(): ?string
    {
        return $this->valor;
    }

    public function setValor(string $valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeImmutable $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(?User $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }
}
