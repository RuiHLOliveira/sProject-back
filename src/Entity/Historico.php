<?php

namespace App\Entity;

use App\Repository\HistoricoRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass=HistoricoRepository::class)
 */
class Historico implements JsonSerializable
{

    public const MODULO_TIPO_PROJETO = 1;
    public const MODULO_TIPO_TAREFA = 2;
    public const MODULOS_SUPORTADOS = [
        self::MODULO_TIPO_PROJETO,
        self::MODULO_TIPO_TAREFA,
    ];

    public function jsonSerialize()
    {
        // $array = parent::jsonSerialize();
        $array = [
            'id' => $this->getId(),
            'descricao' => $this->getDescricao(),
            'moduloId' => $this->getModuloId(),
            'moduloTipo' => $this->getModuloTipo(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
            // 'deletedAt' => $this->getDeletedAt(),
        ];
        return $array;
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $moduloId;

    /**
     * @ORM\Column(type="integer")
     */
    private $moduloTipo;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="text")
     */
    private $descricao;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="historicos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModuloId(): ?int
    {
        return $this->moduloId;
    }

    public function setModuloId(int $moduloId): self
    {
        $this->moduloId = $moduloId;

        return $this;
    }

    public function getModuloTipo(): ?int
    {
        return $this->moduloTipo;
    }

    public function setModuloTipo(int $moduloTipo): self
    {
        $this->moduloTipo = $moduloTipo;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): self
    {
        $this->descricao = $descricao;

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
