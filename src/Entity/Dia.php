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
        $array = [
            'id' => $this->getId(),
            'dataCompleta' => $this->getDataCompleta()->format('Y-m-d'),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
            'deletedAt' => $this->getDeletedAt(),
        ];

        
        if($this->serializarHoras) {
            $array['horas'] = $this->horasArray;
        }

        return $array;
    }

    public function serializarAtividades(){
        foreach ($this->horasArray as $key => $hora) {
            $this->horasArray[$key]->serializarAtividades();
        }
    }

    public function serializarHoras(){
        $this->serializarHoras = true;
        $this->horasToArray();
    }

    public function horasToArray() {
        $collection = $this->getHoras();
        $collection = $collection->toArray();
        // foreach ($collection as $key => $item) {
        //     $collection[$key] = $item;
        // }
        $this->horasArray = $collection;
    }
    
    private $serializarHoras;
    private $horasArray;

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

    /**
     * @ORM\OneToMany(targetEntity=Hora::class, mappedBy="dia")
     */
    private $horas;

    public function __construct()
    {
        $this->horas = new ArrayCollection();
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

    /**
     * @return Collection<int, Hora>
     */
    public function getHoras(): Collection
    {
        return $this->horas;
    }

    public function addHora(Hora $hora): self
    {
        if (!$this->horas->contains($hora)) {
            $this->horas[] = $hora;
            $hora->setDia($this);
        }

        return $this;
    }

    public function removeHora(Hora $hora): self
    {
        if ($this->horas->removeElement($hora)) {
            // set the owning side to null (unless already changed)
            if ($hora->getDia() === $this) {
                $hora->setDia(null);
            }
        }

        return $this;
    }
}
