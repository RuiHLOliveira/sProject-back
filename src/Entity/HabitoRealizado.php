<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\JsonSerializableEntity;
use App\Repository\HabitoRealizadoRepository;

/**
 * @ORM\Entity(repositoryClass=HabitoRealizadoRepository::class)
 */
class HabitoRealizado extends JsonSerializableEntity
{
    public function jsonSerialize()
    {
        $array = parent::jsonSerialize();
        // $array['habito'] = $this->getHabito(); //recursion
        $array['realizadoEm'] = $this->getRealizadoEm() != null ? $this->getRealizadoEm()->format('Y-m-d H:i:s') : null;
        $array['realizadoEmObj'] = $this->getRealizadoEm();
        return $array;
    }
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $realizadoEm;
    
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
     * @ORM\ManyToOne(targetEntity=Habito::class, inversedBy="habitoRealizados")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $habito;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="habitoRealizados")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $usuario;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRealizadoEm(): ?\DateTimeInterface
    {
        return $this->realizadoEm;
    }

    public function setRealizadoEm(\DateTimeInterface $realizadoEm): self
    {
        $this->realizadoEm = $realizadoEm;

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

    public function getHabito(): ?Habito
    {
        return $this->habito;
    }

    public function setHabito(?Habito $habito): self
    {
        $this->habito = $habito;

        return $this;
    }
}
