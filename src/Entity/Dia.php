<?php

namespace App\Entity;

use App\Repository\DiaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass=DiaRepository::class)
 */
class Dia implements JsonSerializable
{
    public function jsonSerialize()
    {
        // $array = parent::jsonSerialize();
        $array = [
            'id' => $this->getId(),
            'dataCompleta' => $this->getDataCompleta()->format('Y-m-d H:i:s'),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
            'deletedAt' => $this->getDeletedAt(),
        ];
        if($this->serializarAtividades) {
            $array['atividades'] = $this->atividadesArray;
        }
        return $array;
    }

    /**
     * Marca as atividades deste dia para serem serializadas
     */
    public function serializarAtividades(){
        $this->serializarAtividades = true;
        $this->atividadesToArray();
    }
    
    public function atividadesToArray() {
        $collection = $this->getAtividades();
        $collection = $collection->toArray();
        $this->atividadesArray = $collection;
    }
    
    private $serializarAtividades;
    private $atividadesArray;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dataCompleta;

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

    // /**
    //  * @ORM\OneToMany(targetEntity=Hora::class, mappedBy="dia")
    //  */
    // private $horas;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="dias")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\OneToMany(targetEntity=Atividade::class, mappedBy="dia")
     */
    private $atividades;
    
    public function __construct()
    {
        // $this->horas = new ArrayCollection();
        $this->atividades = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDataCompleta(): ?\DateTimeInterface
    {
        return $this->dataCompleta;
    }

    public function setDataCompleta(\DateTimeInterface $dataCompleta): self
    {
        $this->dataCompleta = $dataCompleta;

        return $this;
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

    /**
     * @return Collection<int, Atividade>
     */
    public function getAtividades(): Collection
    {
        return $this->atividades;
    }

    public function addAtividade(Atividade $atividade): self
    {
        if (!$this->atividades->contains($atividade)) {
            $this->atividades[] = $atividade;
            $atividade->setDia($this);
        }

        return $this;
    }

    public function removeAtividade(Atividade $atividade): self
    {
        if ($this->atividades->removeElement($atividade)) {
            // set the owning side to null (unless already changed)
            if ($atividade->getDia() === $this) {
                $atividade->setDia(null);
            }
        }

        return $this;
    }
}
