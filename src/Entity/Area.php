<?php

namespace App\Entity;

use App\Entity\Projeto;
use App\Repository\AreaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass=AreaRepository::class)
 */
class Area implements JsonSerializable
{
    public function jsonSerialize()
    {
        // $array = parent::jsonSerialize();
        $array = [
            'id' => $this->getId(),
            'nome' => $this->getNome(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
            'deletedAt' => $this->getDeletedAt(),
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
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $deleted_at;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="projetos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nome;

    
    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
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

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

}
