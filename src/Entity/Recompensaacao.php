<?php

namespace App\Entity;

use JsonSerializable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RecompensaacaoRepository;

/**
 * @ORM\Entity(repositoryClass=RecompensaacaoRepository::class)
 */
class Recompensaacao implements JsonSerializable
{
    public function jsonSerialize()
    {
        // $array = parent::jsonSerialize();
        $array = [
            'id' => $this->getId(),
            'quantidade' => $this->getQuantidade(),
            'tipoatividade' => $this->getTipoatividade(),
            'recompensa' => [
                'id' => $this->getRecompensa()->getId(),
                'nome' => $this->getRecompensa()->getNome(),
            ],
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
     * @ORM\Column(type="string", length=255)
     */
    private $tipoatividade;

    /**
     * @ORM\Column(type="smallint")
     */
    private $quantidade;

    /**
     * @ORM\ManyToOne(targetEntity=Recompensa::class, inversedBy="recompensaacoes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recompensa;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipoatividade(): ?string
    {
        return $this->tipoatividade;
    }

    public function setTipoatividade(string $tipoatividade): self
    {
        $this->tipoatividade = $tipoatividade;

        return $this;
    }

    public function getQuantidade(): ?int
    {
        return $this->quantidade;
    }

    public function setQuantidade(int $quantidade): self
    {
        $this->quantidade = $quantidade;

        return $this;
    }

    public function getRecompensa(): ?Recompensa
    {
        return $this->recompensa;
    }

    public function setRecompensa(?Recompensa $recompensa): self
    {
        $this->recompensa = $recompensa;

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
