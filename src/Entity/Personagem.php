<?php

namespace App\Entity;

use JsonSerializable;
use App\Entity\Recompensa;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PersonagemRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=PersonagemRepository::class)
 */
class Personagem implements JsonSerializable
{
    public function jsonSerialize()
    {
        // $array = parent::jsonSerialize();
        $array = [
            'id' => $this->getId(),
            'nome' => $this->getNome(),
            'nivel' => $this->getNivel(),
            'experiencia' => $this->getExperiencia(),
            'ouro' => $this->getOuro(),
            'createdat' => $this->getCreatedat(),
            'updatedat' => $this->getUpdatedat(),
            'deletedat' => $this->getDeletedat(),
        ];
        $array['personagemhistoricos'] = $this->personagemHistoricosToArray();
        $array['expProximoNivel'] = Recompensa::TABELA_EXPERIENCIA[$this->getNivel()];
        return $array;
    }

    private $personagemHistoricosArray;
    
    public function personagemHistoricosToArray() {
        $collection = $this->getPersonagemHistoricos();
        $collection = $collection->toArray();

        usort($collection, function ($a, $b) {
            return $b->getCreatedAt() <=> $a->getCreatedAt();
        });

        $this->personagemHistoricosArray = $collection;
        return $this->personagemHistoricosArray;
    }


    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdat;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedat;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $deletedat;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="personagens")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nome;

    /**
     * @ORM\Column(type="smallint", options={"default":1})
     */
    private $nivel;

    /**
     * @ORM\Column(type="bigint", options={"default":0})
     */
    private $experiencia;

    /**
     * @ORM\Column(type="bigint", options={"default":0})
     */
    private $ouro;

    /**
     * @ORM\OneToMany(targetEntity=PersonagemHistorico::class, mappedBy="personagem")
     */
    private $personagemHistoricos;

    public function __construct()
    {
        $this->personagemHistoricos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of createdat
     */ 
    public function getCreatedat()
    {
        return $this->createdat;
    }

    /**
     * Set the value of createdat
     *
     * @return  self
     */ 
    public function setCreatedat($createdat)
    {
        $this->createdat = $createdat;

        return $this;
    }

    /**
     * Get the value of updatedat
     */ 
    public function getUpdatedat()
    {
        return $this->updatedat;
    }

    /**
     * Set the value of updatedat
     *
     * @return  self
     */ 
    public function setUpdatedat($updatedat)
    {
        $this->updatedat = $updatedat;

        return $this;
    }

    /**
     * Get the value of deleted_at
     */
    public function getDeletedat()
    {
        return $this->deletedat;
    }

    /**
     * Set the value of deletedat
     */
    public function setDeletedat($deletedat): self
    {
        $this->deletedat = $deletedat;

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

    public function getNivel(): ?int
    {
        return $this->nivel;
    }

    public function setNivel(int $nivel): self
    {
        $this->nivel = $nivel;

        return $this;
    }

    public function getExperiencia(): ?string
    {
        return $this->experiencia;
    }

    public function setExperiencia(string $experiencia): self
    {
        $this->experiencia = $experiencia;

        return $this;
    }

    public function getOuro(): ?string
    {
        return $this->ouro;
    }

    public function setOuro(string $ouro): self
    {
        $this->ouro = $ouro;

        return $this;
    }

    /**
     * @return Collection<int, PersonagemHistorico>
     */
    public function getPersonagemHistoricos(): Collection
    {
        return $this->personagemHistoricos;
    }

    public function addPersonagemHistorico(PersonagemHistorico $personagemHistorico): self
    {
        if (!$this->personagemHistoricos->contains($personagemHistorico)) {
            $this->personagemHistoricos[] = $personagemHistorico;
            $personagemHistorico->setPersonagem($this);
        }

        return $this;
    }

    public function removePersonagemHistorico(PersonagemHistorico $personagemHistorico): self
    {
        if ($this->personagemHistoricos->removeElement($personagemHistorico)) {
            // set the owning side to null (unless already changed)
            if ($personagemHistorico->getPersonagem() === $this) {
                $personagemHistorico->setPersonagem(null);
            }
        }

        return $this;
    }
}
