<?php

namespace App\Entity;

use JsonSerializable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PersonagemHistoricoRepository;

/**
 * @ORM\Entity(repositoryClass=PersonagemHistoricoRepository::class)
 */
class PersonagemHistorico implements JsonSerializable
{
    public function jsonSerialize()
    {
        // $array = parent::jsonSerialize();
        $array = [
            'id' => $this->getId(),
            'texto' => $this->getTexto(),
            'tipo' => $this->getTipohistorico(),
            'dadosjson' => $this->getDadosjson(),
            'personagem' => $this->getPersonagem()->getId(),
            'createdat' => $this->getCreatedat(),
            'updatedat' => $this->getUpdatedat(),
            'deletedat' => $this->getDeletedat(),
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
     * @ORM\ManyToOne(targetEntity=Personagem::class, inversedBy="personagemHistoricos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $personagem;

    /**
     * @ORM\Column(type="smallint")
     */
    private $tipohistorico;

    /**
     * @ORM\Column(type="text")
     */
    private $texto;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dadosjson;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPersonagem(): ?Personagem
    {
        return $this->personagem;
    }

    public function setPersonagem(?Personagem $personagem): self
    {
        $this->personagem = $personagem;

        return $this;
    }

    public function getTipohistorico(): ?int
    {
        return $this->tipohistorico;
    }

    public function setTipohistorico(int $tipohistorico): self
    {
        $this->tipohistorico = $tipohistorico;

        return $this;
    }

    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(string $texto): self
    {
        $this->texto = $texto;

        return $this;
    }

    public function getDadosjson(): ?string
    {
        return $this->dadosjson;
    }

    public function setDadosjson(string $dadosjson): self
    {
        $this->dadosjson = $dadosjson;

        return $this;
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
}
